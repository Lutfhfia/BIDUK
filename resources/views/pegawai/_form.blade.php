@csrf

<div class="biduk-card">

    <div class="card-header">
        <i class="bi bi-person-badge me-2"></i>
        Informasi Pegawai
    </div>


    <div class="card-body">

        {{-- ================= IDENTITAS ================= --}}

        <h6 class="fw-bold mb-3">
            <i class="bi bi-person me-2 text-success"></i>
            Data Identitas
        </h6>

        <div class="row g-3 mb-4">

            <div class="col-md-6">

                <label class="form-label">
                    NIP
                </label>

                <input
                    type="text"
                    name="nip"
                    class="form-control @error('nip') is-invalid @enderror"
                    value="{{ old('nip', $employee->nip ?? '') }}"
                    placeholder="Masukkan NIP"
                >

                @error('nip')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-6">

                <label class="form-label">
                    NUPTK
                </label>

                <input
                    type="text"
                    name="nuptk"
                    class="form-control @error('nuptk') is-invalid @enderror"
                    value="{{ old('nuptk', $employee->nuptk ?? '') }}"
                    placeholder="Masukkan NUPTK"
                >

                @error('nuptk')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-8">

                <label class="form-label">
                    Nama Lengkap
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $employee->name ?? '') }}"
                    placeholder="Masukkan nama lengkap"
                    required
                >

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Jenis Kelamin
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="gender"
                    class="form-select @error('gender') is-invalid @enderror"
                    required
                >

                    <option value="">
                        Pilih...
                    </option>

                    <option
                        value="L"
                        @selected(old('gender', $employee->gender ?? '') === 'L')
                    >
                        Laki-laki
                    </option>

                    <option
                        value="P"
                        @selected(old('gender', $employee->gender ?? '') === 'P')
                    >
                        Perempuan
                    </option>

                </select>

                @error('gender')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-6">

                <label class="form-label">
                    Tempat Lahir
                </label>

                <input
                    type="text"
                    name="birth_place"
                    class="form-control"
                    value="{{ old('birth_place', $employee->birth_place ?? '') }}"
                    placeholder="Kota kelahiran"
                >

            </div>


            <div class="col-md-6">

                <label class="form-label">
                    Tanggal Lahir
                </label>

                <input
                    type="date"
                    name="birth_date"
                    class="form-control"
                    value="{{ old(
                        'birth_date',
                        isset($employee->birth_date)
                            ? $employee->birth_date->format('Y-m-d')
                            : ''
                    ) }}"
                >

            </div>

        </div>


        {{-- ================= KEPEGAWAIAN ================= --}}

        <h6 class="fw-bold mb-3">
            <i class="bi bi-briefcase me-2 text-success"></i>
            Data Kepegawaian
        </h6>

        <div class="row g-3 mb-4">

            <div class="col-md-4">

                <label class="form-label">
                    Jabatan
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="position"
                    class="form-select"
                    required
                >

                    <option value="">
                        Pilih jabatan...
                    </option>

                    <option
                        value="Guru"
                        @selected(old('position', $employee->position ?? '') === 'Guru')
                    >
                        Guru
                    </option>

                    <option
                        value="Kepala Sekolah"
                        @selected(old('position', $employee->position ?? '') === 'Kepala Sekolah')
                    >
                        Kepala Sekolah
                    </option>

                    <option
                        value="Tata Usaha"
                        @selected(old('position', $employee->position ?? '') === 'Tata Usaha')
                    >
                        Tata Usaha
                    </option>

                </select>

                <div class="form-text">
                    Mata pelajaran tidak dikelola pada halaman ini.
                </div>

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Status Kepegawaian
                    <span class="text-danger">*</span>
                </label>

                @php
                    $employmentStatus = old(
                        'employment_status',
                        isset($employee)
                            ? (
                                in_array($employee->employment_status, ['Honorer', 'Kontrak'])
                                    ? 'Non-ASN'
                                    : $employee->employment_status
                            )
                            : ''
                    );
                @endphp

                <select
                    name="employment_status"
                    class="form-select"
                    required
                >

                    <option value="">
                        Pilih status...
                    </option>

                    <option
                        value="PNS"
                        @selected($employmentStatus === 'PNS')
                    >
                        PNS
                    </option>

                    <option
                        value="PPPK"
                        @selected($employmentStatus === 'PPPK')
                    >
                        PPPK
                    </option>

                    <option
                        value="Non-ASN"
                        @selected($employmentStatus === 'Non-ASN')
                    >
                        Non-ASN
                    </option>

                </select>

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Status Pegawai
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="status"
                    class="form-select"
                    required
                >

                    <option
                        value="Aktif"
                        @selected(old('status', $employee->status ?? 'Aktif') === 'Aktif')
                    >
                        Aktif
                    </option>

                    <option
                        value="Nonaktif"
                        @selected(old('status', $employee->status ?? '') === 'Nonaktif')
                    >
                        Nonaktif
                    </option>

                </select>

            </div>

        </div>


        {{-- ================= PENUGASAN ================= --}}

        <h6 class="fw-bold mb-3">
            <i class="bi bi-building me-2 text-success"></i>
            Penugasan
        </h6>

        <div class="alert alert-light border mb-4">

            <i class="bi bi-info-circle me-2 text-success"></i>

            <strong>Wali Kelas:</strong>
            penugasan wali kelas dilakukan melalui menu
            <strong>Data Kelas</strong>.
            Guru yang sudah ditetapkan sebagai wali kelas akan otomatis
            tampil pada kolom <strong>Wali Kelas</strong> di halaman ini.

        </div>


        {{-- ================= KONTAK ================= --}}

        <h6 class="fw-bold mb-3">
            <i class="bi bi-telephone me-2 text-success"></i>
            Informasi Kontak
        </h6>

        <div class="row g-3 mb-4">

            <div class="col-md-6">

                <label class="form-label">
                    Nomor HP
                </label>

                <input
                    type="text"
                    name="phone"
                    class="form-control"
                    value="{{ old('phone', $employee->phone ?? '') }}"
                    placeholder="Masukkan nomor HP"
                >

            </div>


            <div class="col-md-6">

                <label class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $employee->email ?? '') }}"
                    placeholder="Masukkan email"
                >

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-12">

                <label class="form-label">
                    Alamat
                </label>

                <textarea
                    name="address"
                    rows="3"
                    class="form-control"
                    placeholder="Masukkan alamat lengkap"
                >{{ old('address', $employee->address ?? '') }}</textarea>

            </div>

        </div>


        {{-- ================= FOTO ================= --}}

        <h6 class="fw-bold mb-3">
            <i class="bi bi-image me-2 text-success"></i>
            Foto Pegawai
        </h6>

        <div class="mb-4">

            @if(isset($employee) && $employee->photo)

                <div class="mb-3">

                    <img
                        src="{{ asset('storage/' . $employee->photo) }}"
                        alt="Foto {{ $employee->name }}"
                        style="
                            width:100px;
                            height:100px;
                            object-fit:cover;
                            border-radius:12px;
                        "
                    >

                </div>

            @endif


            <input
                type="file"
                name="photo"
                class="form-control"
                accept=".jpg,.jpeg,.png"
            >

            <div class="form-text">
                JPG, JPEG, PNG. Maksimal 2 MB.
            </div>

        </div>


        {{-- ================= AKUN BIDUK ================= --}}

        <h6 class="fw-bold mb-3">
            <i class="bi bi-shield-lock me-2 text-success"></i>
            Akun Akses BIDUK
        </h6>

        <div class="alert alert-success">

            <i class="bi bi-check-circle me-2"></i>

            Akun login akan dibuat atau disinkronkan
            <strong>secara otomatis</strong> berdasarkan data pegawai.

            <br>

            <small>
                Username menggunakan NIP. Jika NIP kosong,
                username menggunakan NUPTK.
            </small>

        </div>


        {{-- ================= BUTTON ================= --}}

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">

            <a
                href="{{ route('pegawai.index') }}"
                class="btn btn-biduk-outline"
            >
                <i class="bi bi-x-lg me-1"></i>
                Batal
            </a>

            <button
                type="submit"
                class="btn btn-biduk-primary"
            >
                <i class="bi bi-check-lg me-1"></i>

                {{ isset($employee) ? 'Simpan Perubahan' : 'Simpan Pegawai' }}

            </button>

        </div>

    </div>

</div>