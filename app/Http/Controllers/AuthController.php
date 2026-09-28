<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login pengguna.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'NIP atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari user berdasarkan username terlebih dahulu.
        $user = User::with(['employee', 'role'])
            ->where('username', $credentials['login'])
            ->first();

        // Jika username tidak ditemukan,
        // cari melalui NIP pegawai yang terhubung dengan user.
        if (!$user) {
            $employee = Employee::where(
                'nip',
                $credentials['login']
            )->first();

            if ($employee) {
                $user = $employee->user;
            }
        }

        // Akun tidak ditemukan.
        if (!$user) {
            return back()
                ->withErrors([
                    'login' => 'NIP atau username atau password yang dimasukkan salah.',
                ])
                ->withInput(
                    $request->only('login')
                );
        }

        // Pastikan akun user masih aktif.
        if ($user->status !== 'Aktif') {
            return back()
                ->withErrors([
                    'login' => 'Akun Anda sedang tidak aktif.',
                ])
                ->withInput(
                    $request->only('login')
                );
        }

        // Jika user terhubung dengan pegawai,
        // pastikan data pegawai juga masih aktif.
        if (
            $user->employee &&
            $user->employee->status !== 'Aktif'
        ) {
            return back()
                ->withErrors([
                    'login' => 'Data pegawai Anda sedang tidak aktif.',
                ])
                ->withInput(
                    $request->only('login')
                );
        }

        $remember = $request->boolean('remember');

        // Cek password.
        if (!Auth::attempt([
            'id' => $user->id,
            'password' => $credentials['password'],
        ], $remember)) {
            return back()
                ->withErrors([
                    'login' => 'NIP atau username atau password yang dimasukkan salah.',
                ])
                ->withInput(
                    $request->only('login')
                );
        }

        // Regenerasi session setelah login berhasil.
        $request->session()->regenerate();

        // Simpan waktu login terakhir.
        $user->update([
            'last_login_at' => now(),
        ]);

        // Catat aktivitas login.
        app(ActivityLogger::class)->log(
            'login',
            'authentication',
            'User berhasil login ke sistem',
            $user
        );

        return redirect()->route('dashboard');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            app(ActivityLogger::class)->log(
                'logout',
                'authentication',
                'User berhasil logout dari sistem',
                $user
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Mengarahkan pengguna ke WhatsApp Super Admin
     * untuk reset password.
     */
    public function forgotPassword()
    {
        $whatsapp = env('SUPER_ADMIN_WHATSAPP');

        $message = urlencode(
            "Halo Admin BIDUK, saya ingin meminta bantuan untuk reset password akun BIDUK saya.\n\n" .
            "Silakan masukkan data berikut:\n" .
            "Username: \n" .
            "Nama: \n" .
            "Jabatan/Role: \n"
        );

        return redirect(
            "https://wa.me/{$whatsapp}?text={$message}"
        );
    }
}