<div class="mb-3">
    <label class="form-label">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
    <input class="form-control" name="name" required value="{{ old('name', $item->name ?? '') }}" placeholder="Contoh: Pramuka / Futsal / Seni Tari">
</div>

<div class="mb-3">
    <label class="form-label">Foto / Banner Kegiatan</label>
    <input class="form-control" type="file" name="image" accept="image/jpeg,image/png,image/webp">
    <div class="form-text">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</div>

    @if($item?->image)
        <div class="mt-2">
            <span class="badge bg-light text-dark border">Foto Saat Ini:</span>
            <img src="{{ asset('storage/' . $item->image) }}"
                alt="{{ $item->name }}"
                class="sp-preview-img mt-1">
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea class="form-control" name="description" rows="3" placeholder="Deskripsi mengenai kegiatan, jadwal, dan manfaat ekskul...">{{ old('description', $item->description ?? '') }}</textarea>
</div>

<div class="form-check form-switch mt-2">
    <input
        class="form-check-input"
        type="checkbox"
        name="is_active"
        id="is_active_{{ $item?->id ?? 'new' }}"
        value="1"
        @checked(old('is_active', $item?->is_active ?? true))
    >
    <label class="form-check-label" for="is_active_{{ $item?->id ?? 'new' }}">Aktif dan tampil di landing page</label>
</div>
