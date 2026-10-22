<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsTemplateExport;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class StudentImportExportController extends Controller
{
    /**
     * Menampilkan form import siswa.
     * Akses route harus dibatasi dengan permission:students.import.
     */
    public function form(): View
    {
        return view('students.import');
    }

    /**
     * Import siswa. Pembatasan kelas Guru juga diterapkan di StudentsImport.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240',
            ],
        ]);

        try {
            $import = new StudentsImport();

            Excel::import($import, $request->file('file'));

            $message = sprintf(
                'Import selesai. Baru: %d, diperbarui: %d, dilewati/gagal: %d.',
                $import->getImportedCount(),
                $import->getUpdatedCount(),
                $import->getSkippedCount()
            );

            return redirect()
                ->route('students.index')
                ->with('success', $message)
                ->with('import_errors', $import->getErrors());
        } catch (HttpExceptionInterface $e) {
            // Jangan ubah penolakan 403 menjadi pesan error import biasa.
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'import' => 'Import gagal. Periksa format dan isi file, lalu coba kembali.',
                ]);
        }
    }
/**
 * Download template kosong untuk import siswa.
 */
public function downloadTemplate()
{
    return Excel::download(
        new StudentsTemplateExport(),
        'Template_Import_Data_Siswa.xlsx'
    );
}
    /**
     * Export siswa. StudentsExport membatasi hasil untuk kelas wali Guru.
     */
    public function exportExcel()
    {
        return Excel::download(
            new StudentsExport(),
            'BIDUK_Data_Siswa_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
