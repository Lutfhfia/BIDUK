{{-- MODAL PRESTASI --}}
<div class="modal fade" id="addAchievement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('school-achievements.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-trophy me-2"></i>Tambah Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">@include('admin.school-profile.partials.achievement-fields', ['item' => null])</div>
            <div class="modal-footer"><button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Simpan</button></div>
        </form>
    </div>
</div>

@foreach($achievements as $item)
    <div class="modal fade" id="editAchievement{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('school-achievements.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Prestasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">@include('admin.school-profile.partials.achievement-fields', ['item' => $item])</div>
                <div class="modal-footer"><button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Update</button></div>
            </form>
        </div>
    </div>
@endforeach

{{-- MODAL EKSTRAKURIKULER --}}
<div class="modal fade" id="addEkskul" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('extracurriculars.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-people me-2"></i>Tambah Ekstrakurikuler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">@include('admin.school-profile.partials.exkul-fields', ['item' => null])</div>
            <div class="modal-footer"><button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Simpan</button></div>
        </form>
    </div>
</div>

@foreach($extracurriculars as $item)
    <div class="modal fade" id="editEkskul{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('extracurriculars.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Ekstrakurikuler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">@include('admin.school-profile.partials.exkul-fields', ['item' => $item])</div>
                <div class="modal-footer"><button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Update</button></div>
            </form>
        </div>
    </div>
@endforeach

{{-- MODAL BERITA --}}
<div class="modal fade" id="addNews" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('school-news.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-newspaper me-2"></i>Tambah Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">@include('admin.school-profile.partials.news-fields', ['item' => null])</div>
            <div class="modal-footer"><button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Simpan</button></div>
        </form>
    </div>
</div>

@foreach($newsItems as $item)
    <div class="modal fade" id="editNews{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('school-news.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Berita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">@include('admin.school-profile.partials.news-fields', ['item' => $item])</div>
                <div class="modal-footer"><button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Update</button></div>
            </form>
        </div>
    </div>
@endforeach

{{-- MODAL GALERI --}}
<div class="modal fade" id="addGallery" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('school-galleries.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-images me-2"></i>Tambah Galeri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">@include('admin.school-profile.partials.gallery-fields', ['item' => null])</div>
            <div class="modal-footer"><button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Simpan</button></div>
        </form>
    </div>
</div>

@foreach($galleries as $item)
    <div class="modal fade" id="editGallery{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('school-galleries.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Galeri</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">@include('admin.school-profile.partials.gallery-fields', ['item' => $item])</div>
                <div class="modal-footer"><button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Update</button></div>
            </form>
        </div>
    </div>
@endforeach
