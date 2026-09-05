<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicYear;
use App\Models\RekapAbsensi;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;

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
        // Ambil semester sesuai tahun ajaran yang dipilih
        $semesters = Semester::with('academicYear')
            ->when($request->academic_year_id, function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id);
            })
            ->orderBy('id')
            ->get();

        // Cek kelas yang boleh diakses user
        $allowedClassIds = null;

        if ($user->isGuru()) {
            $employee = $user->employee;
        
            // Guru/Wali Kelas harus terhubung dengan data pegawai
            if (!$employee) {
                abort(403, 'Akun guru belum terhubung dengan data pegawai.');
            }
        
            // Guru hanya melihat kelas yang menjadi wali kelasnya
            $allowedClassIds = $employee->homeroomClasses()
                ->pluck('id')
                ->toArray();
        
        } elseif (
            !$user->isSuperAdmin()
            && !$user->isAdmin()
            && !$user->isKepalaSekolah()
        ) {
            abort(403, 'Anda tidak memiliki akses ke Rekap Absensi.');
        }

        // Data kelas untuk filter
        $classes = SchoolClass::with('academicYear')
            ->when($allowedClassIds !== null, function ($query) use ($allowedClassIds) {
                $query->whereIn('id', $allowedClassIds);
            })
            ->when($request->academic_year_id, function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id);
            })
            ->orderBy('name')
            ->get();

        // Query rekap absensi
        $rekapAbsensi = RekapAbsensi::with([
            'student',
            'schoolClass.academicYear',
            'semester.academicYear',
        ])
            // Filter berdasarkan kelas yang boleh diakses
            ->when($allowedClassIds !== null, function ($query) use ($allowedClassIds) {
                $query->whereIn('class_id', $allowedClassIds);
            })

            // Filter tahun ajaran melalui kelas
            ->when($request->academic_year_id, function ($query) use ($request) {
                $query->whereHas('schoolClass', function ($q) use ($request) {
                    $q->where('academic_year_id', $request->academic_year_id);
                });
            })

            // Filter kelas
            ->when($request->class_id, function ($query) use ($request) {
                $query->where('class_id', $request->class_id);
            })

            // Filter semester
            ->when($request->semester_id, function ($query) use ($request) {
                $query->where('semester_id', $request->semester_id);
            })

            ->whereHas('student', function ($query) {
                $query->where('status', 'Aktif');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('rekap-absensi.index', compact(
            'rekapAbsensi',
            'academicYears',
            'semesters',
            'classes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
    
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
    
        $allowedClassIds = null;
    
        if ($user->isGuru()) {
            $employee = $user->employee;
    
            if (!$employee) {
                abort(403, 'Akun guru belum terhubung dengan data pegawai.');
            }
    
            $allowedClassIds = $employee->homeroomClasses()
                ->pluck('id')
                ->toArray();
    
        } elseif (
            !$user->isSuperAdmin()
            && !$user->isAdmin()
            && !$user->isKepalaSekolah()
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
    
        $semesters = Semester::with('academicYear')
            ->orderBy('academic_year_id')
            ->orderBy('id')
            ->get();
    
        return view('rekap-absensi.create', compact(
            'academicYears',
            'classes',
            'semesters'
        ));
    }
    /**
 * Mengambil daftar siswa berdasarkan kelas.
 */
public function studentsByClass(SchoolClass $class)
{
    $user = Auth::user();

    // Guru/Wali Kelas hanya boleh mengakses kelas yang diwalikannya
    if ($user->isGuru()) {
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'Akun guru belum terhubung dengan data pegawai.');
        }

        $allowedClassIds = $employee->homeroomClasses()
            ->pluck('id')
            ->toArray();

        if (!in_array($class->id, $allowedClassIds)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }
    } elseif (
        !$user->isSuperAdmin()
        && !$user->isAdmin()
        && !$user->isKepalaSekolah()
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $user = Auth::user();

    // Hak akses
    if (
        !$user->isSuperAdmin()
        && !$user->isAdmin()
        && !$user->isGuru()
        && !$user->isKepalaSekolah()
    ) {
        abort(403, 'Anda tidak memiliki akses ke Rekap Absensi.');
    }

    $validated = $request->validate([
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

        'semester_id' => [
            'required',
            'integer',
            'exists:semesters,id',
        ],

        'sakit' => [
            'required',
            'integer',
            'min:0',
        ],

        'izin' => [
            'required',
            'integer',
            'min:0',
        ],

        'tanpa_keterangan' => [
            'required',
            'integer',
            'min:0',
        ],
    ]);

    // Ambil kelas yang dipilih.
    $schoolClass = SchoolClass::findOrFail($validated['class_id']);

    // Guru/Wali Kelas hanya boleh mengelola kelas yang menjadi tanggung jawabnya.
    if ($user->isGuru()) {
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'Akun guru belum terhubung dengan data pegawai.');
        }

        $isHomeroomTeacher = $employee->homeroomClasses()
            ->whereKey($schoolClass->id)
            ->exists();

        if (!$isHomeroomTeacher) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }
    }

    // Pastikan siswa benar-benar terdaftar di kelas yang dipilih.
    $studentBelongsToClass = $schoolClass->students()
        ->where('students.id', $validated['student_id'])
        ->wherePivot('status', 'Aktif')
        ->exists();

    if (!$studentBelongsToClass) {
        return back()
            ->withInput()
            ->withErrors([
                'student_id' => 'Siswa tidak terdaftar sebagai siswa aktif di kelas yang dipilih.',
            ]);
    }

    // Cegah rekap ganda untuk siswa, kelas, dan semester yang sama.
    $alreadyExists = RekapAbsensi::where('student_id', $validated['student_id'])
        ->where('class_id', $validated['class_id'])
        ->where('semester_id', $validated['semester_id'])
        ->exists();

    if ($alreadyExists) {
        return back()
            ->withInput()
            ->withErrors([
                'student_id' => 'Rekap absensi siswa untuk kelas dan semester tersebut sudah ada.',
            ]);
    }

    RekapAbsensi::create([
        'student_id' => $validated['student_id'],
        'class_id' => $validated['class_id'],
        'semester_id' => $validated['semester_id'],
        'sakit' => $validated['sakit'],
        'izin' => $validated['izin'],
        'tanpa_keterangan' => $validated['tanpa_keterangan'],
    ]);

    return redirect()
        ->route('rekap-absensi.index')
        ->with('success', 'Rekap absensi berhasil ditambahkan.');
}
    /**
     * Display the specified resource.
     */
    public function show(RekapAbsensi $rekapAbsensi)
{
    $user = Auth::user();

    // Guru/Wali Kelas hanya boleh melihat rekap dari kelas yang diwalikannya.
    if ($user->isGuru()) {
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'Akun guru belum terhubung dengan data pegawai.');
        }

        $hasAccess = $employee->homeroomClasses()
            ->whereKey($rekapAbsensi->class_id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke rekap absensi ini.');
        }
    } elseif (
        !$user->isSuperAdmin()
        && !$user->isAdmin()
        && !$user->isKepalaSekolah()
    ) {
        abort(403, 'Anda tidak memiliki akses ke Rekap Absensi.');
    }

    $rekapAbsensi->load([
        'student',
        'schoolClass.academicYear',
        'semester.academicYear',
    ]);

    return view('rekap-absensi.show', compact('rekapAbsensi'));
}
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RekapAbsensi $rekapAbsensi)
    {
        $user = Auth::user();
    
        // Hanya Super Admin dan Guru/Wali Kelas yang boleh mengedit.
        if (!$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit Rekap Absensi.');
        }
    
        // Guru/Wali Kelas hanya boleh mengedit rekap dari kelas yang diwalikannya.
        if ($user->isGuru()) {
            $employee = $user->employee;
    
            if (!$employee) {
                abort(403, 'Akun guru belum terhubung dengan data pegawai.');
            }
    
            $hasAccess = $employee->homeroomClasses()
                ->whereKey($rekapAbsensi->class_id)
                ->exists();
    
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke rekap absensi ini.');
            }
        }
    
        $rekapAbsensi->load([
            'student',
            'schoolClass.academicYear',
            'semester.academicYear',
        ]);
    
        return view('rekap-absensi.edit', compact('rekapAbsensi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RekapAbsensi $rekapAbsensi)
    {
        $user = Auth::user();
    
        // Hanya Super Admin dan Guru/Wali Kelas yang boleh memperbarui.
        if (!$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah Rekap Absensi.');
        }
    
        // Guru/Wali Kelas hanya boleh mengubah rekap dari kelas yang diwalikannya.
        if ($user->isGuru()) {
            $employee = $user->employee;
    
            if (!$employee) {
                abort(403, 'Akun guru belum terhubung dengan data pegawai.');
            }
    
            $hasAccess = $employee->homeroomClasses()
                ->whereKey($rekapAbsensi->class_id)
                ->exists();
    
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke rekap absensi ini.');
            }
        }
    
        $validated = $request->validate([
            'sakit' => [
                'required',
                'integer',
                'min:0',
            ],
    
            'izin' => [
                'required',
                'integer',
                'min:0',
            ],
    
            'tanpa_keterangan' => [
                'required',
                'integer',
                'min:0',
            ],
        ], [
            'sakit.required' => 'Jumlah sakit wajib diisi.',
            'izin.required' => 'Jumlah izin wajib diisi.',
            'tanpa_keterangan.required' => 'Jumlah tanpa keterangan wajib diisi.',
            '*.integer' => 'Jumlah ketidakhadiran harus berupa angka.',
            '*.min' => 'Jumlah ketidakhadiran tidak boleh kurang dari 0.',
        ]);
    
        $rekapAbsensi->update([
            'sakit' => $validated['sakit'],
            'izin' => $validated['izin'],
            'tanpa_keterangan' => $validated['tanpa_keterangan'],
        ]);
    
        return redirect()
            ->route('rekap-absensi.index')
            ->with('success', 'Rekap absensi berhasil diperbarui.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RekapAbsensi $rekapAbsensi)
    {
        $user = Auth::user();
    
        // Hanya Super Admin dan Guru/Wali Kelas yang boleh menghapus.
        if (!$user->isSuperAdmin() && !$user->isGuru()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus Rekap Absensi.');
        }
    
        // Guru/Wali Kelas hanya boleh menghapus rekap dari kelas yang diwalikannya.
        if ($user->isGuru()) {
            $employee = $user->employee;
    
            if (!$employee) {
                abort(403, 'Akun guru belum terhubung dengan data pegawai.');
            }
    
            $hasAccess = $employee->homeroomClasses()
                ->whereKey($rekapAbsensi->class_id)
                ->exists();
    
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke rekap absensi ini.');
            }
        }
    
        $rekapAbsensi->delete();
    
        return redirect()
            ->route('rekap-absensi.index')
            ->with('success', 'Rekap absensi berhasil dihapus.');
        } //
    } //