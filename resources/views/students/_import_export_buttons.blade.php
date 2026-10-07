{{-- Tempelkan blok ini di bagian tombol pada students/index.blade.php --}}
<a href="{{ route('students.import.form') }}" class="btn btn-outline-success">
    <i class="bi bi-file-earmark-arrow-up me-1"></i>
    Import Excel
</a>

<a href="{{ route('students.export.excel') }}" class="btn btn-outline-primary">
    <i class="bi bi-file-earmark-excel me-1"></i>
    Export Excel
</a>
