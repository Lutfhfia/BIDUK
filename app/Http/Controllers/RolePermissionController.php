<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    /**
     * Menampilkan halaman Hak Akses Role.
     */
    public function index()
    {
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get();

        $permissions = Permission::orderBy('name')->get();

        return view('role-permissions.index', compact('roles', 'permissions'));
    }

    /**
     * Menyimpan perubahan hak akses role.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()
            ->route('role-permissions.index')
            ->with('success', 'Hak akses role berhasil diperbarui.');
    }
}