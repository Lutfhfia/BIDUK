<?php

namespace App\Http\Controllers;

use App\Models\Employee;
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
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari pegawai berdasarkan NIP.
        $employee = Employee::where('nip', $credentials['nip'])->first();

        if (!$employee) {
            return back()
                ->withErrors([
                    'nip' => 'NIP atau password yang dimasukkan salah.',
                ])
                ->withInput($request->only('nip'));
        }

        // Cari akun user yang terhubung dengan pegawai tersebut.
        $user = $employee->user;

        if (!$user) {
            return back()
                ->withErrors([
                    'nip' => 'Akun untuk NIP tersebut belum tersedia.',
                ])
                ->withInput($request->only('nip'));
        }

        // Pastikan akun user masih aktif.
        if ($user->status !== 'Aktif') {
            return back()
                ->withErrors([
                    'nip' => 'Akun Anda sedang tidak aktif.',
                ])
                ->withInput($request->only('nip'));
        }

        // Pastikan data pegawai juga masih aktif.
        if ($employee->status !== 'Aktif') {
            return back()
                ->withErrors([
                    'nip' => 'Data pegawai Anda sedang tidak aktif.',
                ])
                ->withInput($request->only('nip'));
        }

        $remember = $request->boolean('remember');

        // Cek password dan login user.
        if (!Auth::attempt([
            'id' => $user->id,
            'password' => $credentials['password'],
        ], $remember)) {
            return back()
                ->withErrors([
                    'nip' => 'NIP atau password yang dimasukkan salah.',
                ])
                ->withInput($request->only('nip'));
        }

        // Regenerasi session setelah login berhasil.
        $request->session()->regenerate();

        // Simpan waktu login terakhir.
        $user->update([
            'last_login_at' => now(),
        ]);

        // Untuk sementara arahkan ke dashboard.
        return redirect()->route('dashboard');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    /**
 * Mengarahkan pengguna ke WhatsApp Super Admin untuk reset password.
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

    return redirect("https://wa.me/{$whatsapp}?text={$message}");
}
}