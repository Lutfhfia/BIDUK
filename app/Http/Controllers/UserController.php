<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
    
        if (!$currentUser || !$currentUser->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke Manajemen User.');
        }
    
        $users = User::with(['role', 'employee'])
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
    
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role_id, function ($query) use ($request) {
                $query->where('role_id', $request->role_id);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    
        $roles = Role::orderBy('name')->get();
    
        return view('users.index', compact('users', 'roles'));
    }
    public function create()
{
    $this->authorizeSuperAdmin();

    $roles = Role::orderBy('name')->get();

    $employees = Employee::whereDoesntHave('user')
        ->where('status', 'Aktif')
        ->orderBy('name')
        ->get();

    return view('users.create', compact('roles', 'employees'));
}    public function store(Request $request)
{
    $this->authorizeSuperAdmin();

    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'username' => [
            'required',
            'string',
            'max:255',
            'unique:users,username',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            'unique:users,email',
        ],

        'role_id' => [
            'required',
            'integer',
            'exists:roles,id',
        ],

        'employee_id' => [
            'nullable',
            'integer',
            'exists:employees,id',
            'unique:users,employee_id',
        ],

        'status' => [
            'required',
            'in:Aktif,Nonaktif',
        ],
    ], [
        'name.required' => 'Nama wajib diisi.',
        'username.required' => 'Username wajib diisi.',
        'username.unique' => 'Username sudah digunakan.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'role_id.required' => 'Role wajib dipilih.',
        'role_id.exists' => 'Role yang dipilih tidak valid.',
        'employee_id.exists' => 'Pegawai yang dipilih tidak valid.',
        'employee_id.unique' => 'Pegawai tersebut sudah memiliki akun.',
        'status.required' => 'Status wajib dipilih.',
    ]);

    // Buat password awal secara otomatis.
    $generatedPassword = Str::random(12);

    User::create([
        'name' => $validated['name'],
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => $generatedPassword,
        'role_id' => $validated['role_id'],
        'employee_id' => $validated['employee_id'] ?? null,
        'status' => $validated['status'],
    ]);

    return redirect()
        ->route('users.index')
        ->with('success', 'User berhasil dibuat.')
        ->with('generated_password', $generatedPassword);
}

    /**
     * Display the specified resource.
     */
    public function show(User $user)
{
    $this->authorizeSuperAdmin();

    $user->load(['role', 'employee']);

    return view('users.show', compact('user'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorizeSuperAdmin();
    
        // Pastikan relasi yang dibutuhkan tersedia.
        $user->load(['role', 'employee']);
    
        $roles = Role::orderBy('name')->get();
    
        // Pegawai yang belum memiliki akun
        // + pegawai yang sedang terhubung dengan user ini.
        $employees = Employee::where(function ($query) use ($user) {
            $query->whereDoesntHave('user')
                  ->orWhere('id', $user->employee_id);
        })
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();
    
        return view('users.edit', compact(
            'user',
            'roles',
            'employees'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
{
    $this->authorizeSuperAdmin();

    $currentUser = auth()->user();

    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'username' => [
            'required',
            'string',
            'max:255',
            'unique:users,username,' . $user->id,
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            'unique:users,email,' . $user->id,
        ],

        'role_id' => [
            'required',
            'integer',
            'exists:roles,id',
        ],

        'employee_id' => [
            'nullable',
            'integer',
            'exists:employees,id',
            'unique:users,employee_id,' . $user->id,
        ],

        'status' => [
            'required',
            'in:Aktif,Nonaktif',
        ],
    ], [
        'name.required' => 'Nama wajib diisi.',
        'username.required' => 'Username wajib diisi.',
        'username.unique' => 'Username sudah digunakan.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'role_id.required' => 'Role wajib dipilih.',
        'role_id.exists' => 'Role yang dipilih tidak valid.',
        'employee_id.exists' => 'Pegawai yang dipilih tidak valid.',
        'employee_id.unique' => 'Pegawai tersebut sudah memiliki akun lain.',
        'status.required' => 'Status wajib dipilih.',
    ]);

    // Akun yang sedang login tidak boleh dinonaktifkan sendiri.
    if ($user->id === $currentUser->id && $validated['status'] !== 'Aktif') {
        return back()
            ->withInput()
            ->withErrors([
                'status' => 'Akun yang sedang digunakan tidak dapat dinonaktifkan.',
            ]);
    }

    // Akun Super Admin tidak boleh diubah menjadi role lain.
    if ($user->isSuperAdmin() && $validated['role_id'] !== $user->role_id) {
        return back()
            ->withInput()
            ->withErrors([
                'role_id' => 'Role akun Super Admin tidak dapat diubah.',
            ]);
    }

    $user->update([
        'name' => $validated['name'],
        'username' => $validated['username'],
        'email' => $validated['email'],
        'role_id' => $validated['role_id'],
        'employee_id' => $validated['employee_id'] ?? null,
        'status' => $validated['status'],
    ]);

    return redirect()
        ->route('users.index')
        ->with('success', 'Data user berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorizeSuperAdmin();
    
        // Jangan izinkan Super Admin menghapus akun Super Admin.
        if ($user->isSuperAdmin()) {
            return redirect()
                ->route('users.index')
                ->withErrors([
                    'delete' => 'Akun Super Admin tidak dapat dihapus.',
                ]);
        }
    
        // Jangan izinkan user menghapus akun yang sedang digunakan.
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->withErrors([
                    'delete' => 'Akun yang sedang digunakan tidak dapat dihapus.',
                ]);
        }
    
        $user->delete();
    
        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
    private function authorizeSuperAdmin(): void
{
    $currentUser = auth()->user();

    if (!$currentUser || !$currentUser->isSuperAdmin()) {
        abort(403, 'Anda tidak memiliki akses ke Manajemen User.');
    }
}
}
