@php
    $schoolName = $schoolProfile['name'] ?? 'SDN 204 Palembang';
    $schoolAddress = $schoolProfile['address'] ?? '';
    $fase = $class->fase ?? $class->phase ?? '-';

    // Pilihan semester berasal dari halaman Rekap Rapot.
    $semesterMode = request('semester', 'ganjil_genap');

    $semesterLabel = match ($semesterMode) {
        'ganjil' => 'Ganjil',
        'genap' => 'Genap',
        default => 'Ganjil & Genap',
    };

    $gradeMapGanjil = $grades->get($semesterGanjil?->id, collect())->keyBy('subject_id');
    $gradeMapGenap = $grades->get($semesterGenap?->id, collect())->keyBy('subject_id');

    // Data kenaikan hasil simpan dari form Nilai Rapot.
    $promotionStatus = $promotionStatus ?? null;
    $promotionClass = $promotionClass ?? null;
    $promotionDate = $promotionDate ?? null;

    /*
     * Bentuk tampilan mengikuti buku BIDUK fisik:
     * - Seni dan Budaya menjadi kelompok utama dengan sub a-d.
     * - Muatan Lokal menjadi kelompok utama dengan sub a-c.
     * Data nilai tetap mengambil subject yang sama dari database.
     */
    $displayRows = [];
    $rowNumber = 1;
    $seniChildrenNames = ['seni musik', 'seni rupa', 'seni teater', 'seni tari'];
    $mulokChildrenNames = ['mulok a', 'mulok b', 'mulok c'];

    foreach ($subjects as $subject) {
        $subjectName = strtolower(trim((string) $subject->name));

        if ($subjectName === 'seni budaya') {
            $displayRows[] = [
                'type' => 'parent',
                'number' => $rowNumber++,
                'label' => 'Seni dan Budaya',
                'subject' => $subject,
                'children' => $subjects->filter(fn ($s) => in_array(strtolower(trim((string) $s->name)), $seniChildrenNames, true))->values(),
            ];
            continue;
        }

        if ($subjectName === 'mulok a') {
            $displayRows[] = [
                'type' => 'parent',
                'number' => $rowNumber++,
                'label' => 'Muatan Lokal',
                'subject' => null,
                'children' => $subjects->filter(fn ($s) => in_array(strtolower(trim((string) $s->name)), $mulokChildrenNames, true))
                    ->sortBy(fn ($s) => array_search(strtolower(trim((string) $s->name)), $mulokChildrenNames, true))
                    ->values(),
            ];
            continue;
        }

        if (in_array($subjectName, $seniChildrenNames, true) || in_array($subjectName, ['mulok b', 'mulok c'], true)) {
            continue;
        }

        $displayRows[] = [
            'type' => 'normal',
            'number' => $rowNumber++,
            'label' => $subject->name,
            'subject' => $subject,
            'children' => collect(),
        ];
    }
@endphp

<div class="page">
    <div class="title">IV. LAPORAN HASIL CAPAIAN PEMBELAJARAN PESERTA DIDIK KURIKULUM MERDEKA SEKOLAH DASAR</div>

    <div class="section-title">A. IDENTITAS PESERTA DIDIK</div>
    <table class="identity">
        <tr>
            <td class="label-left">Nama Peserta Didik</td>
            <td class="colon-left">:</td>
            <td class="value-left"><div class="value-line">{{ $student->name }}</div></td>
            <td class="label-right">Kelas</td>
            <td class="colon-right">:</td>
            <td class="value-right"><div class="value-line">{{ $class->name }}</div></td>
        </tr>
        <tr>
            <td class="label-left">NISN / NIS</td>
            <td class="colon-left">:</td>
            <td class="value-left"><div class="value-line">{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</div></td>
            <td class="label-right">Fase</td>
            <td class="colon-right">:</td>
            <td class="value-right"><div class="value-line">{{ $fase }}</div></td>
        </tr>
        <tr>
            <td class="label-left">Nama Sekolah</td>
            <td class="colon-left">:</td>
            <td class="value-left"><div class="value-line">{{ $schoolName }}</div></td>
            <td class="label-right">Semester</td>
            <td class="colon-right">:</td>
            <td class="value-right"><div class="value-line">{{ ucfirst($semesterLabel) }}</div></td>
        </tr>
        <tr>
            <td class="label-left">Alamat</td>
            <td class="colon-left">:</td>
            <td class="value-left"><div class="value-line">{{ $schoolAddress ?: ($student->address ?? '-') }}</div></td>
            <td class="label-right">Tahun Pelajaran</td>
            <td class="colon-right">:</td>
            <td class="value-right"><div class="value-line">{{ $academicYear->name ?? '-' }}</div></td>
        </tr>
    </table>

    @if(!$isFinalClass)
        <div class="section-title">B. INTRAKURIKULER</div>
        <table class="report">
            <colgroup>
                <col style="width:5%"><col style="width:22%"><col style="width:10%"><col style="width:27%"><col style="width:10%"><col style="width:26%">
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="2">NO.</th>
                    <th rowspan="2">MATA PELAJARAN</th>
                    <th colspan="2">SEMESTER GANJIL</th>
                    <th colspan="2">SEMESTER GENAP</th>
                </tr>
                <tr>
                    <th>NILAI AKHIR</th>
                    <th>CAPAIAN PEMBELAJARAN</th>
                    <th>NILAI AKHIR</th>
                    <th>CAPAIAN PEMBELAJARAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($displayRows as $row)
                    @if($row['type'] === 'parent')
                        @php
                            $parent = $row['subject'];
                            $pg = $parent ? $gradeMapGanjil->get($parent->id) : null;
                            $pe = $parent ? $gradeMapGenap->get($parent->id) : null;
                        @endphp
                        <tr>
                            <td class="center">{{ $row['number'] }}</td>
                            <td class="subject">{{ $row['label'] }}</td>
                            <td class="center">{{ $pg?->score ?? '' }}</td>
                            <td>{{ $pg?->learning_outcome ?? '' }}</td>
                            <td class="center">{{ $pe?->score ?? '' }}</td>
                            <td>{{ $pe?->learning_outcome ?? '' }}</td>
                        </tr>
                        @foreach($row['children'] as $childIndex => $child)
                            @php
                                $cg = $gradeMapGanjil->get($child->id);
                                $ce = $gradeMapGenap->get($child->id);
                            @endphp
                            <tr>
                                <td class="center"></td>
                                <td class="subject">{{ chr(97 + $childIndex) }}. {{ $child->name }}</td>
                                <td class="center">{{ $cg?->score ?? '' }}</td>
                                <td>{{ $cg?->learning_outcome ?? '' }}</td>
                                <td class="center">{{ $ce?->score ?? '' }}</td>
                                <td>{{ $ce?->learning_outcome ?? '' }}</td>
                            </tr>
                        @endforeach
                    @else
                        @php
                            $subject = $row['subject'];
                            $g = $gradeMapGanjil->get($subject->id);
                            $e = $gradeMapGenap->get($subject->id);
                        @endphp
                        <tr>
                            <td class="center">{{ $row['number'] }}</td>
                            <td class="subject">{{ $row['label'] }}</td>
                            <td class="center">{{ $g?->score ?? '' }}</td>
                            <td>{{ $g?->learning_outcome ?? '' }}</td>
                            <td class="center">{{ $e?->score ?? '' }}</td>
                            <td>{{ $e?->learning_outcome ?? '' }}</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <th colspan="2" class="center">Jumlah Nilai</th>
                    <td class="center">{{ $gradeMapGanjil->sum(fn($g) => (float)$g->score) ?: '' }}</td>
                    <td></td>
                    <td class="center">{{ $gradeMapGenap->sum(fn($g) => (float)$g->score) ?: '' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="2" class="center">RATA - RATA</th>
                    <td class="center">{{ $gradeMapGanjil->count() ? number_format($gradeMapGanjil->avg(fn($g) => (float)$g->score), 2, ',', '.') : '' }}</td>
                    <td></td>
                    <td class="center">{{ $gradeMapGenap->count() ? number_format($gradeMapGenap->avg(fn($g) => (float)$g->score), 2, ',', '.') : '' }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="section-title">B. INTRAKURIKULER</div>
        <table class="report">
            <colgroup>
                <col style="width:5%"><col style="width:22%"><col style="width:11%"><col style="width:25%"><col style="width:11%"><col style="width:26%">
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="2">NO.</th>
                    <th rowspan="2">MATA PELAJARAN</th>
                    <th colspan="2">NILAI RATA-RATA RAPORT</th>
                    <th colspan="2">NILAI UJIAN SEKOLAH</th>
                </tr>
                <tr>
                    <th>NILAI AKHIR</th>
                    <th>CAPAIAN PEMBELAJARAN</th>
                    <th>NILAI AKHIR</th>
                    <th>CAPAIAN PEMBELAJARAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($displayRows as $row)
                    @if($row['type'] === 'parent')
                        @php
                            $parent = $row['subject'];
                            $vals = $parent ? collect([
                                $gradeMapGanjil->get($parent->id)?->score,
                                $gradeMapGenap->get($parent->id)?->score,
                            ])->filter(fn($v) => $v !== null && $v !== '') : collect();
                            $average = $vals->count() ? $vals->avg() : null;
                            $outcomes = $parent ? collect([
                                $gradeMapGanjil->get($parent->id)?->learning_outcome,
                                $gradeMapGenap->get($parent->id)?->learning_outcome,
                            ])->filter()->values() : collect();
                        @endphp
                        <tr>
                            <td class="center">{{ $row['number'] }}</td>
                            <td class="subject">{{ $row['label'] }}</td>
                            <td class="center">{{ $average !== null ? number_format($average, 2, ',', '.') : '' }}</td>
                            <td>{{ $outcomes->join(' / ') }}</td>
                            <td class="center"></td>
                            <td></td>
                        </tr>
                        @foreach($row['children'] as $childIndex => $child)
                            @php
                                $vals = collect([
                                    $gradeMapGanjil->get($child->id)?->score,
                                    $gradeMapGenap->get($child->id)?->score,
                                ])->filter(fn($v) => $v !== null && $v !== '');
                                $average = $vals->count() ? $vals->avg() : null;
                                $outcomes = collect([
                                    $gradeMapGanjil->get($child->id)?->learning_outcome,
                                    $gradeMapGenap->get($child->id)?->learning_outcome,
                                ])->filter()->values();
                            @endphp
                            <tr>
                                <td class="center"></td>
                                <td class="subject">{{ chr(97 + $childIndex) }}. {{ $child->name }}</td>
                                <td class="center">{{ $average !== null ? number_format($average, 2, ',', '.') : '' }}</td>
                                <td>{{ $outcomes->join(' / ') }}</td>
                                <td class="center"></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @else
                        @php
                            $subject = $row['subject'];
                            $scores = collect([$gradeMapGanjil->get($subject->id)?->score, $gradeMapGenap->get($subject->id)?->score])->filter(fn($v) => $v !== null && $v !== '');
                            $average = $scores->count() ? $scores->avg() : null;
                            $outcomes = collect([$gradeMapGanjil->get($subject->id)?->learning_outcome, $gradeMapGenap->get($subject->id)?->learning_outcome])->filter()->values();
                        @endphp
                        <tr>
                            <td class="center">{{ $row['number'] }}</td>
                            <td class="subject">{{ $row['label'] }}</td>
                            <td class="center">{{ $average !== null ? number_format($average, 2, ',', '.') : '' }}</td>
                            <td>{{ $outcomes->join(' / ') }}</td>
                            <td class="center"></td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <th colspan="2">RATA - RATA</th>
                    <td class="center">
                        @php
                            $finalScores = $subjects->map(function($subject) use ($gradeMapGanjil, $gradeMapGenap) {
                                $vals = collect([
                                    $gradeMapGanjil->get($subject->id)?->score,
                                    $gradeMapGenap->get($subject->id)?->score,
                                ])->filter(fn($v) => $v !== null && $v !== '');
                                return $vals->count() ? $vals->avg() : null;
                            })->filter(fn($v) => $v !== null);
                        @endphp
                        {{ $finalScores->count() ? number_format($finalScores->avg(), 2, ',', '.') : '' }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="footer-note">Nilai Ujian Sekolah dan Nilai Sekolah belum ditampilkan karena sumber data tersebut belum tersedia pada struktur database BIDUK saat ini.</div>
    @endif

    <div class="section-title">C. EKSTRAKURIKULER</div>
    <table class="report">
        <colgroup>
            <col style="width:4%">
            <col style="width:30%">
            <col style="width:33%">
            <col style="width:33%">
        </colgroup>
        <thead>
            <tr>
                <th>NO.</th>
                <th>KEGIATAN EKSTRAKURIKULER</th>
                <th>KETERANGAN</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach(range(1,3) as $i)
                <tr>
                    <td class="center">{{ $i }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">D. PRESTASI</div>
    <table class="report">
        <colgroup>
            <col style="width:4%">
            <col style="width:30%">
            <col style="width:33%">
            <col style="width:33%">
        </colgroup>
        <thead>
            <tr>
                <th>NO.</th>
                <th>JENIS PRESTASI</th>
                <th>KETERANGAN</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($achievements as $index => $achievement)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $achievement->type }}</td>
                    <td>{{ $achievement->level ?? '' }}</td>
                    <td>{{ $achievement->description ?? '' }}</td>
                </tr>
            @endforeach
            @for($i = $achievements->count() + 1; $i <= 3; $i++)
                <tr>
                    <td class="center">{{ $i }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="section-title">E. KETIDAKHADIRAN DAN KENAIKAN</div>

    <table class="attendance-promotion-table">
        <colgroup>
            <col style="width:14.2857%">
            <col style="width:14.2857%">
            <col style="width:14.2857%">
            <col style="width:14.2857%">
            <col style="width:14.2857%">
            <col style="width:14.2857%">
            <col style="width:14.2857%">
        </colgroup>

        {{-- Header --}}
        <tr>
            <th class="e-left" rowspan="4">
                KETIDAKHADIRAN
            </th>

            <th class="e-semester" colspan="2">
                Semester Ganjil
            </th>

            <th class="e-semester" colspan="2">
                Semester Genap
            </th>

            <td class="e-signature" rowspan="5">
                <div class="signature-block">
                    Wali Kelas
                    <div class="signature-space"></div>
                    <div class="signature-line">................................</div>
                    <div class="signature-nip">NIP. ...........................</div>
                </div>
            </td>

            <td class="e-signature" rowspan="5">
                <div class="signature-block">
                    Mengetahui<br>
                    Kepala Sekolah
                    <div class="signature-space"></div>
                    <div class="signature-line">................................</div>
                    <div class="signature-nip">NIP. ...........................</div>
                </div>
            </td>
        </tr>

        {{-- Sakit --}}
        <tr>
            <td class="e-attendance-label">1. Sakit</td>
            <td class="e-attendance-days">{{ $attendanceGanjil?->sakit ?? 0 }} Hari</td>
            <td class="e-attendance-label">1. Sakit</td>
            <td class="e-attendance-days">{{ $attendanceGenap?->sakit ?? 0 }} Hari</td>
        </tr>

        {{-- Izin --}}
        <tr>
            <td class="e-attendance-label">2. Izin</td>
            <td class="e-attendance-days">{{ $attendanceGanjil?->izin ?? 0 }} Hari</td>
            <td class="e-attendance-label">2. Izin</td>
            <td class="e-attendance-days">{{ $attendanceGenap?->izin ?? 0 }} Hari</td>
        </tr>

        {{-- Tanpa Keterangan --}}
        <tr>
            <td class="e-attendance-label">3. Tanpa Keterangan</td>
            <td class="e-attendance-days">{{ $attendanceGanjil?->tanpa_keterangan ?? 0 }} Hari</td>
            <td class="e-attendance-label">3. Tanpa Keterangan</td>
            <td class="e-attendance-days">{{ $attendanceGenap?->tanpa_keterangan ?? 0 }} Hari</td>
        </tr>

        {{-- Kenaikan --}}
        <tr>
            <th class="promotion-left">KENAIKAN</th>

            <td class="promotion-decision" colspan="2">
                <strong>Keputusan</strong><br>
                Berdasarkan Capaian Pembelajaran pada<br>
                Semester I dan II, Peserta Didik<br>
                ditetapkan
            </td>

            <td class="promotion-result" colspan="2">
                <table class="promotion-inner">
                    <tr>
                        <td class="result-title">{{ $promotionStatus ?: 'Naik / Tidak Naik' }}</td>
                    </tr>
                    <tr>
                        <td class="field">Ke Kelas: {{ $promotionClass ?: '........................' }}</td>
                    </tr>
                    <tr>
                        <td class="last">Tanggal: {{ $promotionDate ? date('d-m-Y', strtotime($promotionDate)) : '........................' }}</td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
