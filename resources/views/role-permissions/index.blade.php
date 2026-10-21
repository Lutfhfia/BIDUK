
@extends('layouts.app')

@section('title', 'Hak Akses Role')

@section('content')
@php
    $roleLabel = function ($name) {
        $name = strtolower($name);

        if (str_contains($name, 'super')) {
            return 'Super Admin';
        }

        if (str_contains($name, 'kepala')) {
            return 'Kepala Sekolah';
        }

        if (str_contains($name, 'guru') || str_contains($name, 'wali')) {
            return 'Guru / Wali Kelas';
        }

        if (str_contains($name, 'admin')) {
            return 'Admin / Tata Usaha';
        }

        return $name;
    };

    $orderedRoles = $roles->sortBy(function ($role) use ($roleLabel) {
        return match ($roleLabel($role->name)) {
            'Super Admin' => 1,
            'Admin / Tata Usaha' => 2,
            'Guru / Wali Kelas' => 3,
            'Kepala Sekolah' => 4,
            default => 5,
        };
    })->values();

    $selectedRole = $roles->firstWhere('id', (int) request('role'))
        ?? $orderedRoles->first();

    /*
     * Kelompok disesuaikan dengan fitur pada sidebar BIDUK.
     * Semester dan Berita Sekolah tidak ditampilkan sebagai
     * kelompok terpisah di halaman ini.
     */
    $permissionGroups = [
        'Dashboard' => [
            'icon' => 'bi-grid-1x2',
            'prefixes' => ['dashboard'],
        ],
        'Data Siswa' => [
            'icon' => 'bi-people',
            'prefixes' => ['students'],
        ],
        'Data Pegawai' => [
            'icon' => 'bi-person-vcard',
            'prefixes' => ['employees'],
        ],
        'Data Kelas' => [
            'icon' => 'bi-building',
            'prefixes' => ['classes'],
        ],
        'Tahun Ajaran' => [
            'icon' => 'bi-calendar3',
            'prefixes' => ['academic_years'],
        ],
        'Mata Pelajaran' => [
            'icon' => 'bi-journal-bookmark',
            'prefixes' => ['subjects'],
        ],
        'Nilai Rapot' => [
            'icon' => 'bi-card-checklist',
            'prefixes' => ['grades'],
        ],
        'Buku Induk' => [
            'icon' => 'bi-book',
            'prefixes' => ['student_records'],
        ],
        'Rekap Absensi' => [
            'icon' => 'bi-clipboard2-check',
            'prefixes' => ['attendance'],
        ],
        'Rekap Rapot' => [
            'icon' => 'bi-file-earmark-text',
            'prefixes' => ['report_cards'],
        ],
        'Rekap Prestasi' => [
            'icon' => 'bi-trophy',
            'prefixes' => ['achievements'],
        ],
        'Manajemen User' => [
            'icon' => 'bi-person-gear',
            'prefixes' => ['users'],
        ],
        'Hak Akses Role' => [
            'icon' => 'bi-shield-lock',
            'prefixes' => ['roles'],
        ],
        'Profil Sekolah' => [
            'icon' => 'bi-info-circle',
            'prefixes' => ['school_profile'],
        ],
        'Log Aktivitas' => [
            'icon' => 'bi-clock-history',
            'prefixes' => ['activity_logs'],
        ],
        'Laporan Umum' => [
            'icon' => 'bi-file-earmark-bar-graph',
            'prefixes' => ['reports'],
        ],
    ];

    $permissionLabels = [
        'dashboard.view' => 'Lihat Dashboard',

        'students.view' => 'Lihat Data Siswa',
        'students.create' => 'Tambah Data Siswa',
        'students.edit' => 'Ubah Data Siswa',
        'students.delete' => 'Hapus Data Siswa',
        'students.import' => 'Import Data Siswa',
        'students.export' => 'Export Data Siswa',

        'employees.view' => 'Lihat Data Pegawai',
        'employees.create' => 'Tambah Data Pegawai',
        'employees.edit' => 'Ubah Data Pegawai',
        'employees.delete' => 'Hapus Data Pegawai',
        'employees.import' => 'Import Data Pegawai',
        'employees.export' => 'Export Data Pegawai',

        'classes.view' => 'Lihat Data Kelas',
        'classes.create' => 'Tambah Data Kelas',
        'classes.edit' => 'Ubah Data Kelas',
        'classes.delete' => 'Hapus Data Kelas',

        'academic_years.view' => 'Lihat Tahun Ajaran',
        'academic_years.create' => 'Tambah Tahun Ajaran',
        'academic_years.edit' => 'Ubah Tahun Ajaran',
        'academic_years.delete' => 'Hapus Tahun Ajaran',

        'subjects.view' => 'Lihat Mata Pelajaran',
        'subjects.create' => 'Tambah Mata Pelajaran',
        'subjects.edit' => 'Ubah Mata Pelajaran',
        'subjects.delete' => 'Hapus Mata Pelajaran',

        'grades.view' => 'Lihat Nilai Rapot',
        'grades.create' => 'Tambah Nilai Rapot',
        'grades.edit' => 'Ubah Nilai Rapot',
        'grades.delete' => 'Hapus Nilai Rapot',
        'grades.import' => 'Import Nilai Rapot',

        'student_records.view' => 'Lihat Buku Induk',
        'student_records.print' => 'Cetak Buku Induk',
        'student_records.export' => 'Unduh Buku Induk',

        'attendance.view' => 'Lihat Rekap Absensi',
        'attendance.create' => 'Tambah Rekap Absensi',
        'attendance.edit' => 'Ubah Rekap Absensi',
        'attendance.delete' => 'Hapus Rekap Absensi',
        'attendance.export' => 'Unduh Rekap Absensi',

        'report_cards.view' => 'Lihat Rekap Rapot',
        'report_cards.print' => 'Cetak Rekap Rapot',
        'report_cards.export' => 'Unduh Rekap Rapot',

        'achievements.view' => 'Lihat Rekap Prestasi',
        'achievements.create' => 'Tambah Data Prestasi',
        'achievements.edit' => 'Ubah Data Prestasi',
        'achievements.delete' => 'Hapus Data Prestasi',
        'achievements.print' => 'Cetak Rekap Prestasi',
        'achievements.export' => 'Unduh Rekap Prestasi',

        'users.view' => 'Lihat Pengguna',
        'users.create' => 'Tambah Pengguna',
        'users.edit' => 'Ubah Pengguna',
        'users.delete' => 'Hapus Pengguna',
        'users.manage' => 'Kelola Manajemen User',

        'roles.manage' => 'Kelola Hak Akses Role',

        'school_profile.view' => 'Lihat Profil Sekolah',
        'school_profile.manage' => 'Ubah Profil Sekolah',

        'activity_logs.view' => 'Lihat Log Aktivitas',

        'reports.view' => 'Lihat Laporan',
    ];
@endphp

<div class="container-fluid role-permission-page">

    {{-- HEADER --}}
    <div class="page-heading mb-4">
        <div class="page-heading-icon">
            <i class="bi bi-shield-lock"></i>
        </div>

        <div>
            <h4 class="fw-bold mb-1">Hak Akses Role</h4>
            <p class="text-muted mb-0">
                Atur menu dan tindakan yang boleh dilakukan setiap role.
            </p>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Tutup">
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Perubahan gagal disimpan.</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PILIH ROLE --}}
    <div class="section-card mb-4">
        <div class="section-card-heading">
            <div>
                <h6 class="fw-bold mb-1">Pilih Role</h6>
                <small class="text-muted">
                    Pilih role yang ingin diatur hak aksesnya.
                </small>
            </div>
        </div>

        <div class="section-card-body">
            <div class="row g-3">
                @foreach ($orderedRoles as $role)
                    @php
                        $label = $roleLabel($role->name);

                        $active = $selectedRole
                            && (int) $selectedRole->id === (int) $role->id;
                    @endphp

                    <div class="col-12 col-sm-6 col-xl-3">
                        <a
                            href="{{ route('role-permissions.index', ['role' => $role->id]) }}"
                            class="role-choice {{ $active ? 'active' : '' }}"
                            @if ($active) aria-current="page" @endif
                        >
                            <span class="role-choice-icon">
                                @switch($label)
                                    @case('Super Admin')
                                        <i class="bi bi-shield-fill-check"></i>
                                        @break

                                    @case('Admin / Tata Usaha')
                                        <i class="bi bi-person-gear"></i>
                                        @break

                                    @case('Guru / Wali Kelas')
                                        <i class="bi bi-mortarboard-fill"></i>
                                        @break

                                    @case('Kepala Sekolah')
                                        <i class="bi bi-person-badge-fill"></i>
                                        @break

                                    @default
                                        <i class="bi bi-person-fill"></i>
                                @endswitch
                            </span>

                            <span class="role-choice-text">
                                <strong>{{ $label }}</strong>
                                <small>
                                    {{ $active ? 'Role dipilih' : 'Atur hak akses' }}
                                </small>
                            </span>

                            @if ($active)
                                <i class="bi bi-check-circle-fill role-active-check"></i>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ($selectedRole)
        {{-- FORM PERMISSION --}}
        <div class="section-card">

            <div class="section-card-heading permission-toolbar">
                <div>
                    <h5 class="fw-bold mb-1">
                        Hak Akses {{ $roleLabel($selectedRole->name) }}
                    </h5>

                    <small class="text-muted">
                        Centang tindakan yang diizinkan untuk role ini.
                    </small>
                </div>

                <div class="permission-tools">
                    <div class="input-group permission-search">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="search"
                            id="permissionSearch"
                            class="form-control"
                            placeholder="Cari fitur atau izin..."
                            autocomplete="off"
                        >
                    </div>

                    <label class="select-all-control">
                        <input
                            type="checkbox"
                            id="selectAllPermissions"
                            class="form-check-input"
                        >
                        <span>Pilih semua yang tampil</span>
                    </label>
                </div>
            </div>

            @if ($roleLabel($selectedRole->name) === 'Guru / Wali Kelas')
                <div class="role-notice">
                    <i class="bi bi-info-circle-fill"></i>

                    <div>
                        <strong>Catatan untuk Guru / Wali Kelas</strong>
                        <p class="mb-0">
                            Hak akses tindakan tidak memperluas cakupan data.
                            Data siswa, nilai, dan laporan tetap harus dibatasi
                            ke kelas wali pada tahun ajaran aktif melalui backend.
                        </p>
                    </div>
                </div>
            @elseif ($roleLabel($selectedRole->name) === 'Kepala Sekolah')
                <div class="role-notice">
                    <i class="bi bi-info-circle-fill"></i>

                    <div>
                        <strong>Mode monitoring</strong>
                        <p class="mb-0">
                            Kepala Sekolah dapat melihat dan mengunduh atau
                            mencetak laporan sesuai izin, tanpa mengubah data.
                        </p>
                    </div>
                </div>
            @endif

            <form
                action="{{ route('role-permissions.update', $selectedRole) }}"
                method="POST"
                id="permissionForm"
            >
                @csrf
                @method('PUT')

                <div class="section-card-body">

                    @foreach ($permissionGroups as $groupName => $groupConfig)
                        @php
                            $prefixes = $groupConfig['prefixes'];

                            $groupPermissions = $permissions
                                ->filter(function ($permission) use ($prefixes) {
                                    foreach ($prefixes as $prefix) {
                                        if (str_starts_with(
                                            $permission->name,
                                            $prefix . '.'
                                        )) {
                                            return true;
                                        }
                                    }

                                    return false;
                                })
                                ->sortBy('name')
                                ->values();
                        @endphp

                        @if ($groupPermissions->isNotEmpty())
                            <section
                                class="permission-group"
                                data-permission-group
                            >
                                <div class="permission-group-heading">
                                    <span class="permission-group-icon">
                                        <i class="bi {{ $groupConfig['icon'] }}"></i>
                                    </span>

                                    <div>
                                        <h6 class="fw-bold mb-0">
                                            {{ $groupName }}
                                        </h6>

                                        <small class="text-muted">
                                            {{ $groupPermissions->count() }}
                                            pilihan izin
                                        </small>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    @foreach ($groupPermissions as $permission)
                                        @php
                                            $label = $permissionLabels[$permission->name]
                                                ?? $permission->description
                                                ?? $permission->name;

                                            $checked = $selectedRole->permissions
                                                ->contains('id', $permission->id);
                                        @endphp

                                        <div
                                            class="col-12 col-md-6 col-xl-4 permission-column"
                                            data-permission-column
                                        >
                                            <label
                                                class="permission-option {{ $checked ? 'checked' : '' }}"
                                                for="permission_{{ $permission->id }}"
                                                data-permission-item
                                                data-search="{{ strtolower($groupName . ' ' . $label . ' ' . $permission->name) }}"
                                            >
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input permission-checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    id="permission_{{ $permission->id }}"
                                                    @checked($checked)
                                                >

                                                <span class="permission-option-text">
                                                    {{ $label }}
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    @endforeach

                    <div
                        id="permissionEmptyState"
                        class="permission-empty-state d-none"
                    >
                        <i class="bi bi-search"></i>
                        <p class="mb-0">
                            Tidak ada permission yang cocok dengan pencarian.
                        </p>
                    </div>
                </div>

                <div class="section-card-footer">
                    <a
                        href="{{ route('role-permissions.index', ['role' => $selectedRole->id]) }}"
                        class="btn btn-outline-secondary"
                    >
                        Batal
                    </a>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="alert alert-warning">
            Belum ada role yang tersedia.
        </div>
    @endif
</div>

<style>
    .role-permission-page {
        padding-bottom: 28px;
        color: #243044;
    }

    .page-heading {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .page-heading-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #198754;
        background: #eaf7ef;
        font-size: 25px;
        flex-shrink: 0;
    }

    .section-card {
        overflow: hidden;
        border: 1px solid #e5e9ed;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(25, 40, 60, .04);
        margin-bottom: 20px;
    }

    .section-card-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 18px 20px;
        border-bottom: 1px solid #edf0f2;
    }

    .section-card-body {
        padding: 20px;
    }

    .role-choice {
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
        min-height: 76px;
        padding: 14px;
        border: 1px solid #dfe5e9;
        border-radius: 10px;
        color: #243044;
        text-decoration: none;
        background: #fff;
        transition: background .15s, border-color .15s;
    }

    .role-choice:hover {
        border-color: #198754;
        background: #f7fcf8;
        color: #146c43;
    }

    .role-choice.active {
        border: 1.5px solid #198754;
        background: #eaf7ef;
        color: #146c43;
    }

    .role-choice-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        border-radius: 9px;
        background: #e5f4eb;
        color: #198754;
        font-size: 20px;
    }

    .role-choice-text {
        display: flex;
        flex-direction: column;
        gap: 3px;
        flex: 1;
        min-width: 0;
    }

    .role-choice-text strong {
        font-size: 13px;
    }

    .role-choice-text small {
        font-size: 11px;
        color: #6c757d;
    }

    .role-active-check {
        color: #198754;
        font-size: 17px;
    }

    .permission-toolbar {
        align-items: center;
        flex-wrap: wrap;
    }

    .permission-tools {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 12px;
    }

    .permission-search {
        width: min(330px, 100%);
    }

    .permission-search .input-group-text {
        background: #fff;
        border-color: #dfe5e9;
        color: #6c757d;
    }

    .permission-search .form-control {
        border-color: #dfe5e9;
        min-height: 40px;
    }

    .select-all-control {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border: 1px solid #cfe4d6;
        border-radius: 8px;
        background: #f7fcf8;
        color: #146c43;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }

    .select-all-control .form-check-input {
        margin: 0;
    }

    .role-notice {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 20px;
        border-bottom: 1px solid #edf0f2;
        background: #f8faf9;
        color: #46544c;
        font-size: 13px;
    }

    .role-notice > i {
        color: #198754;
        font-size: 17px;
        margin-top: 1px;
    }

    .role-notice p {
        margin-top: 4px;
        line-height: 1.6;
    }

    .permission-group {
        margin-bottom: 28px;
    }

    .permission-group:last-child {
        margin-bottom: 0;
    }

    .permission-group-heading {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e8ecef;
    }

    .permission-group-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 9px;
        background: #eaf7ef;
        color: #198754;
        font-size: 18px;
        flex-shrink: 0;
    }

    .permission-option {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 49px;
        height: 100%;
        padding: 12px;
        border: 1px solid #dfe5e9;
        border-radius: 8px;
        cursor: pointer;
        background: #fff;
        transition: background .15s, border-color .15s;
    }

    .permission-option:hover {
        border-color: #8ec5a2;
        background: #f8fcf9;
    }

    .permission-option.checked {
        border-color: #8ec5a2;
        background: #eff9f2;
    }

    .permission-option .form-check-input {
        float: none;
        margin: 0;
        width: 17px;
        height: 17px;
        flex-shrink: 0;
        cursor: pointer;
    }

    .permission-option-text {
        font-size: 12px;
        font-weight: 500;
        line-height: 1.45;
    }

    .section-card-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 15px 20px;
        border-top: 1px solid #edf0f2;
        background: #fff;
    }

    .section-card-footer .btn {
        min-height: 40px;
        padding: 9px 16px;
        font-size: 13px;
    }

    .permission-empty-state {
        padding: 32px 15px;
        text-align: center;
        color: #6c757d;
    }

    .permission-empty-state i {
        display: block;
        margin-bottom: 10px;
        font-size: 26px;
    }

    @media (max-width: 767px) {
        .section-card-heading {
            align-items: stretch;
            flex-direction: column;
            padding: 15px;
        }

        .section-card-body {
            padding: 15px;
        }

        .permission-tools {
            align-items: stretch;
            flex-direction: column;
        }

        .permission-search {
            width: 100%;
        }

        .select-all-control {
            align-self: flex-start;
        }

        .permission-group-heading {
            margin-bottom: 10px;
        }

        .role-choice {
            padding: 11px;
        }

        .section-card-footer {
            padding: 13px 15px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('permissionSearch');
    const selectAll = document.getElementById('selectAllPermissions');
    const emptyState = document.getElementById('permissionEmptyState');

    const checkboxes = Array.from(
        document.querySelectorAll('.permission-checkbox')
    );

    const groups = Array.from(
        document.querySelectorAll('[data-permission-group]')
    );

    function updateOptionStyle(checkbox) {
        const option = checkbox.closest('.permission-option');

        if (option) {
            option.classList.toggle('checked', checkbox.checked);
        }
    }

    function visibleCheckboxes() {
        return checkboxes.filter(function (checkbox) {
            const column = checkbox.closest('[data-permission-column]');

            return column && !column.classList.contains('d-none');
        });
    }

    function updateSelectAllState() {
        const visible = visibleCheckboxes();
        const checkedCount = visible.filter(
            checkbox => checkbox.checked
        ).length;

        selectAll.checked =
            visible.length > 0 && checkedCount === visible.length;

        selectAll.indeterminate =
            checkedCount > 0 && checkedCount < visible.length;
    }

    function filterPermissions() {
        const query = search.value.trim().toLowerCase();
        let totalVisible = 0;

        groups.forEach(function (group) {
            const columns = Array.from(
                group.querySelectorAll('[data-permission-column]')
            );

            let groupVisible = 0;

            columns.forEach(function (column) {
                const item = column.querySelector('[data-permission-item]');
                const searchableText = item?.dataset.search || '';
                const matches = searchableText.includes(query);

                column.classList.toggle('d-none', !matches);

                if (matches) {
                    groupVisible++;
                    totalVisible++;
                }
            });

            group.classList.toggle('d-none', groupVisible === 0);
        });

        emptyState.classList.toggle('d-none', totalVisible !== 0);

        updateSelectAllState();
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            updateOptionStyle(checkbox);
            updateSelectAllState();
        });

        updateOptionStyle(checkbox);
    });

    selectAll.addEventListener('change', function () {
        visibleCheckboxes().forEach(function (checkbox) {
            checkbox.checked = selectAll.checked;
            updateOptionStyle(checkbox);
        });

        updateSelectAllState();
    });

    search.addEventListener('input', filterPermissions);

    updateSelectAllState();
});
</script>
@endsection
