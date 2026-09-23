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
            403
        );
    }

    /**
     * Menampilkan halaman Hak Akses Role.
     */
    public function index()
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

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        $role->permissions()->sync(
            $request->permissions ?? []
        );

        return redirect()
            ->route('role-permissions.index')
            ->with(
                'success',
                'Hak akses role berhasil diperbarui.'
            );
    }
}