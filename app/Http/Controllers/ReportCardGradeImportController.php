<?php

namespace App\Http\Controllers;

use App\Services\ReportCardGradeImportContext;
use App\Services\ReportCardGradeImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportCardGradeImportController extends Controller
{
    public function downloadTemplate(
        Request $request,
        ReportCardGradeImportContext $context,
        ReportCardGradeImportService $importer
    ): StreamedResponse {
        $selection = $context->validateSelection($request->user(), $request->query());
        $spreadsheet = $importer->createTemplate($selection);
        $filename = 'Template_Import_Nilai_Rapot_'.Str::slug(
            str_replace('/', '-', $selection['academic_year']->name)
                .'_'.$selection['class']->name
                .'_'.$selection['semester']->name
        ).'.xlsx';

        return response()->streamDownload(
            fn () => (new Xlsx($spreadsheet))->save('php://output'),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function preview(
        Request $request,
        ReportCardGradeImportContext $context,
        ReportCardGradeImportService $importer
    ): RedirectResponse|View {
        $selection = $context->validateSelection($request->user(), $request->all());
        $file = $request->file('file');
        if (! $file || strtolower($file->getClientOriginalExtension()) !== 'xlsx') {
            return redirect()
                ->route('report-card-grades.index')
                ->withErrors([
                    'file' => 'File tidak sesuai dengan Template Import Nilai Rapot BIDUK. Silakan download template terbaru dan gunakan format .xlsx.',
                ]);
        }

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $path = $file->store('report-card-imports', 'local');
        $absolutePath = Storage::disk('local')->path($path);

        try {
            $preview = $importer->preview($absolutePath, $selection);
        } catch (SpreadsheetException) {
            Storage::disk('local')->delete($path);

            return redirect()
                ->route('report-card-grades.index')
                ->withErrors([
                    'file' => 'File tidak sesuai dengan Template Import Nilai Rapot BIDUK. Silakan download template terbaru dan gunakan format .xlsx.',
                ]);
        }

        $token = Str::random(48);
        $request->session()->put('report_card_imports.'.$token, [
            'path' => $path,
            'context' => [
                'academic_year_id' => $selection['academic_year']->id,
                'class_id' => $selection['class']->id,
                'semester_id' => $selection['semester']->id,
            ],
        ]);

        return view('admin.report-card-grades.import-preview', compact('preview', 'token'));
    }

    public function confirm(
        Request $request,
        ReportCardGradeImportContext $context,
        ReportCardGradeImportService $importer
    ): RedirectResponse|View {
        $validated = $request->validate([
            'token' => ['required', 'string', 'size:48'],
        ]);
        $sessionKey = 'report_card_imports.'.$validated['token'];
        $session = $request->session()->get($sessionKey);
        abort_unless(
            is_array($session)
                && is_string($session['path'] ?? null)
                && is_array($session['context'] ?? null)
                && Storage::disk('local')->exists($session['path']),
            404,
            'Sesi preview import tidak ditemukan. Unggah kembali file Excel.'
        );
        $selection = $context->validateSelection($request->user(), $session['context']);

        $preview = $importer->preview(Storage::disk('local')->path($session['path']), $selection);
        if (! $preview['can_import']) {
            return view('admin.report-card-grades.import-preview', [
                'preview' => $preview,
                'token' => $validated['token'],
            ]);
        }

        $importedRecords = $importer->import($preview);
        Storage::disk('local')->delete($session['path']);
        $request->session()->forget($sessionKey);

        return redirect()
            ->route('report-card-grades.index', [
                'academic_year_id' => $preview['academic_year_id'],
                'class_id' => $selection['class']->id,
            ])
            ->with('success', $importedRecords.' data rapor berhasil diimpor atau diperbarui.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'size:48'],
        ]);
        $sessionKey = 'report_card_imports.'.$validated['token'];
        $session = $request->session()->pull($sessionKey);

        if (is_array($session) && is_string($session['path'] ?? null)) {
            Storage::disk('local')->delete($session['path']);
        }

        return redirect()->route('report-card-grades.index');
    }
}
