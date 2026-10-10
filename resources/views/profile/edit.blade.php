
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('profile.show') }}"
            class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Profil
        </a>

        <h4 class="fw-bold mt-3 mb-1">Edit Profil</h4>
        <p class="text-muted mb-0">
            Perbarui nama dan alamat email akun Anda.
        </p>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
        <form action="{{ route('profile.update') }}"
      method="POST"
      enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-4">
    <label class="form-label fw-semibold" for="profile_photo">
        Foto Profil
    </label>

    <div class="mb-3">
        @if ($user->profile_photo_path)
            <img
                src="{{ asset('storage/' . $user->profile_photo_path) }}"
                alt="Foto Profil"
                style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;"
            >
        @else
            <div
                class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center"
                style="width: 100px; height: 100px; font-size: 42px;"
            >
                <i class="bi bi-person-fill"></i>
            </div>
        @endif
    </div>

    <input
        type="file"
        name="profile_photo"
        id="profile_photo"
        class="form-control @error('profile_photo') is-invalid @enderror"
        accept=".jpg,.jpeg,.png,.webp"
    >

    @error('profile_photo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div class="form-text">
        Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
        Kosongkan jika tidak ingin mengganti foto.
    </div>
</div>

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">
                        Nama Lengkap <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}"
                        maxlength="100"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">
                        Alamat Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}"
                        maxlength="100"
                    >

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        Email boleh dikosongkan apabila tidak digunakan.
                    </div>
                </div>

                <div class="alert alert-light border small">
                    <i class="bi bi-shield-lock me-1"></i>
                    Username, role, status, dan kata sandi tidak dapat diubah di sini.
                    Untuk mengganti atau mereset kata sandi, hubungi Admin atau Super Admin.
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                    <a href="{{ route('profile.show') }}"
                        class="btn btn-outline-secondary">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2-circle me-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
