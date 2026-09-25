<div class="mb-3">
    <label class="form-label">Nama Ekstrakurikuler</label>
    <input class="form-control" name="name" required value="{{ old('name', $item->name ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Foto Ekstrakurikuler</label>
    <input class="form-control" type="file" name="image" accept="image/jpeg,image/png,image/webp">
    <small class="text-muted">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>

    @if($item?->image)
        <img
            src="{{ asset('storage/' . $item->image) }}"
            alt="{{ $item->name }}"
            class="img-fluid rounded mt-3"
            style="width: 100%; max-height: 190px; object-fit: cover;"
        >
    @endif
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea class="form-control" name="description" rows="4">{{ old('description', $item->description ?? '') }}</textarea>
</div>

<div class="form-check">
    <input
        class="form-check-input"
        type="checkbox"
        name="is_active"
        value="1"
        @checked(old('is_active', $item?->is_active ?? true))
    >
    <label class="form-check-label">Aktif dan tampil di landing page</label>
</div>
