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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}