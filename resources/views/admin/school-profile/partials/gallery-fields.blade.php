<div class="mb-3">
    <label class="form-label">Judul Galeri</label>
    <input class="form-control" name="title" required value="{{ old('title', $item->title ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea class="form-control" name="description" rows="4">{{ old('description', $item->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Foto Galeri</label>
    <input class="form-control" type="file" name="image" accept="image/*" {{ $item ? '' : 'required' }}>
    @if($item?->image)
        <img class="sp-preview" src="{{ asset('storage/'.$item->image) }}" alt="Foto galeri">
    @endif
</div>
<div class="form-check">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item?->is_active ?? true))>
    <label class="form-check-label">Aktif dan tampil di landing page</label>
</div>
