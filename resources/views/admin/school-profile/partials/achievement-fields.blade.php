<div class="mb-3">
    <label class="form-label">Nama Prestasi</label>
    <input class="form-control" name="title" required value="{{ old('title', $item->title ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Tahun</label>
    <input class="form-control" type="number" name="year" min="1900" max="2100" value="{{ old('year', $item->year ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Tingkat</label>
    <select class="form-select" name="level">
        <option value="">Pilih tingkat</option>
        @foreach(['Sekolah','Kecamatan','Kota','Provinsi','Nasional','Internasional'] as $level)
            <option value="{{ $level }}" @selected(old('level', $item->level ?? '') === $level)>{{ $level }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea class="form-control" name="description" rows="4">{{ old('description', $item->description ?? '') }}</textarea>
</div>
<div>
    <label class="form-label">Foto Prestasi</label>
    <input class="form-control" type="file" name="image" accept="image/*">
    @if($item?->image)
        <img class="sp-preview" src="{{ asset('storage/'.$item->image) }}" alt="Foto prestasi">
    @endif
</div>
