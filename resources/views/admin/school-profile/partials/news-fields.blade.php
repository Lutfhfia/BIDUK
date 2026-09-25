<div class="mb-3">
    <label class="form-label">Judul Berita <span class="text-danger">*</span></label>
    <input class="form-control" name="title" required value="{{ old('title', $item->title ?? '') }}" placeholder="Contoh: Kegiatan Sosialisasi Program Belajar dan Ekstrakurikuler SDN 204">
</div>

<div class="row g-2 mb-3">
    <div class="col-md-6">
        <label class="form-label">Tanggal Publikasi</label>
        <input class="form-control" type="date" name="published_at" value="{{ old('published_at', optional($item?->published_at)->format('Y-m-d') ?: date('Y-m-d')) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Foto Utama Berita</label>
        <input class="form-control" type="file" name="image" accept="image/*">
    </div>
</div>

@if($item?->image)
    <div class="mb-3">
        <span class="badge bg-light text-dark border">Foto Saat Ini:</span>
        <img class="sp-preview-img mt-1" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}">
    </div>
@endif

<div class="mb-3">
    <label class="form-label">Ringkasan Berita (Excerpt)</label>
    <textarea class="form-control" name="summary" rows="2" placeholder="Ringkasan singkat yang ditampilkan pada kartu berita...">{{ old('summary', $item->summary ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Isi Lengkap Berita</label>
    <textarea class="form-control" rows="7" name="content" placeholder="Tulis isi berita lengkap di sini...">{{ old('content', $item->content ?? '') }}</textarea>
</div>

<div class="form-check form-switch mt-2">
    <input class="form-check-input" type="checkbox" name="is_published" id="is_pub_{{ $item?->id ?? 'new' }}" value="1" @checked(old('is_published', $item?->is_published ?? true))>
    <label class="form-check-label" for="is_pub_{{ $item?->id ?? 'new' }}">Publikasikan berita di Landing Page</label>
</div>
