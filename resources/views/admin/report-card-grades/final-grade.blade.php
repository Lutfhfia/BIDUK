@extends('layouts.app')

@section('title', 'Nilai Akhir / Ijazah')

@section('content')

<div class="container-fluid py-4">

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-award me-2"></i>
                Nilai Akhir / Ijazah
            </h4>

            <p class="text-muted mb-0">
                Kelola nilai akhir dan data ijazah peserta didik kelas 6.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a
                href="{{ route('final-grades.print', [$class->id, $student->id]) }}"
                class="btn btn-outline-danger"
                target="_blank"
            >
                <i class="bi bi-printer me-1"></i>
                Cetak PDF
            </a>

            <a
                href="{{ $returnUrl ?? route('report-card-grades.edit', [
                    'class' => $class->id,
                    'student' => $student->id
                ]) }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

    </div>


    {{-- =====================================================
        ALERT
    ====================================================== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <div class="fw-semibold mb-2">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Terdapat kesalahan:
            </div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- =====================================================
        PERINGATAN DATA TIDAK LENGKAP
    ====================================================== --}}
    @if(!empty($missingData))
        <div class="alert alert-warning">
            <div class="fw-semibold mb-2">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Beberapa nilai rapor belum tersedia:
            </div>
            <ul class="mb-0 small">
                @foreach($missingData as $subjectId => $missings)
                    @php
                        $subjectName = $ijazahMapping->firstWhere('subject_id', $subjectId)['ijazah_name'] ?? 'Mapel #'.$subjectId;
                    @endphp
                    <li>
                        <strong>{{ $subjectName }}:</strong>
                        {{ implode(', ', $missings) }}
                    </li>
                @endforeach
            </ul>
            <p class="mb-0 mt-2 small text-muted">
                Nilai Sekolah dihitung dari data yang tersedia. Lengkapi nilai rapor yang kurang untuk hasil akurat.
            </p>
        </div>
    @endif


    {{-- =====================================================
        FORM
    ====================================================== --}}
    <form
        method="POST"
        action="{{ route('final-grades.update', [
            'class'   => $class->id,
            'student' => $student->id
        ]) }}"
    >
        @csrf
        @method('PUT')

        <input type="hidden" name="return_url" value="{{ $returnUrl ?? '' }}">


        {{-- =================================================
            A. IDENTITAS PESERTA DIDIK
        ================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    A. Identitas Peserta Didik
                </h5>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label small text-muted">Nama Peserta Didik</label>
                        <input type="text" class="form-control" value="{{ $student->name }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">NISN</label>
                        <input type="text" class="form-control" value="{{ $student->nisn ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">NIS</label>
                        <input type="text" class="form-control" value="{{ $student->nis ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-muted">Nama Sekolah</label>
                        <input type="text" class="form-control" value="SDN 204 Palembang" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">Kelas</label>
                        <input type="text" class="form-control" value="{{ $class->name }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">Tahun Pelajaran</label>
                        <input type="text" class="form-control" value="{{ $class->academicYear->name ?? '-' }}" readonly>
                    </div>

                </div>
            </div>

        </div>


        {{-- =================================================
            B. TABEL NILAI AKHIR / IJAZAH
        ================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    B. Nilai Akhir / Ijazah
                </h5>
                <p class="text-muted small mb-0 mt-1">
                    Nilai Sekolah dihitung otomatis dari rata-rata rapor kelas 5 &amp; 6.
                    Input nilai praktik dan tertulis secara manual.
                </p>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 align-middle" id="tabel-nilai-akhir">

                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 40px;">No</th>
                                <th style="min-width: 200px;" class="text-start">Mata Pelajaran</th>
                                <th style="width: 90px;">
                                    Nilai<br>Sekolah
                                    <br><small class="text-muted fw-normal">(otomatis)</small>
                                </th>
                                <th style="min-width: 180px;" class="text-start">
                                    Capaian Pembelajaran<br>
                                    <small class="text-muted fw-normal">(Nilai Sekolah)</small>
                                </th>
                                <th style="width: 90px;">
                                    Nilai<br>Praktik
                                    <br><small class="text-muted fw-normal">(input)</small>
                                </th>
                                <th style="width: 90px;">
                                    Nilai<br>Tertulis
                                    <br><small class="text-muted fw-normal">(input)</small>
                                </th>
                                <th style="width: 90px;">
                                    Nilai Ujian<br>Sekolah
                                    <br><small class="text-muted fw-normal">(otomatis)</small>
                                </th>
                                <th style="width: 90px;">
                                    Nilai<br>Akhir
                                    <br><small class="text-muted fw-normal">(otomatis)</small>
                                </th>
                                <th style="min-width: 180px;" class="text-start">
                                    Capaian Pembelajaran<br>
                                    <small class="text-muted fw-normal">(Nilai Akhir)</small>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $no = 0;
                                $currentGroup = null;
                            @endphp

                            @foreach($ijazahMapping as $item)
                                @php
                                    // Tampilkan header kelompok jika berubah
                                    $showGroupHeader = $item['group_name'] && $item['group_name'] !== $currentGroup;
                                    if ($showGroupHeader) {
                                        $currentGroup = $item['group_name'];
                                    }

                                    $no++;
                                    $sid = $item['subject_id'];
                                    $stored = $sid ? ($storedGrades[$sid] ?? null) : null;
                                    $schoolScore = $sid ? ($schoolScores[$sid] ?? null) : null;
                                    $hasMissing = $sid && isset($missingData[$sid]);
                                @endphp

                                {{-- Header kelompok (Muatan Lokal) --}}
                                @if($showGroupHeader)
                                    <tr class="table-secondary">
                                        <td colspan="9" class="fw-semibold ps-3">
                                            {{ $currentGroup }}
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    {{-- No --}}
                                    <td class="text-center">{{ $no }}</td>

                                    {{-- Mata Pelajaran --}}
                                    <td class="{{ $item['group_name'] ? 'ps-4' : '' }}">
                                        {{ $item['ijazah_name'] }}
                                        @if(!$sid)
                                            <br><small class="text-danger">
                                                <i class="bi bi-exclamation-circle"></i>
                                                Mapel tidak ditemukan di database
                                            </small>
                                        @endif
                                    </td>

                                    {{-- Nilai Sekolah (otomatis) --}}
                                    <td class="text-center">
                                        @if($sid)
                                            <span
                                                class="fw-semibold {{ $hasMissing ? 'text-warning' : '' }}"
                                                title="{{ $hasMissing ? 'Data tidak lengkap: ' . implode(', ', $missingData[$sid]) : 'Rata-rata 4 semester' }}"
                                            >
                                                {{ $schoolScore !== null ? number_format($schoolScore, 2) : '-' }}
                                            </span>
                                            @if($hasMissing)
                                                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 0.75rem;"></i>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Capaian Pembelajaran Nilai Sekolah --}}
                                    <td>
                                        @if($sid)
                                            <textarea
                                                name="school_learning_outcomes[{{ $sid }}]"
                                                class="form-control form-control-sm"
                                                rows="2"
                                                placeholder="Capaian pembelajaran..."
                                            >{{ old("school_learning_outcomes.{$sid}", $stored->school_learning_outcome ?? '') }}</textarea>
                                        @endif
                                    </td>

                                    {{-- Nilai Praktik --}}
                                    <td>
                                        @if($sid)
                                            <input
                                                type="number"
                                                name="practical_scores[{{ $sid }}]"
                                                class="form-control form-control-sm text-center input-praktik"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                data-subject="{{ $sid }}"
                                                value="{{ old("practical_scores.{$sid}", $stored->practical_score ?? '') }}"
                                                placeholder="0-100"
                                            >
                                        @endif
                                    </td>

                                    {{-- Nilai Tertulis --}}
                                    <td>
                                        @if($sid)
                                            <input
                                                type="number"
                                                name="written_scores[{{ $sid }}]"
                                                class="form-control form-control-sm text-center input-tertulis"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                data-subject="{{ $sid }}"
                                                value="{{ old("written_scores.{$sid}", $stored->written_score ?? '') }}"
                                                placeholder="0-100"
                                            >
                                        @endif
                                    </td>

                                    {{-- Nilai Ujian Sekolah (otomatis) --}}
                                    <td class="text-center">
                                        @if($sid)
                                            <span class="fw-semibold nilai-ujian" id="exam-{{ $sid }}">
                                                {{ $stored && $stored->exam_score !== null ? number_format($stored->exam_score, 2) : '-' }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Nilai Akhir (otomatis) --}}
                                    <td class="text-center">
                                        @if($sid)
                                            <span class="fw-bold text-primary nilai-akhir" id="final-{{ $sid }}"
                                                data-school="{{ $schoolScore }}"
                                            >
                                                {{ $stored && $stored->final_score !== null ? number_format($stored->final_score, 2) : '-' }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Capaian Pembelajaran Nilai Akhir --}}
                                    <td>
                                        @if($sid)
                                            <textarea
                                                name="final_learning_outcomes[{{ $sid }}]"
                                                class="form-control form-control-sm"
                                                rows="2"
                                                placeholder="Capaian pembelajaran..."
                                            >{{ old("final_learning_outcomes.{$sid}", $stored->final_learning_outcome ?? '') }}</textarea>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Baris Rata-rata --}}
                            <tr class="table-info fw-bold">
                                <td colspan="2" class="text-center">Rata-rata</td>
                                <td class="text-center" id="avg-school">
                                    @php
                                        $validSchool = collect($schoolScores)->filter(fn($v) => $v !== null);
                                        $avgSchool = $validSchool->isNotEmpty() ? $validSchool->avg() : null;
                                    @endphp
                                    {{ $avgSchool !== null ? number_format($avgSchool, 2) : '-' }}
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center" id="avg-exam">
                                    @php
                                        $validExam = $storedGrades->filter(fn($g) => $g->exam_score !== null);
                                        $avgExam = $validExam->isNotEmpty() ? $validExam->avg('exam_score') : null;
                                    @endphp
                                    {{ $avgExam !== null ? number_format($avgExam, 2) : '-' }}
                                </td>
                                <td class="text-center text-primary" id="avg-final">
                                    @php
                                        $validFinal = $storedGrades->filter(fn($g) => $g->final_score !== null);
                                        $avgFinal = $validFinal->isNotEmpty() ? $validFinal->avg('final_score') : null;
                                    @endphp
                                    {{ $avgFinal !== null ? number_format($avgFinal, 2) : '-' }}
                                </td>
                                <td></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>


        {{-- =================================================
            C. KETIDAKHADIRAN
        ================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    C. Ketidakhadiran
                </h5>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Sakit</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $attendance ? $attendance->sakit . ' hari' : 'Data belum tersedia' }}"
                            readonly
                        >
                        @if(!$attendance)
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Rekap absensi belum diisi
                            </small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Izin</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $attendance ? $attendance->izin . ' hari' : 'Data belum tersedia' }}"
                            readonly
                        >
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Tanpa Keterangan</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $attendance ? $attendance->tanpa_keterangan . ' hari' : 'Data belum tersedia' }}"
                            readonly
                        >
                    </div>
                </div>
            </div>

        </div>


        {{-- =================================================
            D. STATUS KELULUSAN
        ================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    D. Status Kelulusan
                </h5>
                <p class="text-muted small mb-0 mt-1">
                    Status ditetapkan manual oleh pihak sekolah yang berwenang.
                </p>
            </div>

            <div class="card-body">

                {{-- Peringatan jika data belum lengkap --}}
                @php
                    $hasIncompleteGrades = $storedGrades->isEmpty()
                        || $storedGrades->contains(fn($g) => $g->final_score === null);
                @endphp

                @if($hasIncompleteGrades)
                    <div class="alert alert-warning small mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Perhatian:</strong> Beberapa nilai akhir belum lengkap.
                        Pastikan semua nilai telah diisi sebelum menetapkan status kelulusan.
                    </div>
                @endif

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label small text-muted">Status Akhir</label>
                        <select
                            name="graduation_status"
                            class="form-select"
                        >
                            <option value="">— Belum Ditetapkan —</option>
                            <option
                                value="LULUS"
                                {{ old('graduation_status', $graduationStatus?->status ?? '') === 'LULUS' ? 'selected' : '' }}
                            >
                                LULUS
                            </option>
                            <option
                                value="TIDAK LULUS"
                                {{ old('graduation_status', $graduationStatus?->status ?? '') === 'TIDAK LULUS' ? 'selected' : '' }}
                            >
                                TIDAK LULUS
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">Tanggal Penetapan</label>
                        <input
                            type="date"
                            name="graduation_date"
                            class="form-control"
                            value="{{ old('graduation_date', $graduationStatus?->graduation_date?->format('Y-m-d') ?? '') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">Nomor Ijazah</label>
                        <input
                            type="text"
                            name="certificate_number"
                            class="form-control"
                            value="{{ old('certificate_number', $graduationStatus?->certificate_number ?? '') }}"
                            placeholder="Opsional"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">Nomor SKHUS</label>
                        <input
                            type="text"
                            name="skhus_number"
                            class="form-control"
                            value="{{ old('skhus_number', $graduationStatus?->skhus_number ?? '') }}"
                            placeholder="Opsional"
                        >
                    </div>

                </div>
            </div>

        </div>


        {{-- =================================================
            E. IDENTITAS PENGESAHAN
        ================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    E. Identitas Pengesahan
                </h5>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label small text-muted">Wali Kelas</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $homeroomTeacher ? $homeroomTeacher->name . ' (NIP: ' . ($homeroomTeacher->nip ?? '-') . ')' : 'Belum ditentukan' }}"
                            readonly
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-muted">Kepala Sekolah</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $headmaster ? $headmaster->name . ' (NIP: ' . ($headmaster->nip ?? '-') . ')' : 'Belum ditentukan' }}"
                            readonly
                        >
                    </div>

                </div>
            </div>

        </div>


        {{-- =================================================
            TOMBOL SIMPAN
        ================================================== --}}
        <div class="d-flex justify-content-between mb-5">

            <a
                href="{{ $returnUrl ?? route('report-card-grades.edit', [
                    'class' => $class->id,
                    'student' => $student->id
                ]) }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>

            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-circle me-1"></i>
                Simpan Nilai Akhir
            </button>

        </div>

    </form>

</div>

@endsection


@push('scripts')
<script>
/**
 * Hitung Nilai Ujian Sekolah dan Nilai Akhir secara real-time
 * di browser (untuk preview, perhitungan resmi dilakukan di server).
 */
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.input-praktik, .input-tertulis');

    inputs.forEach(function (input) {
        input.addEventListener('input', function () {
            const subjectId = this.dataset.subject;
            recalculate(subjectId);
        });
    });

    function recalculate(subjectId) {
        const praktikEl = document.querySelector('.input-praktik[data-subject="' + subjectId + '"]');
        const tertulisEl = document.querySelector('.input-tertulis[data-subject="' + subjectId + '"]');
        const examEl = document.getElementById('exam-' + subjectId);
        const finalEl = document.getElementById('final-' + subjectId);

        if (!praktikEl || !tertulisEl || !examEl || !finalEl) return;

        const praktik = parseFloat(praktikEl.value);
        const tertulis = parseFloat(tertulisEl.value);
        const schoolScore = parseFloat(finalEl.dataset.school);

        // Hitung Nilai Ujian Sekolah
        if (!isNaN(praktik) && !isNaN(tertulis)) {
            const exam = (praktik + tertulis) / 2;
            examEl.textContent = exam.toFixed(2);

            // Hitung Nilai Akhir
            if (!isNaN(schoolScore)) {
                const finalScore = (schoolScore * 0.60) + (exam * 0.40);
                finalEl.textContent = finalScore.toFixed(2);
            } else {
                finalEl.textContent = '-';
            }
        } else {
            examEl.textContent = '-';
            finalEl.textContent = '-';
        }

        // Update rata-rata
        updateAverages();
    }

    function updateAverages() {
        const examEls = document.querySelectorAll('.nilai-ujian');
        const finalEls = document.querySelectorAll('.nilai-akhir');

        let examSum = 0, examCount = 0;
        let finalSum = 0, finalCount = 0;

        examEls.forEach(el => {
            const val = parseFloat(el.textContent);
            if (!isNaN(val)) { examSum += val; examCount++; }
        });

        finalEls.forEach(el => {
            const val = parseFloat(el.textContent);
            if (!isNaN(val)) { finalSum += val; finalCount++; }
        });

        const avgExamEl = document.getElementById('avg-exam');
        const avgFinalEl = document.getElementById('avg-final');

        if (avgExamEl) {
            avgExamEl.textContent = examCount > 0 ? (examSum / examCount).toFixed(2) : '-';
        }
        if (avgFinalEl) {
            avgFinalEl.textContent = finalCount > 0 ? (finalSum / finalCount).toFixed(2) : '-';
        }
    }
});
</script>
@endpush
