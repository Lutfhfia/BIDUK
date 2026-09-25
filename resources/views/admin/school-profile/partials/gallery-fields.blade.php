<div class="mb-3">
    <label class="form-label">Judul Foto / Kegiatan <span class="text-danger">*</span></label>
    <input class="form-control" name="title" required value="{{ old('title', $item->title ?? '') }}" placeholder="Contoh: Gedung dan Fasilitas Belajar SDN 204">
</div>

<div class="mb-3">
    <label class="form-label">Keterangan / Deskripsi</label>
    <textarea class="form-control" name="description" rows="3" placeholder="Keterangan singkat mengenai foto/dokumentasi...">{{ old('description', $item->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">File Foto <span class="text-danger">{{ $item ? '' : '*' }}</span></label>
    <input class="form-control" type="file" name="image" accept="image/*" {{ $item ? '' : 'required' }}>
    <div class="form-text">Format: JPG, PNG, WEBP. Maks 5MB.</div>

    @if($item?->image)
        <div class="mt-2">
            <span class="badge bg-light text-dark border">Foto Saat Ini:</span>
            <img class="sp-preview-img mt-1" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}">
        </div>
    @endif
</div>

<div class="form-check form-switch mt-2">
    <input class="form-check-input" type="checkbox" name="is_active" id="gal_active_{{ $item?->id ?? 'new' }}" value="1" @checked(old('is_active', $item?->is_active ?? true))>
    <label class="form-check-label" for="gal_active_{{ $item?->id ?? 'new' }}">Aktif dan tampil di galeri Landing Page</label>
</div>
