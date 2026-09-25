<div class="mb-3">
    <label class="form-label">Judul Berita</label>
    <input class="form-control" name="title" required value="{{ old('title', $item->title ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Tanggal Publikasi</label>
    <input class="form-control" type="date" name="published_at" value="{{ old('published_at', optional($item?->published_at)->format('Y-m-d')) }}">
</div>
<div class="mb-3">
    <label class="form-label">Ringkasan</label>
    <textarea class="form-control" name="summary" rows="3">{{ old('summary', $item->summary ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Isi Berita</label>
    <textarea class="form-control" rows="6" name="content">{{ old('content', $item->content ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Foto Berita</label>
    <input class="form-control" type="file" name="image" accept="image/*">
    @if($item?->image)
        <img class="sp-preview" src="{{ asset('storage/'.$item->image) }}" alt="Foto berita">
    @endif
</div>
<div class="form-check">
    <input class="form-check-input" type="checkbox" name="is_published" value="1" @checked(old('is_published', $item?->is_published ?? false))>
    <label class="form-check-label">Publikasikan berita</label>
</div>
