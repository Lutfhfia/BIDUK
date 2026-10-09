<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class StudentImportExportController extends Controller
{
    public function form(): View
    {
        return view('students.import');
    }

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
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'import' => 'Import gagal: ' . $e->getMessage(),
                ]);
        }
    }

    public function exportExcel()
    {
        return Excel::download(
            new StudentsExport(),
            'BIDUK_Data_Siswa_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
