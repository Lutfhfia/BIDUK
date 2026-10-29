<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\RekapAbsensi;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\TeacherClassAccess;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekapAbsensiController extends Controller
{
    /**
     * Menampilkan daftar rekap absensi.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Tahun ajaran untuk filter
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $selectedAcademicYearId = $request->query->has('academic_year_id')
            ? ($request->input('academic_year_id') ?: null)
            : AcademicYear::getActive()?->id;

        $allowedClassIds = app(TeacherClassAccess::class)->classIds($user)?->all();

        if (
            ! $user->isGuru() &&
            ! $user->isSuperAdmin()
            && ! $user->isAdmin()
            && ! $user->isKepalaSekolah()
        ) {
            abort(403, 'Anda tidak memiliki akses ke Rekap Absensi.');
        }

        // Data kelas untuk filter
        $classes = SchoolClass::with('academicYear')
            ->when($allowedClassIds !== null, function ($query) use ($allowedClassIds) {
                $query->whereIn('id', $allowedClassIds);
            })
            ->when($selectedAcademicYearId, function ($query) use ($selectedAcademicYearId) {
                $query->where('academic_year_id', $selectedAcademicYearId);
            })
            ->orderBy('academic_year_id')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        // Query rekap absensi
        $rekapAbsensiQuery = RekapAbsensi::with([
            'student',
            'schoolClass.academicYear',
            'semester',
        ])
            // Filter berdasarkan kelas yang boleh diakses
            ->when($allowedClassIds !== null, function ($query) use ($allowedClassIds) {
                $query->whereIn('class_id', $allowedClassIds);
            })

            // Filter tahun ajaran melalui kelas
            ->when($selectedAcademicYearId, function ($query) use ($selectedAcademicYearId) {
                $query->whereHas('schoolClass', function ($q) use ($selectedAcademicYearId) {
                    $q->where('academic_year_id', $selectedAcademicYearId);
                });
            })

            // Filter kelas
            ->when($request->class_id, function ($query) use ($request) {
                $query->where('class_id', $request->class_id);
            })

            // Cari nama siswa, NIS, atau NISN
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where(function ($studentQuery) use ($search) {
                        $studentQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('nis', 'like', '%'.$search.'%')
                            ->orWhere('nisn', 'like', '%'.$search.'%');
                    });
                });
            })

            ->whereHas('student', function ($query) {
                $query->where('status', 'Aktif');
            });

        $rekapAbsensiSummary = (clone $rekapAbsensiQuery)
            ->selectRaw(
                'COALESCE(SUM(sakit), 0) as sakit, '
                .'COALESCE(SUM(izin), 0) as izin, '
                .'COALESCE(SUM(tanpa_keterangan), 0) as tanpa_keterangan'
            )
            ->first();

        $attendancePairs = (clone $rekapAbsensiQuery)
            ->select('student_id', 'class_id')
            ->groupBy('student_id', 'class_id')
            ->orderBy('student_id')
            ->paginate(10)
            ->withQueryString();

        $attendanceRecords = $attendancePairs->isEmpty()
            ? collect()
            : (clone $rekapAbsensiQuery)
                ->where(function ($query) use ($attendancePairs) {
                    foreach ($attendancePairs as $pair) {
                        $query->orWhere(function ($pairQuery) use ($pair) {
                            $pairQuery->where('student_id', $pair->student_id)
                                ->where('class_id', $pair->class_id);
                        });
                    }
                })
                ->get();

        $attendanceGroups = $attendancePairs->getCollection()->map(function ($pair) use ($attendanceRecords) {
            $records = $attendanceRecords
                ->where('student_id', $pair->student_id)
                ->where('class_id', $pair->class_id);
            $semesterRecords = ['ganjil' => null, 'genap' => null];

            foreach ($records as $record) {
                $semesterName = strtolower($record->semester?->name ?? '');

                if (str_contains($semesterName, 'ganjil')) {
                    $semesterRecords['ganjil'] = $record;
                } elseif (str_contains($semesterName, 'genap')) {
                    $semesterRecords['genap'] = $record;
                }
            }

            $actionRecord = $semesterRecords['ganjil']
                ?? $semesterRecords['genap']
                ?? $records->first();

            return (object) [
                'student' => $actionRecord?->student,
                'schoolClass' => $actionRecord?->schoolClass,
                'ganjil' => $semesterRecords['ganjil'],
                'genap' => $semesterRecords['genap'],
                'actionRecord' => $actionRecord,
            ];
        });

        $rekapAbsensi = new LengthAwarePaginator(
            $attendanceGroups,
            $attendancePairs->total(),
            $attendancePairs->perPage(),
            $attendancePairs->currentPage(),
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('rekap-absensi.index', compact(
            'rekapAbsensi',
            'rekapAbsensiSummary',
            'academicYears',
            'classes',
            'selectedAcademicYearId'
        ));
    }

    /**
     * Menampilkan form tambah rekap absensi.
     */
    public function create()
    {
        $user = Auth::user();

        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $allowedClassIds = app(TeacherClassAccess::class)->classIds($user)?->all();

        if (
            ! $user->isGuru() &&
            ! $user->isSuperAdmin()
            && ! $user->isAdmin()
            && ! $user->isKepalaSekolah()
        ) {
            abort(403, 'Anda tidak memiliki akses ke Rekap Absensi.');
        }

        $classes = SchoolClass::with('academicYear')
            ->when($allowedClassIds !== null, function ($query) use ($allowedClassIds) {
                $query->whereIn('id', $allowedClassIds);
            })
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        return view('rekap-absensi.create', compact(
            'academicYears',
            'classes'
        ));
    }

    /**
     * Mengambil daftar siswa berdasarkan kelas.
     */
    public function studentsByClass(SchoolClass $class)
    {
        $user = Auth::user();

        if ($user->isGuru()) {
            app(TeacherClassAccess::class)->assertCanAccessClass($user, $class);
        } elseif (
            ! $user->isSuperAdmin()
            && ! $user->isAdmin()
            && ! $user->isKepalaSekolah()
        ) {
            abort(403, 'Anda tidak memiliki akses ke data siswa.');
        }

        $students = $class->students()
            ->where('students.status', 'Aktif')
            ->orderBy('students.name')
            ->get([
                'students.id',
                'students.nisn',
                'students.name',
            ]);

        return response()->json($students);
    }

    /**
     * Menyimpan rekap absensi baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Hak akses
        if (
            ! $user->isSuperAdmin()
            && ! $user->isAdmin()
            && ! $user->isGuru()
            && ! $user->isKepalaSekolah()
        ) {
            abort(403, 'Anda tidak memiliki akses ke Rekap Absensi.');
        }

        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],

            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],

            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],

            'attendance.ganjil.sakit' => ['required', 'integer', 'min:0'],
            'attendance.ganjil.izin' => ['required', 'integer', 'min:0'],
            'attendance.ganjil.tanpa_keterangan' => ['required', 'integer', 'min:0'],
            'attendance.genap.sakit' => ['required', 'integer', 'min:0'],
            'attendance.genap.izin' => ['required', 'integer', 'min:0'],
            'attendance.genap.tanpa_keterangan' => ['required', 'integer', 'min:0'],
        ]);

        // Ambil kelas yang dipilih.
        $schoolClass = SchoolClass::findOrFail($validated['class_id']);
        app(TeacherClassAccess::class)->assertCanAccessClass($user, $schoolClass);

        if ((int) $validated['academic_year_id'] !== (int) $schoolClass->academic_year_id) {
            return back()
                ->withInput()
                ->withErrors([
                    'academic_year_id' => 'Tahun ajaran harus sama dengan tahun ajaran kelas.',
                ]);
        }

        $semesters = $this->semesterPair((int) $schoolClass->academic_year_id);

        if (! $semesters['ganjil'] || ! $semesters['genap']) {
            return back()
                ->withInput()
                ->withErrors([
                    'academic_year_id' => 'Tahun ajaran ini belum memiliki semester Ganjil dan Genap.',
                ]);
        }

        // Guru/Wali Kelas hanya boleh mengelola kelas yang menjadi tanggung jawabnya.
        // Pastikan siswa benar-benar terdaftar di kelas yang dipilih.
        $studentBelongsToClass = $schoolClass->students()
            ->where('students.id', $validated['student_id'])
            ->wherePivot('status', 'Aktif')
            ->exists();

        if (! $studentBelongsToClass) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_id' => 'Siswa tidak terdaftar sebagai siswa aktif di kelas yang dipilih.',
                ]);
        }

        $semesterIds = collect($semesters)->map(fn (Semester $semester) => $semester->id);

        // Cegah pasangan rekap tahunan dibuat dua kali.
        $alreadyExists = RekapAbsensi::where('student_id', $validated['student_id'])
            ->where('class_id', $validated['class_id'])
            ->whereIn('semester_id', $semesterIds)
            ->exists();

        if ($alreadyExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_id' => 'Rekap absensi siswa untuk kelas dan tahun ajaran tersebut sudah ada. Silakan edit data yang tersedia.',
                ]);
        }

        $attendanceValues = $this->normalizeAttendanceValues($validated['attendance']);
        $rekapAbsensi = DB::transaction(function () use (
            $validated,
            $semesters,
            $attendanceValues
        ) {
            $records = [];

            foreach (['ganjil', 'genap'] as $semesterKey) {
                $records[$semesterKey] = RekapAbsensi::create([
                    'student_id' => $validated['student_id'],
                    'class_id' => $validated['class_id'],
                    'semester_id' => $semesters[$semesterKey]->id,
                    ...$attendanceValues[$semesterKey],
                ]);
            }

            return $records['ganjil'];
        });

        // Ambil data relasi untuk deskripsi log.
        $student = $rekapAbsensi->student;
        $className = $schoolClass->name;

        // Catat aktivitas penambahan rekap absensi.
        app(ActivityLogger::class)->log(
            'created',
            'attendance',
            'Menambahkan rekap absensi siswa: '
                .($student?->name ?? 'Siswa')
                .' - Kelas '
                .$className
                .' (Semester Ganjil dan Genap)',
            $rekapAbsensi->fresh(),
            null,
            $attendanceValues
        );

        return redirect()
            ->route('rekap-absensi.index')
            ->with('success', 'Rekap absensi berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail rekap absensi.
     */
    public function show(RekapAbsensi $rekapAbsensi)
    {
        $user = Auth::user();

        // Guru/Wali Kelas hanya boleh melihat rekap dari kelas yang diwalikannya.
        if ($user->isGuru()) {
            app(TeacherClassAccess::class)->assertCanAccessClass($user, $rekapAbsensi->schoolClass);
        } elseif (
            ! $user->isSuperAdmin()
            && ! $user->isAdmin()
            && ! $user->isKepalaSekolah()
        ) {
            abort(403, 'Anda tidak memiliki akses ke Rekap Absensi.');
        }

        $rekapAbsensi->load([
            'student',
            'schoolClass.academicYear',
            'semester.academicYear',
        ]);

        $semesters = $this->semesterPair((int) $rekapAbsensi->schoolClass->academic_year_id);
        $attendanceBySemester = $this->attendanceBySemester($rekapAbsensi, $semesters);

        return view('rekap-absensi.show', compact(
            'rekapAbsensi',
            'attendanceBySemester'
        ));
    }

    /**
     * Menampilkan form edit rekap absensi.
     */
    public function edit(RekapAbsensi $rekapAbsensi)
    {
        $user = Auth::user();

        // Hanya Super Admin dan Guru/Wali Kelas yang boleh mengedit.
        if (! $user->isSuperAdmin() && ! $user->isGuru()) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit Rekap Absensi.');
        }

        // Guru/Wali Kelas hanya boleh mengedit rekap dari kelas yang diwalikannya.
        if ($user->isGuru()) {
            app(TeacherClassAccess::class)->assertCanAccessClass($user, $rekapAbsensi->schoolClass);
        }

        $rekapAbsensi->load([
            'student',
            'schoolClass.academicYear',
            'semester.academicYear',
        ]);

        $semesters = $this->semesterPair((int) $rekapAbsensi->schoolClass->academic_year_id);
        $attendanceBySemester = $this->attendanceBySemester($rekapAbsensi, $semesters);

        return view('rekap-absensi.edit', compact(
            'rekapAbsensi',
            'attendanceBySemester'
        ));
    }

    /**
     * Memperbarui rekap absensi.
     */
    public function update(Request $request, RekapAbsensi $rekapAbsensi)
    {
        $user = Auth::user();

        // Hanya Super Admin dan Guru/Wali Kelas yang boleh memperbarui.
        if (! $user->isSuperAdmin() && ! $user->isGuru()) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah Rekap Absensi.');
        }

        // Guru/Wali Kelas hanya boleh mengubah rekap dari kelas yang diwalikannya.
        if ($user->isGuru()) {
            app(TeacherClassAccess::class)->assertCanAccessClass($user, $rekapAbsensi->schoolClass);
        }

        $validated = $request->validate([
            'attendance.ganjil.sakit' => ['required', 'integer', 'min:0'],
            'attendance.ganjil.izin' => ['required', 'integer', 'min:0'],
            'attendance.ganjil.tanpa_keterangan' => ['required', 'integer', 'min:0'],
            'attendance.genap.sakit' => ['required', 'integer', 'min:0'],
            'attendance.genap.izin' => ['required', 'integer', 'min:0'],
            'attendance.genap.tanpa_keterangan' => ['required', 'integer', 'min:0'],
        ], [
            'attendance.*.*.required' => 'Semua jumlah ketidakhadiran wajib diisi.',
            'attendance.*.*.integer' => 'Jumlah ketidakhadiran harus berupa angka.',
            'attendance.*.*.min' => 'Jumlah ketidakhadiran tidak boleh kurang dari 0.',
        ]);

        $semesters = $this->semesterPair((int) $rekapAbsensi->schoolClass->academic_year_id);

        if (! $semesters['ganjil'] || ! $semesters['genap']) {
            return back()
                ->withInput()
                ->withErrors([
                    'attendance' => 'Tahun ajaran ini belum memiliki semester Ganjil dan Genap.',
                ]);
        }

        $newAttendanceValues = $this->normalizeAttendanceValues($validated['attendance']);
        $oldAttendanceValues = $this->attendanceBySemester($rekapAbsensi, $semesters);

        DB::transaction(function () use (
            $rekapAbsensi,
            $semesters,
            $newAttendanceValues,
            &$oldAttendanceValues
        ) {
            foreach (['ganjil', 'genap'] as $semesterKey) {
                $record = RekapAbsensi::firstOrNew([
                    'student_id' => $rekapAbsensi->student_id,
                    'class_id' => $rekapAbsensi->class_id,
                    'semester_id' => $semesters[$semesterKey]->id,
                ]);

                if (! $record->exists) {
                    $oldAttendanceValues[$semesterKey] = null;
                }

                $record->fill($newAttendanceValues[$semesterKey])->save();
            }
        });

        $changedOldValues = [];
        $changedNewValues = [];

        foreach (['ganjil', 'genap'] as $semesterKey) {
            $oldValues = $oldAttendanceValues[$semesterKey];

            if ($oldValues === null || $oldValues !== $newAttendanceValues[$semesterKey]) {
                $changedOldValues[$semesterKey] = $oldValues;
                $changedNewValues[$semesterKey] = $newAttendanceValues[$semesterKey];
            }
        }

        if ($changedNewValues !== []) {
            $rekapAbsensi->loadMissing('student', 'schoolClass');

            app(ActivityLogger::class)->log(
                'updated',
                'attendance',
                'Mengubah rekap absensi siswa: '
                    .($rekapAbsensi->student?->name ?? 'Siswa')
                    .' - Kelas '
                    .($rekapAbsensi->schoolClass?->name ?? '-')
                    .' (Semester Ganjil dan Genap)',
                $rekapAbsensi->fresh(),
                $changedOldValues,
                $changedNewValues
            );
        }

        return redirect()
            ->route('rekap-absensi.index')
            ->with('success', 'Rekap absensi berhasil diperbarui.');
    }

    /**
     * Menghapus rekap absensi.
     */
    public function destroy(RekapAbsensi $rekapAbsensi)
    {
        $user = Auth::user();

        // Hanya Super Admin dan Guru/Wali Kelas yang boleh menghapus.
        if (! $user->isSuperAdmin() && ! $user->isGuru()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus Rekap Absensi.');
        }

        // Guru/Wali Kelas hanya boleh menghapus rekap dari kelas yang diwalikannya.
        if ($user->isGuru()) {
            app(TeacherClassAccess::class)->assertCanAccessClass($user, $rekapAbsensi->schoolClass);
        }

        // Muat relasi sebelum record dihapus.
        $rekapAbsensi->loadMissing(
            'student',
            'schoolClass'
        );

        $studentName = $rekapAbsensi->student?->name ?? 'Siswa';
        $className = $rekapAbsensi->schoolClass?->name ?? '-';

        $semesters = $this->semesterPair((int) $rekapAbsensi->schoolClass->academic_year_id);
        $semesterIds = collect($semesters)->filter()->map(
            fn (Semester $semester) => $semester->id
        );
        $records = RekapAbsensi::query()
            ->where('student_id', $rekapAbsensi->student_id)
            ->where('class_id', $rekapAbsensi->class_id)
            ->where(function ($query) use ($semesterIds, $rekapAbsensi) {
                $query->whereIn('semester_id', $semesterIds)
                    ->orWhereKey($rekapAbsensi->id);
            })
            ->get();

        if ($records->isEmpty()) {
            $records = collect([$rekapAbsensi]);
        }

        $oldAttendanceValues = $records->mapWithKeys(function (RekapAbsensi $record) {
            $semesterName = strtolower($record->semester?->name ?? '');
            $semesterKey = str_contains($semesterName, 'genap') ? 'genap' : 'ganjil';

            return [$semesterKey => [
                'sakit' => $record->sakit,
                'izin' => $record->izin,
                'tanpa_keterangan' => $record->tanpa_keterangan,
            ]];
        })->all();

        DB::transaction(function () use ($records) {
            foreach ($records as $record) {
                $record->delete();
            }
        });

        // Catat aktivitas penghapusan.
        app(ActivityLogger::class)->log(
            'deleted',
            'attendance',
            'Menghapus rekap absensi siswa: '
                .$studentName
                .' - Kelas '
                .$className
                .' (Semester Ganjil dan Genap)',
            $rekapAbsensi,
            $oldAttendanceValues,
            null
        );

        return redirect()
            ->route('rekap-absensi.index')
            ->with('success', 'Rekap absensi berhasil dihapus.');
    }

    /**
     * Mendapatkan semester Ganjil dan Genap untuk satu tahun ajaran.
     *
     * @return array{ganjil: Semester|null, genap: Semester|null}
     */
    private function semesterPair(int $academicYearId): array
    {
        $pair = ['ganjil' => null, 'genap' => null];

        foreach (Semester::where('academic_year_id', $academicYearId)->orderBy('id')->get() as $semester) {
            $name = strtolower(trim($semester->name));

            if ($pair['ganjil'] === null && str_contains($name, 'ganjil')) {
                $pair['ganjil'] = $semester;
            } elseif ($pair['genap'] === null && str_contains($name, 'genap')) {
                $pair['genap'] = $semester;
            }
        }

        return $pair;
    }

    /**
     * Memetakan nilai absensi ke masing-masing semester.
     *
     * @param  array{ganjil: Semester|null, genap: Semester|null}  $semesters
     * @return array{ganjil: array|null, genap: array|null}
     */
    private function attendanceBySemester(RekapAbsensi $rekapAbsensi, array $semesters): array
    {
        $records = RekapAbsensi::query()
            ->where('student_id', $rekapAbsensi->student_id)
            ->where('class_id', $rekapAbsensi->class_id)
            ->whereIn('semester_id', collect($semesters)->filter()->map(
                fn (Semester $semester) => $semester->id
            ))
            ->get()
            ->keyBy('semester_id');

        $attendance = [];

        foreach (['ganjil', 'genap'] as $semesterKey) {
            $record = $semesters[$semesterKey]
                ? $records->get($semesters[$semesterKey]->id)
                : null;

            $attendance[$semesterKey] = $record ? [
                'sakit' => $record->sakit,
                'izin' => $record->izin,
                'tanpa_keterangan' => $record->tanpa_keterangan,
            ] : null;
        }

        return $attendance;
    }

    /**
     * Cast validated input values before persistence and change comparison.
     */
    private function normalizeAttendanceValues(array $attendance): array
    {
        foreach (['ganjil', 'genap'] as $semesterKey) {
            foreach (['sakit', 'izin', 'tanpa_keterangan'] as $field) {
                $attendance[$semesterKey][$field] = (int) $attendance[$semesterKey][$field];
            }
        }

        return $attendance;
    }
}
