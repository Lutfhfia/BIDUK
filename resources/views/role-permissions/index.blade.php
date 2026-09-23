@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Hak Akses Role</h4>
            <p class="text-muted mb-0">
                Atur hak akses berdasarkan role pengguna.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @forelse($roles as $role)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $role->name }}</h5>

                        @if($role->description)
                            <small class="text-muted">
                                {{ $role->description }}
                            </small>
                        @endif
                    </div>

                    <span class="badge bg-primary">
                        {{ $role->permissions->count() }} Hak Akses
                    </span>
                </div>
            </div>

            <div class="card-body">
                @if($permissions->count())
                    <form action="{{ route('role-permissions.update', $role) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            @foreach($permissions as $permission)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check border rounded p-3 h-100">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            id="permission_{{ $role->id }}_{{ $permission->id }}"
                                            @checked($role->permissions->contains($permission->id))
                                        >

                                        <label
                                            class="form-check-label ms-2"
                                            for="permission_{{ $role->id }}_{{ $permission->id }}"
                                        >
                                            <div class="fw-semibold">
                                                {{ $permission->name }}
                                            </div>

                                            @if($permission->description)
                                                <small class="text-muted">
                                                    {{ $permission->description }}
                                                </small>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i>
                                Simpan Hak Akses
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-shield-lock fs-1 text-muted"></i>
                        <h6 class="mt-3">Belum ada permission</h6>
                        <p class="text-muted mb-0">
                            Data permission belum tersedia.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-person-badge fs-1 text-muted"></i>
                <h5 class="mt-3">Belum ada role</h5>
                <p class="text-muted mb-0">
                    Belum ada data role yang tersedia.
                </p>
            </div>
        </div>
    @endforelse

</div>
@endsection