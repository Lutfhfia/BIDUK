<div class="mb-3">
    <label class="form-label">Nama Prestasi <span class="text-danger">*</span></label>
    <input class="form-control" name="title" required value="{{ old('title', $item->title ?? '') }}" placeholder="Contoh: Juara 1 Lomba Cerdas Cermat">
</div>
<div class="row g-2 mb-3">
    <div class="col-md-6">
        <label class="form-label">Tahun</label>
        <input class="form-control" type="number" name="year" min="1900" max="2100" value="{{ old('year', $item->year ?? date('Y')) }}" placeholder="2026">
    </div>
    <div class="col-md-6">
        <label class="form-label">Tingkat</label>
        <select class="form-select" name="level">
            <option value="">Pilih tingkat</option>
            @foreach(['Sekolah','Kecamatan','Kota','Provinsi','Nasional','Internasional'] as $level)
                <option value="{{ $level }}" @selected(old('level', $item->level ?? '') === $level)>{{ $level }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi / Keterangan</label>
    <textarea class="form-control" name="description" rows="3" placeholder="Keterangan singkat prestasi yang diraih...">{{ old('description', $item->description ?? '') }}</textarea>
</div>
<div>
    <label class="form-label">Foto / Piagam / Dokumentasi</label>
    <input class="form-control" type="file" name="image" accept="image/*">
    <div class="form-text">Format: JPG, PNG, WEBP. Maksimal 5MB.</div>
    @if($item?->image)
        <div class="mt-2">
            <span class="badge bg-light text-dark border">Foto Saat Ini:</span>
            <img class="sp-preview-img mt-1" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}">
        </div>
    @endif
</div>
