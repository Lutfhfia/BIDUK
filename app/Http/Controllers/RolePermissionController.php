<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolePermissionController extends Controller
{
    /**
     * Memastikan hanya Super Admin
     * yang dapat mengelola hak akses role.
     */
    private function authorizeAccess(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->isSuperAdmin(),
            403,
            'Anda tidak memiliki akses untuk mengelola hak akses role.'
        );
    }

    /**
     * Menampilkan halaman Hak Akses Role.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get();

        $permissions = Permission::orderBy('name')->get();

        return view(
            'role-permissions.index',
            compact('roles', 'permissions')
        );
    }

    /**
     * Menyimpan perubahan hak akses role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorizeAccess();

        /*
         * Pastikan permission yang dikirim memang ada
         * di database.
         */
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        /*
         * Jika tidak ada permission yang dicentang,
         * gunakan array kosong.
         */
        $permissionIds = $validated['permissions'] ?? [];

        /*
         * Sinkronisasi permission role.
         *
         * Permission lama yang tidak dicentang akan dihapus,
         * sedangkan permission baru akan ditambahkan.
         */
        $role->permissions()->sync($permissionIds);

        return redirect()
            ->route('role-permissions.index', [
                'role' => $role->id,
            ])
            ->with(
                'success',
                'Hak akses role berhasil diperbarui.'
            );
    }
}