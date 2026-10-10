<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil akun yang sedang login.
     */
    public function show(Request $request): View
    {
        $user = $request->user()->load(['role', 'employee']);

        return view('profile.show', compact('user'));
    }

    /**
     * Menampilkan formulir edit profil.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['role', 'employee']);

        return view('profile.edit', compact('user'));
    }

    /**
     * Memperbarui nama, email, dan foto profil sendiri.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        // Validasi email agar tidak digunakan akun atau pegawai lain.
        $emailRules = [
            'nullable',
            'email',
            'max:100',
            Rule::unique('users', 'email')->ignore($user->id),
        ];

        if ($employee) {
            $emailRules[] = Rule::unique('employees', 'email')
                ->ignore($employee->id);
        } else {
            $emailRules[] = Rule::unique('employees', 'email');
        }

        // Validasi data profil dan foto.
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => $emailRules,
            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 100 karakter.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.unique' => 'Email sudah digunakan akun atau pegawai lain.',
            'profile_photo.image' => 'File harus berupa gambar.',
            'profile_photo.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'profile_photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $oldPhotoPath = $user->profile_photo_path;
        $newPhotoPath = null;

        // Unggah foto baru jika pengguna memilih file.
        if ($request->hasFile('profile_photo')) {
            $newPhotoPath = $request->file('profile_photo')
                ->store('profile-photos', 'public');

            if ($newPhotoPath === false) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'profile_photo' =>
                            'Foto gagal diunggah. Silakan coba lagi.',
                    ]);
            }
        }

        try {
            DB::transaction(function () use (
                $user,
                $employee,
                $validated,
                $newPhotoPath
            ): void {
                $name = trim($validated['name']);
                $email = $validated['email'] ?? null;

                // Perbarui akun pengguna.
                $user->name = $name;
                $user->email = $email;

                // Pertahankan foto lama jika tidak mengunggah foto baru.
                if ($newPhotoPath !== null) {
                    $user->profile_photo_path = $newPhotoPath;
                }

                $user->save();

                // Sinkronkan nama dan email dengan data pegawai.
                if ($employee) {
                    $employee->name = $name;
                    $employee->email = $email;
                    $employee->save();
                }
            });
        } catch (Throwable $exception) {
            // Hapus foto baru jika penyimpanan database gagal.
            if ($newPhotoPath !== null) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            throw $exception;
        }

        // Hapus foto lama setelah data baru berhasil disimpan.
        if (
            $newPhotoPath !== null
            && $oldPhotoPath !== null
            && $oldPhotoPath !== $newPhotoPath
        ) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
