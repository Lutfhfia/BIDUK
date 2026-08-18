# DOKUMENTASI PERANCANGAN APLIKASI BIDUK

## Buku Induk Data Siswa Sekolah Dasar (SDN 204)

**Dokumen:** Spesifikasi Sistem dan Fitur Data Siswa\
**Nama Aplikasi:** BIDUK\
**Lingkup:** Sistem Informasi Buku Induk dan Administrasi Data Sekolah\
**Target Pengguna:** Super Admin, Admin/Tata Usaha, Guru/Wali Kelas,
Kepala Sekolah\
**Teknologi yang direncanakan:** Laravel, PHP, MySQL/MariaDB, Bootstrap,
CSS, JavaScript, HTML, Laragon, Git/GitHub

------------------------------------------------------------------------

# DAFTAR ISI

1.  Gambaran Umum Aplikasi BIDUK
2.  Tujuan Aplikasi
3.  Ruang Lingkup Sistem
4.  Aktor dan Hak Akses
5.  Struktur Menu Utama
6.  Konsep Data dan Relasi Antar Fitur
7.  Dashboard
8.  Master Data
9.  Fitur Data Siswa
10. Alur Data Siswa Super Admin
11. Alur Data Siswa Guru/Wali Kelas
12. Data Induk Siswa
13. Pintasan Input Nilai Rapot
14. Fitur Data Pegawai
15. Fitur Data Kelas
16. Fitur Akademik
17. Fitur Laporan
18. Fitur Pengaturan
19. Landing Page Sekolah
20. Konsep Database
21. Relasi Data Siswa dengan Modul Lain
22. Aturan Bisnis
23. Keamanan dan Otorisasi
24. Validasi Data
25. Import dan Export Excel
26. Pencatatan Log Aktivitas
27. Arsitektur Fullstack
28. Struktur Project Laravel
29. Rancangan Route
30. Rancangan Controller dan Service
31. Rancangan Antarmuka
32. Rancangan Kolaborasi Git/GitHub
33. Skenario Pengujian
34. Kesimpulan Perancangan

------------------------------------------------------------------------

# 1. GAMBARAN UMUM APLIKASI BIDUK

BIDUK merupakan aplikasi berbasis web yang dirancang untuk membantu
sekolah dalam mengelola data administrasi dan akademik siswa secara
terintegrasi.

Aplikasi ini tidak hanya berfungsi sebagai tempat penyimpanan data
siswa, tetapi menjadi pusat pengelolaan data sekolah yang menghubungkan:

-   data siswa;
-   data induk siswa;
-   data pegawai;
-   data kelas;
-   tahun ajaran;
-   mata pelajaran;
-   semester;
-   nilai rapot;
-   laporan buku induk;
-   rekap absensi;
-   rekap rapot;
-   rekap prestasi;
-   informasi sekolah;
-   berita sekolah;
-   manajemen pengguna;
-   hak akses role;
-   log aktivitas.

Konsep utama BIDUK adalah **satu data siswa digunakan oleh berbagai
fitur**, sehingga pengguna tidak perlu memasukkan identitas siswa
berulang kali pada setiap modul.

Contohnya:

``` text
DATA SISWA
    │
    ├── Data Induk Siswa
    │
    ├── Kelas
    │
    ├── Tahun Ajaran
    │
    ├── Nilai Rapot
    │
    ├── Absensi
    │
    ├── Prestasi
    │
    └── Laporan Buku Induk
```

Dengan pendekatan tersebut, perubahan identitas dasar siswa dapat
dikelola dari sumber data yang terstruktur dan digunakan kembali oleh
modul lainnya.

------------------------------------------------------------------------

# 2. TUJUAN APLIKASI

## 2.1 Tujuan Umum

Membangun sistem informasi sekolah yang membantu pengelolaan data siswa
dan administrasi sekolah secara terpusat, terstruktur, aman, dan mudah
digunakan.

## 2.2 Tujuan Khusus

Aplikasi diharapkan dapat:

1.  Mengelola data siswa secara terpusat.
2.  Mengurangi pencatatan data siswa secara manual.
3.  Memudahkan wali kelas melengkapi data induk siswa.
4.  Memudahkan guru/wali kelas menginput nilai rapot.
5.  Menghubungkan data siswa dengan kelas dan tahun ajaran.
6.  Memudahkan Tata Usaha mengelola data master.
7.  Memudahkan Kepala Sekolah melakukan monitoring.
8.  Menghasilkan laporan sekolah berdasarkan data yang tersimpan.
9.  Menyediakan informasi sekolah untuk landing page.
10. Mencatat aktivitas penting pengguna melalui log aktivitas.

------------------------------------------------------------------------

# 3. RUANG LINGKUP SISTEM

BIDUK memiliki beberapa kelompok fitur utama.

## 3.1 Dashboard

Dashboard menampilkan ringkasan kondisi data sekolah.

Contoh:

-   total siswa;
-   total guru/pegawai;
-   total kelas;
-   total pengguna;
-   statistik siswa per kelas;
-   statistik lainnya yang relevan.

## 3.2 Master Data

Master data terdiri dari:

-   Data Siswa
-   Data Pegawai
-   Data Kelas

## 3.3 Akademik

Akademik terdiri dari:

-   Tahun Ajaran
-   Mata Pelajaran
-   Semester
-   Nilai Rapot

## 3.4 Laporan

Laporan terdiri dari:

-   Buku Induk
-   Rekap Absensi
-   Rekap Rapot
-   Rekap Prestasi

## 3.5 Pengaturan

Pengaturan terdiri dari:

-   Manajemen User
-   Hak Akses Role
-   Profil Sekolah
-   Berita Sekolah
-   Log Aktivitas

------------------------------------------------------------------------

# 4. AKTOR DAN HAK AKSES

BIDUK memiliki empat kelompok pengguna utama.

## 4.1 Super Admin

Super Admin memiliki akses pengelolaan sistem paling luas.

### Akses:

-   Dashboard
-   Data Siswa
-   Data Pegawai
-   Data Kelas
-   Tahun Ajaran
-   Mata Pelajaran
-   Semester
-   Nilai Rapot
-   Semua laporan
-   Manajemen User
-   Hak Akses Role
-   Profil Sekolah
-   Berita Sekolah
-   Log Aktivitas

Super Admin dapat melakukan CRUD sesuai hak pengelolaan masing-masing
fitur.

------------------------------------------------------------------------

## 4.2 Admin / Tata Usaha

Admin/Tata Usaha bertugas mengelola administrasi sekolah.

### Dapat mengelola:

-   Data Siswa
-   Data Pegawai
-   Data Kelas
-   Tahun Ajaran
-   Mata Pelajaran
-   Semester

### Dapat mengakses:

-   Buku Induk
-   Rekap Absensi
-   Rekap Rapot
-   Rekap Prestasi

### Pengaturan:

-   Profil Sekolah

### Tidak diperbolehkan:

-   Mengelola akun pengguna
-   Menambah/menghapus user
-   Mengubah role user
-   Mengatur hak akses role

------------------------------------------------------------------------

## 4.3 Guru / Wali Kelas

Guru memiliki akses berdasarkan kelas yang menjadi tanggung jawabnya.

### Dapat mengakses:

-   Data siswa yang berada di kelas tanggung jawabnya
-   Data induk siswa
-   Nilai rapot
-   Laporan untuk kelas yang menjadi tanggung jawabnya

### Tidak dapat:

-   Melihat seluruh siswa sekolah jika bukan bagian dari kelasnya
-   Mengubah master data sekolah secara global
-   Mengelola user
-   Mengelola role
-   Mengatur hak akses sistem

------------------------------------------------------------------------

## 4.4 Kepala Sekolah

Kepala Sekolah digunakan sebagai role monitoring.

Kepala Sekolah dapat:

-   melihat dashboard;
-   melihat data siswa;
-   melihat data pegawai;
-   melihat data kelas;
-   melihat data akademik;
-   melihat nilai;
-   melihat laporan;
-   melihat informasi sekolah sesuai kebutuhan monitoring.

Kepala Sekolah **tidak melakukan perubahan data**.

Dengan demikian:

``` text
Kepala Sekolah = Read / Monitoring
```

bukan:

``` text
Kepala Sekolah = CRUD
```

------------------------------------------------------------------------

# 5. STRUKTUR MENU UTAMA

Sidebar BIDUK secara umum:

``` text
BIDUK SDN 204

Dashboard

MASTER DATA
├── Data Siswa
├── Data Pegawai
└── Data Kelas

AKADEMIK
├── Tahun Ajaran
├── Mata Pelajaran
├── Semester
└── Nilai Rapot

LAPORAN
├── Buku Induk
├── Rekap Absensi
├── Rekap Rapot
└── Rekap Prestasi

PENGATURAN
├── Manajemen User
├── Hak Akses Role
├── Profil Sekolah
├── Berita Sekolah
└── Log Aktivitas
```

Menu ditampilkan secara dinamis berdasarkan role.

Contoh:

``` text
Super Admin
→ semua menu

Tata Usaha
→ menu administrasi yang diizinkan

Guru/Wali Kelas
→ menu sesuai tugasnya

Kepala Sekolah
→ menu monitoring
```

Catatan penting:

**Menyembunyikan menu saja tidak cukup untuk keamanan.**

Laravel tetap harus melakukan authorization pada
route/controller/policy/middleware agar user tidak dapat mengakses URL
secara langsung.

------------------------------------------------------------------------

# 6. KONSEP DATA DAN RELASI ANTAR FITUR

BIDUK menggunakan konsep data terintegrasi.

Data siswa menjadi salah satu pusat data utama.

``` text
                     TAHUN AJARAN
                          │
                          │
                       KELAS
                          │
                     WALI KELAS
                          │
                          ▼
                       SISWA
                     /   │   \
                    /    │    \
                   ▼     ▼     ▼
             DATA INDUK NILAI ABSENSI
                   │
                   ▼
               PRESTASI
                   │
                   ▼
                LAPORAN
```

Dengan demikian, modul tidak berdiri sendiri.

------------------------------------------------------------------------

# 7. DASHBOARD

Dashboard merupakan halaman pertama setelah pengguna berhasil login.

## 7.1 Dashboard Super Admin

Contoh kartu:

``` text
┌───────────────┐ ┌───────────────┐
│ Total Siswa   │ │ Total Pegawai │
│     280       │ │      35       │
└───────────────┘ └───────────────┘

┌───────────────┐ ┌───────────────┐
│ Total Kelas   │ │ Tahun Aktif   │
│      12       │ │ 2026/2027     │
└───────────────┘ └───────────────┘
```

Dashboard dapat menampilkan grafik jumlah siswa per kelas.

## 7.2 Dashboard Wali Kelas

Dashboard dapat difokuskan pada kelasnya.

Contoh:

``` text
Wali Kelas: 4A

Jumlah Siswa       : 28
Data Induk Lengkap : 22
Belum Lengkap      : 6
Nilai Terisi       : 20
Belum Selesai      : 8
```

## 7.3 Dashboard Kepala Sekolah

Fokus pada monitoring.

Contoh:

-   total siswa;
-   jumlah kelas;
-   jumlah pegawai;
-   grafik siswa per kelas;
-   ringkasan kelengkapan data;
-   ringkasan nilai.

------------------------------------------------------------------------

# 8. MASTER DATA

Master data terdiri dari tiga komponen utama:

1.  Data Siswa
2.  Data Pegawai
3.  Data Kelas

Data siswa merupakan modul penting karena menjadi sumber identitas siswa
untuk fitur lainnya.

------------------------------------------------------------------------

# 9. FITUR DATA SISWA

## 9.1 Tujuan

Fitur Data Siswa digunakan untuk mengelola identitas dasar seluruh
peserta didik yang terdaftar di sekolah.

Fitur ini berbeda dengan **Data Induk Siswa**.

### Data Siswa

Berisi identitas dasar yang diperlukan untuk pengelolaan siswa.

### Data Induk Siswa

Berisi informasi siswa secara lebih lengkap sesuai kebutuhan buku induk.

Dengan pemisahan ini, tabel utama tidak terlalu penuh tetapi informasi
lengkap tetap dapat diakses melalui detail siswa.

------------------------------------------------------------------------

# 10. STRUKTUR TABEL DATA SISWA

Tabel utama Data Siswa memiliki kolom:

  Kolom                        Keterangan
  ---------------------------- -----------------------
  No Urut                      Nomor urutan tampilan
  Nomor Induk                  Nomor induk siswa/NIS
  Nomor Induk Siswa Nasional   NISN
  Nama Peserta Didik           Nama siswa
  Jenis Kelamin                L/P
  Kelas                        Kelas siswa
  Aksi                         Operasi yang tersedia

Contoh:

``` text
┌────┬────────┬────────────┬─────────────────┬────┬───────┬─────────┐
│ No │ NIS    │ NISN       │ Nama            │ JK │ Kelas │ Aksi    │
├────┼────────┼────────────┼─────────────────┼────┼───────┼─────────┤
│ 1  │ 24001  │ 0101010101 │ Ahmad Ramadhan  │ L  │ 4A    │ CRUD    │
│ 2  │ 24002  │ 0101010102 │ Siti Aisyah     │ P  │ 4A    │ CRUD    │
└────┴────────┴────────────┴─────────────────┴────┴───────┴─────────┘
```

------------------------------------------------------------------------

# 11. TAMPILAN DATA SISWA SUPER ADMIN

Super Admin dapat melihat data siswa seluruh sekolah.

Halaman:

``` text
Master Data > Data Siswa
```

Bagian atas halaman dapat berisi:

``` text
Data Siswa

[ + Tambah Siswa ] [ Import Excel ] [ Export Excel ]

[ Search Nama/NIS/NISN ] [ Filter Kelas ] [ Filter Status ]
```

Kemudian tabel:

``` text
No Urut
Nomor Induk
NISN
Nama Peserta Didik
Jenis Kelamin
Kelas
Aksi
```

------------------------------------------------------------------------

# 12. AKSI CRUD SUPER ADMIN

Super Admin memiliki operasi:

## Create

Menambahkan siswa baru.

## Read

Melihat data siswa.

## Update

Mengubah data siswa.

## Delete

Menghapus data siswa sesuai aturan sistem.

Namun penghapusan perlu dibuat aman.

Sistem sebaiknya memberikan konfirmasi:

``` text
Apakah Anda yakin ingin menghapus siswa ini?

[ Batal ] [ Hapus ]
```

Jika siswa sudah memiliki data akademik/rapot/laporan, penghapusan
permanen sebaiknya tidak dilakukan sembarangan.

Alternatif yang lebih aman adalah menggunakan:

``` text
status siswa
```

atau soft delete.

------------------------------------------------------------------------

# 13. TAMBAH DATA SISWA

Form Tambah Siswa minimal dapat berisi:

``` text
Nomor Induk
Nomor Induk Siswa Nasional
Nama Peserta Didik
Jenis Kelamin
Kelas
```

Contoh:

``` text
Nomor Induk
[________________]

NISN
[________________]

Nama Peserta Didik
[________________]

Jenis Kelamin
[ Laki-laki ▼ ]

Kelas
[ 4A ▼ ]

[ Batal ] [ Simpan ]
```

------------------------------------------------------------------------

# 14. VALIDASI DATA SISWA

Validasi penting untuk mencegah data ganda dan data tidak valid.

## 14.1 Nomor Induk

-   wajib diisi;
-   tidak boleh kosong;
-   harus unik sesuai aturan sekolah.

## 14.2 NISN

-   wajib diisi jika memang menjadi data wajib sekolah;
-   harus memiliki format yang benar;
-   sebaiknya unik.

## 14.3 Nama

-   wajib diisi;
-   tidak boleh hanya berisi spasi.

## 14.4 Jenis Kelamin

Pilihan:

``` text
Laki-laki
Perempuan
```

## 14.5 Kelas

Dipilih dari tabel kelas.

Sebaiknya jangan diketik manual.

------------------------------------------------------------------------

# 15. PENCARIAN DATA SISWA

Data siswa perlu memiliki pencarian.

Pengguna dapat mencari berdasarkan:

-   nama;
-   NIS;
-   NISN.

Contoh:

``` text
[ Cari Nama, NIS, atau NISN... 🔍 ]
```

Pencarian sebaiknya dilakukan dengan query database dan pagination,
bukan mengambil seluruh data lalu memprosesnya di browser.

------------------------------------------------------------------------

# 16. FILTER DATA SISWA

Filter dapat berdasarkan:

-   kelas;
-   status siswa;
-   tahun ajaran jika struktur penempatan siswa menggunakan tahun
    ajaran.

Contoh:

``` text
Kelas:
[ Semua Kelas ▼ ]

Status:
[ Semua Status ▼ ]

[ Filter ]
```

------------------------------------------------------------------------

# 17. PAGINATION

Jika data siswa berjumlah ratusan atau ribuan, tabel tidak boleh
menampilkan semuanya sekaligus.

Gunakan pagination.

Contoh:

``` text
Menampilkan 1–10 dari 280 data

[ ‹ ] [1] [2] [3] ... [28] [›]
```

Dalam Laravel dapat menggunakan:

``` php
Student::paginate(10);
```

Jumlah 10 dapat disesuaikan kebutuhan UI.

------------------------------------------------------------------------

# 18. DATA SISWA PADA ROLE WALI KELAS

Ini merupakan bagian penting dari desain BIDUK.

Aturan yang digunakan:

> **Satu guru hanya menjadi wali kelas untuk satu kelas pada satu tahun
> ajaran aktif.**

Karena itu, Wali Kelas **tidak perlu memilih beberapa kelas**.

### Alur yang benar:

``` text
Login Wali Kelas
       ↓
Dashboard
       ↓
Data Siswa
       ↓
Langsung membuka kelas tanggung jawab
       ↓
Daftar Siswa
```

Contoh:

``` text
Guru:
Ibu Siti Nurhayati

Wali Kelas:
4A
```

Ketika membuka:

``` text
Master Data > Data Siswa
```

langsung tampil:

``` text
Master Data > Data Siswa > Kelas 4A

Data Siswa Kelas 4A
```

Tidak ada halaman:

``` text
Pilih Kelas
```

karena kelas sudah diketahui dari relasi wali kelas.

------------------------------------------------------------------------

# 19. PEMBATASAN DATA WALI KELAS

Wali Kelas hanya dapat melihat siswa yang menjadi bagian kelasnya.

Misalnya:

``` text
Ibu Siti
Wali Kelas 4A
```

Maka:

``` text
BISA:
✓ Siswa kelas 4A
✓ Data induk siswa kelas 4A
✓ Nilai rapot siswa kelas 4A
✓ Laporan kelas 4A

TIDAK BISA:
✗ Siswa kelas 4B
✗ Siswa kelas 5A
✗ Mengelola user
✗ Mengubah role
✗ Mengelola kelas global
```

Ini harus ditegakkan di backend, bukan hanya dengan menyembunyikan menu.

------------------------------------------------------------------------

# 20. RELASI WALI KELAS DAN TAHUN AJARAN

Aturan satu guru satu kelas berlaku dalam konteks tahun ajaran.

Contoh:

``` text
Tahun Ajaran 2025/2026
Guru A → Wali Kelas 4A

Tahun Ajaran 2026/2027
Guru A → Wali Kelas 5A
```

Hal ini penting agar riwayat sekolah tetap tersimpan.

Jangan membuat aturan global:

``` text
Guru A hanya boleh menjadi wali kelas 4A selamanya.
```

Yang benar:

``` text
Guru A hanya boleh memiliki satu kelas sebagai wali kelas
pada satu tahun ajaran tertentu.
```

------------------------------------------------------------------------

# 21. DATA INDUK SISWA

Data Induk Siswa adalah halaman lanjutan dari Data Siswa.

Ketika Wali Kelas memilih:

``` text
Aksi → Lengkapi Data Induk
```

sistem membuka:

``` text
Master Data > Data Siswa > [Nama Siswa] > Data Induk
```

Tujuan halaman ini adalah melengkapi informasi siswa secara lebih
detail.

------------------------------------------------------------------------

# 22. KONSEP DATA INDUK SISWA

Data induk dapat dikelompokkan berdasarkan bagian buku induk.

Contoh kelompok:

1.  Identitas Peserta Didik
2.  Data Orang Tua/Wali
3.  Data Kesehatan
4.  Riwayat Pendidikan
5.  Perkembangan/Prestasi
6.  Data Tambahan
7.  Dokumen pendukung jika diperlukan

Struktur final field harus disesuaikan dengan format buku induk sekolah
yang akan digunakan sebagai acuan aplikasi.

------------------------------------------------------------------------

# 23. HALAMAN DETAIL DATA INDUK

Contoh tampilan:

``` text
Data Induk Siswa

┌─────────────────────────────────────────┐
│ Foto Siswa                              │
│                                         │
│ Ahmad Ramadhan                          │
│ NIS  : 24001                            │
│ NISN : 0101010101                       │
│ Kelas: 4A                               │
└─────────────────────────────────────────┘

[ Biodata ]
[ Orang Tua/Wali ]
[ Kesehatan ]
[ Riwayat Sekolah ]
[ Prestasi ]
[ Lainnya ]
```

Pendekatan tab digunakan agar form yang sangat panjang tidak menjadi
satu halaman yang melelahkan.

------------------------------------------------------------------------

# 24. PINTASAN INPUT NILAI RAPOT

Pada aksi siswa Wali Kelas tersedia pintasan:

``` text
Input Nilai Rapot
```

Alurnya:

``` text
Data Siswa
   ↓
Pilih Siswa
   ↓
Input Nilai Rapot
```

Sistem dapat otomatis membawa:

-   siswa;
-   kelas;
-   tahun ajaran aktif;
-   semester aktif.

Dengan demikian guru tidak perlu mengisi identitas yang sudah tersedia
secara manual.

------------------------------------------------------------------------

# 25. HUBUNGAN DATA SISWA DAN NILAI

Nilai rapot tidak boleh berdiri tanpa hubungan dengan siswa.

Konsep:

``` text
Siswa
  │
  ├── Tahun Ajaran
  ├── Semester
  ├── Kelas
  └── Nilai
        └── Mata Pelajaran
```

Contoh:

``` text
Ahmad Ramadhan
NIS: 24001
Kelas: 4A
Tahun Ajaran: 2026/2027
Semester: Ganjil

Bahasa Indonesia = 88
Matematika        = 85
IPAS              = 87
PJOK              = 92
```

------------------------------------------------------------------------

# 26. STATUS KELENGKAPAN NILAI

Pada halaman Nilai Rapot terdapat kolom:

``` text
No
NIS
NISN
Nama Siswa
Kelas
Jenis Kelamin
Status
Aksi
```

Status:

### Sudah Terisi

Semua nilai yang diwajibkan sudah dimasukkan.

### Belum

Belum ada nilai yang diinput.

### Belum Selesai

Sebagian nilai sudah diinput tetapi masih ada mata pelajaran yang belum
memiliki nilai.

Contoh:

``` text
Sudah Terisi
✓ Semua mata pelajaran sudah memiliki nilai

Belum
○ Belum ada nilai

Belum Selesai
◐ Sebagian sudah ada, sebagian belum
```

------------------------------------------------------------------------

# 27. DATA PEGAWAI

Data Pegawai digunakan untuk mengelola data tenaga pendidik dan tenaga
kependidikan.

Contoh kolom:

``` text
No
Nama
Foto
NIP
Jabatan
Kelas
Status
Aksi
```

Data pegawai menjadi sumber data untuk:

-   guru;
-   wali kelas;
-   kepala sekolah;
-   tata usaha;
-   pengguna sistem sesuai kebutuhan.

------------------------------------------------------------------------

# 28. DATA KELAS

Data Kelas digunakan untuk mendefinisikan kelas yang ada di sekolah.

Contoh:

``` text
No
Nama Kelas
Tingkat
Wali Kelas
Tahun Ajaran
Status
Aksi
```

Relasi kelas perlu memperhatikan tahun ajaran.

------------------------------------------------------------------------

# 29. AKADEMIK

Fitur Akademik:

``` text
Tahun Ajaran
Mata Pelajaran
Semester
Nilai Rapot
```

------------------------------------------------------------------------

# 30. TAHUN AJARAN

Kolom:

``` text
No
Tahun Ajaran
Tanggal Mulai
Tanggal Selesai
Status
Aksi
```

Contoh:

``` text
2026/2027
01-07-2026
30-06-2027
Aktif
```

Sebaiknya hanya ada satu tahun ajaran aktif pada waktu yang sama.

------------------------------------------------------------------------

# 31. MATA PELAJARAN

Kolom:

``` text
No
Mata Pelajaran
Kode
Jenis
Aksi
```

Jenis dapat disesuaikan dengan kurikulum sekolah.

------------------------------------------------------------------------

# 32. SEMESTER

Kolom:

``` text
No
Semester
Tahun Ajaran
Aksi
```

Contoh:

``` text
1 | Ganjil | 2026/2027
2 | Genap  | 2026/2027
```

Sesuai rancangan UI yang telah ditetapkan, pada halaman daftar Semester
**tidak perlu menampilkan tombol Tambah Semester** jika pengelolaannya
memang dibuat otomatis atau dikendalikan melalui proses tertentu.

------------------------------------------------------------------------

# 33. NILAI RAPOT

Halaman nilai:

``` text
No
NIS
NISN
Nama Siswa
Kelas
Jenis Kelamin
Status
Aksi
```

Aksi:

``` text
[ Input Nilai ]
```

Nilai harus terhubung dengan:

-   siswa;
-   mata pelajaran;
-   kelas;
-   tahun ajaran;
-   semester.

------------------------------------------------------------------------

# 34. LAPORAN

Laporan BIDUK:

``` text
Buku Induk
Rekap Absensi
Rekap Rapot
Rekap Prestasi
```

Laporan dapat menyediakan:

-   filter;
-   preview;
-   cetak;
-   export PDF;
-   export Excel jika dibutuhkan.

------------------------------------------------------------------------

# 35. BUKU INDUK

Buku Induk merupakan laporan yang mengambil data dari berbagai bagian.

Contoh sumber:

``` text
Data Siswa
     +
Data Induk
     +
Kelas
     +
Tahun Ajaran
     +
Data Orang Tua
     +
Kesehatan
     +
Prestasi
     +
Akademik
```

Kemudian sistem menghasilkan format Buku Induk.

------------------------------------------------------------------------

# 36. REKAP ABSENSI

Rekap absensi menampilkan ringkasan kehadiran siswa.

Contoh:

``` text
NIS
Nama
Kelas
Sakit
Izin
Alpa
Total Kehadiran
```

------------------------------------------------------------------------

# 37. REKAP RAPOT

Rekap rapot mengambil nilai dari tabel nilai.

Filter:

-   tahun ajaran;
-   semester;
-   kelas;
-   siswa.

------------------------------------------------------------------------

# 38. REKAP PRESTASI

Rekap prestasi menampilkan prestasi siswa.

Contoh:

``` text
Nama Siswa
Kelas
Jenis Prestasi
Tingkat
Tanggal
Keterangan
```

------------------------------------------------------------------------

# 39. PENGATURAN

Pengaturan terdiri dari:

``` text
Manajemen User
Hak Akses Role
Profil Sekolah
Berita Sekolah
Log Aktivitas
```

------------------------------------------------------------------------

# 40. MANAJEMEN USER

Hanya role yang diberi kewenangan, terutama Super Admin, yang dapat
mengelola akun.

Data:

``` text
Nama
Username
Email
Role
Status
Aksi
```

Admin/Tata Usaha tidak diperbolehkan mengelola akun dan role pengguna
berdasarkan aturan sistem yang telah ditetapkan.

------------------------------------------------------------------------

# 41. HAK AKSES ROLE

Hak akses role menentukan fitur yang dapat digunakan.

Konsep:

``` text
Role
  ↓
Permission
  ↓
Menu / Action
```

Contoh:

``` text
Super Admin
→ siswa.view
→ siswa.create
→ siswa.update
→ siswa.delete
→ user.manage
→ role.manage
```

Sedangkan Kepala Sekolah:

``` text
dashboard.view
siswa.view
pegawai.view
kelas.view
nilai.view
laporan.view
```

tanpa permission create/update/delete.

------------------------------------------------------------------------

# 42. PROFIL SEKOLAH

Profil Sekolah digunakan untuk mengelola informasi yang muncul pada
landing page.

Contoh informasi:

-   nama sekolah;
-   logo;
-   alamat;
-   nomor telepon;
-   email;
-   visi;
-   misi;
-   tujuan;
-   identitas sekolah;
-   informasi lainnya.

------------------------------------------------------------------------

# 43. BERITA SEKOLAH

Berita sekolah dapat dibuat, diubah, dan dihapus oleh role yang diberi
izin.

Data berita:

``` text
Judul
Slug
Gambar
Isi
Tanggal Publikasi
Status
Penulis
```

Landing page menampilkan berita yang statusnya dipublikasikan.

------------------------------------------------------------------------

# 44. LOG AKTIVITAS

Log Aktivitas mencatat aktivitas penting.

Contoh:

``` text
User: Super Admin
Aktivitas: Menambahkan siswa
Data: Ahmad Ramadhan
Waktu: 10 Agustus 2026 10:15
```

Aktivitas yang dapat dicatat:

-   login;
-   logout;
-   tambah data;
-   edit data;
-   hapus data;
-   import;
-   export;
-   perubahan role;
-   perubahan hak akses.

------------------------------------------------------------------------

# 45. LANDING PAGE

Landing page merupakan halaman publik sekolah.

Struktur yang telah ditentukan:

``` text
HEADER
│
├── Beranda
├── Profil Sekolah
│   ├── Informasi Sekolah
│   ├── Visi dan Misi
│   ├── Tujuan Sekolah
│   └── Prestasi Sekolah
│
├── Ekstrakurikuler
│
└── Login

HERO SECTION

KONTEN

FOOTER
├── Kontak
├── Alamat
├── Telepon
├── Email
└── Informasi sekolah
```

Menu yang tidak dimasukkan:

-   Sejarah
-   Fasilitas
-   Informasi Akademik
-   Galeri

------------------------------------------------------------------------

# 46. DATABASE

Database yang digunakan dapat menggunakan MySQL/MariaDB.

Rancangan tabel utama:

``` text
users
roles
permissions
role_permissions

students
student_details
student_parents
student_health
student_education_histories
student_achievements

employees
classes
class_teacher_assignments

academic_years
subjects
semesters
grades

attendance
achievements

school_profiles
news
activity_logs
```

Nama tabel dapat disesuaikan ketika implementasi.

------------------------------------------------------------------------

# 47. TABEL STUDENTS

Contoh struktur:

``` text
students
--------------------------------
id
nis
nisn
name
gender
class_id
academic_year_id
status
created_at
updated_at
deleted_at
```

Catatan:

`class_id` dan `academic_year_id` perlu dirancang hati-hati.

Jika penempatan siswa berubah setiap tahun, lebih baik memisahkan:

``` text
students
```

sebagai identitas permanen siswa dan:

``` text
student_class_assignments
```

sebagai riwayat penempatan siswa.

Pendekatan ini lebih kuat untuk menyimpan histori.

------------------------------------------------------------------------

# 48. REKOMENDASI STRUKTUR SISWA YANG LEBIH BAIK

Daripada:

``` text
students
 └── class_id
```

lebih fleksibel:

``` text
students
   │
   └── student_class_assignments
             │
             ├── class_id
             ├── academic_year_id
             └── status
```

Contoh:

``` text
Ahmad
2025/2026 → Kelas 3A
2026/2027 → Kelas 4A
2027/2028 → Kelas 5A
```

Dengan begitu histori siswa tetap ada.

------------------------------------------------------------------------

# 49. TABEL CLASSES

Contoh:

``` text
classes
------------------------
id
name
grade_level
created_at
updated_at
```

Contoh:

``` text
4A
4B
5A
5B
```

------------------------------------------------------------------------

# 50. TABEL WALI KELAS

Karena aturan sistem adalah satu guru satu kelas pada satu tahun ajaran,
dapat dibuat tabel:

``` text
class_teacher_assignments
--------------------------------
id
employee_id
class_id
academic_year_id
created_at
updated_at
```

Aturan unik:

``` text
employee_id + academic_year_id
```

harus unik jika satu guru hanya boleh menjadi wali kelas satu kelas pada
tahun ajaran tersebut.

Dan:

``` text
class_id + academic_year_id
```

juga sebaiknya unik agar satu kelas tidak memiliki dua wali kelas pada
tahun ajaran aktif.

------------------------------------------------------------------------

# 51. TABEL DATA INDUK

Karena data induk dapat sangat banyak, jangan memasukkan semua field ke
tabel `students`.

Contoh:

``` text
student_details
-----------------------------
id
student_id
birth_place
birth_date
religion
citizenship
address
phone
family_status
child_order
siblings_count
created_at
updated_at
```

Data orang tua:

``` text
student_parents
-----------------------------
id
student_id
father_name
father_nik
father_occupation
mother_name
mother_nik
mother_occupation
guardian_name
guardian_relationship
created_at
updated_at
```

Data kesehatan:

``` text
student_health
-----------------------------
id
student_id
blood_type
height
weight
health_notes
created_at
updated_at
```

Struktur akhir perlu disesuaikan dengan format buku induk sekolah.

------------------------------------------------------------------------

# 52. TABEL ACADEMIC YEARS

``` text
academic_years
--------------------------------
id
name
start_date
end_date
status
created_at
updated_at
```

Contoh:

``` text
2026/2027
2027/2028
```

Status:

``` text
active
inactive
```

------------------------------------------------------------------------

# 53. TABEL SUBJECTS

``` text
subjects
--------------------------------
id
name
code
type
status
created_at
updated_at
```

------------------------------------------------------------------------

# 54. TABEL SEMESTERS

``` text
semesters
--------------------------------
id
academic_year_id
name
created_at
updated_at
```

------------------------------------------------------------------------

# 55. TABEL GRADES

Contoh:

``` text
grades
--------------------------------
id
student_id
subject_id
class_id
academic_year_id
semester_id
score
predicate
description
created_at
updated_at
```

Unique constraint dapat mempertimbangkan:

``` text
student_id
subject_id
academic_year_id
semester_id
```

agar satu siswa tidak memiliki dua nilai untuk mata pelajaran yang sama
pada semester dan tahun ajaran yang sama.

------------------------------------------------------------------------

# 56. RELASI DATABASE DATA SISWA

Gambaran:

``` text
students
   │
   ├──────── student_details
   │
   ├──────── student_parents
   │
   ├──────── student_health
   │
   ├──────── student_class_assignments
   │                 │
   │                 ├── classes
   │                 └── academic_years
   │
   ├──────── grades
   │             ├── subjects
   │             ├── semesters
   │             └── academic_years
   │
   ├──────── attendance
   │
   └──────── achievements
```

------------------------------------------------------------------------

# 57. FULLSTACK

## Frontend

Teknologi:

-   HTML
-   CSS
-   Bootstrap
-   JavaScript
-   Blade Laravel

Bootstrap digunakan untuk:

-   layout;
-   grid;
-   table;
-   modal;
-   form;
-   button;
-   navbar/sidebar;
-   responsive design.

JavaScript digunakan untuk:

-   interaksi sidebar;
-   modal;
-   konfirmasi;
-   filter;
-   validasi tambahan;
-   preview;
-   interaksi dinamis.

------------------------------------------------------------------------

# 58. BACKEND

Backend menggunakan Laravel.

Komponen:

``` text
Routes
Controllers
Models
Migrations
Middleware
Policies
Requests
Services
Views
```

Contoh:

``` text
DataSiswaController
Student model
StudentDetail model
StudentClassAssignment model
```

------------------------------------------------------------------------

# 59. CONTROLLER DATA SISWA

Contoh method:

``` text
index()
create()
store()
show()
edit()
update()
destroy()
```

Untuk wali kelas, dapat dibuat endpoint khusus atau controller yang sama
dengan authorization berbeda.

Contoh konsep:

``` php
public function index()
{
    // Super Admin:
    // seluruh siswa

    // Wali Kelas:
    // hanya siswa pada kelas tanggung jawab
}
```

Namun filter akses harus ditentukan oleh backend.

------------------------------------------------------------------------

# 60. AUTHORIZATION

Jangan melakukan:

``` php
if (auth()->user()->role == 'guru') {
    // sembunyikan menu
}
```

saja.

Harus ada pembatasan query.

Contoh konsep:

``` php
$students = Student::whereHas(
    'classAssignments',
    function ($query) use ($classId) {
        $query->where('class_id', $classId);
    }
)->get();
```

Kemudian middleware/policy memastikan guru tidak dapat mengganti
`class_id` melalui request untuk melihat kelas lain.

------------------------------------------------------------------------

# 61. KEAMANAN DATA SISWA

Karena data siswa merupakan data penting, aplikasi perlu:

-   authentication;
-   authorization;
-   validation;
-   CSRF protection;
-   password hashing;
-   session protection;
-   audit/log;
-   pembatasan upload;
-   validasi file Excel;
-   validasi file foto;
-   backup database.

Password harus disimpan dalam bentuk hash, bukan teks biasa.

------------------------------------------------------------------------

# 62. IMPORT EXCEL

Super Admin dapat mengimpor data siswa melalui Excel.

Contoh kolom Excel:

``` text
NIS
NISN
Nama Peserta Didik
Jenis Kelamin
Kelas
```

Sebelum memasukkan data:

``` text
Upload Excel
      ↓
Validasi format
      ↓
Validasi kolom
      ↓
Validasi NIS/NISN
      ↓
Preview
      ↓
Konfirmasi
      ↓
Import
```

Sebaiknya sistem memberikan preview sebelum data benar-benar dimasukkan.

------------------------------------------------------------------------

# 63. EXPORT EXCEL

Super Admin dapat melakukan export berdasarkan filter.

Contoh:

``` text
Kelas: 4A
Status: Aktif

[ Export Excel ]
```

Data yang diekspor mengikuti hasil filter.

------------------------------------------------------------------------

# 64. SOFT DELETE

Untuk data siswa, soft delete sangat disarankan jika sistem perlu
menjaga histori.

Daripada:

``` text
DELETE permanen
```

sistem dapat menggunakan:

``` text
deleted_at
```

Dengan begitu data historis dapat dipertahankan.

------------------------------------------------------------------------

# 65. STRUKTUR PROJECT LARAVEL

Contoh:

``` text
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── StudentController.php
│   │   ├── StudentDetailController.php
│   │   ├── EmployeeController.php
│   │   ├── ClassController.php
│   │   ├── AcademicYearController.php
│   │   ├── SubjectController.php
│   │   ├── SemesterController.php
│   │   └── GradeController.php
│   │
│   ├── Requests/
│   └── Middleware/
│
├── Models/
│   ├── User.php
│   ├── Student.php
│   ├── StudentDetail.php
│   ├── Employee.php
│   ├── SchoolClass.php
│   ├── AcademicYear.php
│   ├── Subject.php
│   ├── Semester.php
│   └── Grade.php
│
└── Policies/

database/
├── migrations/
└── seeders/

resources/
└── views/
    ├── layouts/
    ├── dashboard/
    ├── students/
    ├── employees/
    ├── classes/
    ├── academics/
    ├── reports/
    └── settings/
```

------------------------------------------------------------------------

# 66. ROUTE DATA SISWA

Contoh:

``` php
Route::resource('students', StudentController::class);
```

Route dapat dibatasi menggunakan middleware.

Contoh konsep:

``` php
Route::middleware(['auth', 'permission:student.view'])
    ->group(function () {

        Route::get('/students', ...);
    });
```

Untuk Wali Kelas, route yang sama dapat menggunakan authorization
berdasarkan kelas.

------------------------------------------------------------------------

# 67. UI DATA SISWA

Desain halaman mengikuti pola dashboard BIDUK yang sudah dibuat.

Sidebar:

``` text
BIDUK SDN 204

Dashboard

Master Data
  Data Siswa
  Data Pegawai
  Data Kelas

Akademik
  Tahun Ajaran
  Mata Pelajaran
  Semester
  Nilai Rapot

Laporan
  Buku Induk
  Rekap Absensi
  Rekap Rapot
  Rekap Prestasi

Pengaturan
  ...
```

Content:

``` text
Master Data > Data Siswa

Data Siswa

[Tambah Siswa] [Import Excel] [Export Excel]

[Search] [Filter]

TABLE

Pagination
```

Warna visual mengikuti identitas hijau BIDUK yang telah dirancang.

------------------------------------------------------------------------

# 68. PERBEDAAN UI SUPER ADMIN DAN WALI KELAS

## Super Admin

``` text
Data Siswa

[Tambah Siswa]
[Import Excel]
[Export Excel]

Search
Filter

Semua siswa
```

## Wali Kelas

``` text
Data Siswa Kelas 4A

Search
Filter jika diperlukan

Hanya siswa kelas 4A
```

Tidak ada tombol pengelolaan global seperti:

``` text
Import seluruh siswa
Kelola kelas lain
```

jika tidak termasuk kewenangan Wali Kelas.

------------------------------------------------------------------------

# 69. AKSI DATA SISWA WALI KELAS

Pada aksi:

``` text
⋮
```

dapat ditampilkan:

``` text
Lihat Detail
Lengkapi Data Induk
Input Nilai Rapot
```

Alurnya:

``` text
Data Siswa
   │
   ├── Lihat Detail
   │
   ├── Lengkapi Data Induk
   │
   └── Input Nilai Rapot
```

------------------------------------------------------------------------

# 70. FLOW DATA SISWA

## Super Admin

``` text
Login
 ↓
Dashboard
 ↓
Master Data
 ↓
Data Siswa
 ↓
Lihat seluruh siswa
 ↓
Tambah / Lihat / Edit / Hapus
 ↓
Data Induk
 ↓
Nilai / Laporan
```

## Wali Kelas

``` text
Login
 ↓
Dashboard
 ↓
Data Siswa
 ↓
Sistem mengetahui kelas wali kelas
 ↓
Tampilkan siswa kelas tersebut
 ↓
Pilih siswa
 ├── Lihat Detail
 ├── Lengkapi Data Induk
 └── Input Nilai Rapot
```

------------------------------------------------------------------------

# 71. CONTOH SKENARIO

Misalnya:

``` text
Guru:
Ibu Siti Nurhayati

Tahun Ajaran:
2026/2027

Wali Kelas:
4A
```

Saat Ibu Siti membuka Data Siswa:

``` text
Master Data > Data Siswa

Data Siswa Kelas 4A

28 siswa
```

Ibu Siti memilih Ahmad.

Muncul:

``` text
Ahmad Ramadhan
NIS  : 24001
NISN : 0101010101
Kelas: 4A

[ Lihat Detail ]
[ Lengkapi Data Induk ]
[ Input Nilai Rapot ]
```

Jika memilih:

``` text
Lengkapi Data Induk
```

maka sistem membuka halaman data induk Ahmad.

Jika memilih:

``` text
Input Nilai Rapot
```

maka sistem membuka input nilai Ahmad dengan konteks kelas, tahun
ajaran, dan semester yang sesuai.

------------------------------------------------------------------------

# 72. ATURAN BISNIS DATA SISWA

Aturan yang harus diterapkan:

1.  NIS tidak boleh kosong.
2.  NIS tidak boleh duplikat sesuai kebijakan sekolah.
3.  NISN tidak boleh duplikat sesuai kebijakan sekolah.
4.  Nama siswa wajib diisi.
5.  Jenis kelamin wajib dipilih.
6.  Kelas harus berasal dari data kelas.
7.  Wali kelas hanya melihat siswa kelas tanggung jawabnya.
8.  Satu guru hanya menjadi wali kelas satu kelas pada satu tahun
    ajaran.
9.  Satu kelas hanya mempunyai satu wali kelas pada satu tahun ajaran
    aktif.
10. Tahun ajaran memiliki status aktif/nonaktif.
11. Nilai harus terhubung dengan siswa.
12. Nilai harus terhubung dengan mata pelajaran.
13. Nilai harus terhubung dengan semester.
14. Nilai harus terhubung dengan tahun ajaran.
15. Penghapusan data harus mempertimbangkan histori.
16. Hak akses harus diperiksa di backend.

------------------------------------------------------------------------

# 73. KEPALA SEKOLAH DAN DATA SISWA

Kepala Sekolah dapat melihat data siswa untuk monitoring.

Namun:

``` text
View = boleh
Create = tidak
Update = tidak
Delete = tidak
```

Contoh:

``` text
Kepala Sekolah
     ↓
Data Siswa
     ↓
Lihat
     ↓
Detail Siswa
     ↓
Monitoring
```

Tidak terdapat tombol:

``` text
Tambah
Edit
Hapus
```

------------------------------------------------------------------------

# 74. TATA USAHA DAN DATA SISWA

Tata Usaha memiliki kewenangan mengelola data siswa.

Dapat:

``` text
Tambah
Lihat
Edit
Hapus
Import
Export
```

sesuai hak akses yang ditentukan.

Namun:

``` text
Tidak dapat:
Manajemen User
Hak Akses Role
```

------------------------------------------------------------------------

# 75. AUDIT DATA SISWA

Setiap perubahan penting dapat dicatat.

Contoh:

``` text
10-08-2026 10:30
Super Admin
Mengubah data siswa
Ahmad Ramadhan
```

Contoh:

``` text
10-08-2026 10:45
Ibu Siti Nurhayati
Melengkapi data induk
Ahmad Ramadhan
```

Ini membantu ketika terjadi kesalahan data.

------------------------------------------------------------------------

# 76. STRATEGI IMPLEMENTASI

Urutan pengerjaan yang disarankan:

## Tahap 1

Setup:

-   Laravel
-   PHP
-   Composer
-   Node/NPM
-   Bootstrap
-   MySQL
-   Git/GitHub

## Tahap 2

Authentication:

-   login;
-   logout;
-   role.

## Tahap 3

Database inti:

-   users;
-   roles;
-   permissions;
-   academic_years;
-   employees;
-   classes;
-   students.

## Tahap 4

Master Data:

-   Data Siswa;
-   Data Pegawai;
-   Data Kelas.

## Tahap 5

Akademik:

-   Tahun Ajaran;
-   Mata Pelajaran;
-   Semester;
-   Nilai Rapot.

## Tahap 6

Data Induk:

-   biodata;
-   orang tua;
-   kesehatan;
-   riwayat;
-   prestasi.

## Tahap 7

Laporan.

## Tahap 8

Pengaturan.

## Tahap 9

Landing page.

## Tahap 10

Testing dan deployment.

------------------------------------------------------------------------

# 77. PRIORITAS PENGEMBANGAN DATA SISWA

Untuk fitur Data Siswa, urutan paling aman:

``` text
1. Migration
2. Model
3. Relationship
4. Seeder
5. Controller
6. Form Request
7. Authorization/Policy
8. Route
9. Blade index
10. Blade create
11. Blade edit
12. Blade show
13. Search
14. Filter
15. Pagination
16. Import Excel
17. Export Excel
18. Data Induk
19. Pintasan Nilai
20. Testing
```

Jangan langsung membuat UI kompleks sebelum struktur database dan hak
akses benar.

------------------------------------------------------------------------

# 78. PEMBAGIAN PEKERJAAN GIT/GITHUB

Jika dikerjakan berdua, gunakan:

``` text
main
develop

feature/auth
feature/master-siswa
feature/master-pegawai
feature/master-kelas
feature/akademik
feature/nilai
feature/laporan
feature/pengaturan
feature/landing-page
```

Jangan coding langsung di `main`.

Alur:

``` text
develop
   ↓
feature/master-siswa
   ↓
coding
   ↓
commit
   ↓
push
   ↓
Pull Request
   ↓
review
   ↓
merge ke develop
```

------------------------------------------------------------------------

# 79. DEFINITION OF DONE DATA SISWA

Fitur Data Siswa dianggap selesai apabila:

### Database

-   [ ] Migration berhasil
-   [ ] Foreign key benar
-   [ ] Index benar
-   [ ] Constraint benar

### Backend

-   [ ] CRUD berhasil
-   [ ] Validation berhasil
-   [ ] Authorization berhasil
-   [ ] Search berhasil
-   [ ] Filter berhasil
-   [ ] Pagination berhasil

### Frontend

-   [ ] Halaman index
-   [ ] Tambah
-   [ ] Edit
-   [ ] Detail
-   [ ] Hapus
-   [ ] Modal konfirmasi
-   [ ] Responsive

### Import/Export

-   [ ] Import Excel
-   [ ] Validasi Excel
-   [ ] Preview
-   [ ] Export Excel

### Integrasi

-   [ ] Data siswa terhubung ke kelas
-   [ ] Data siswa terhubung ke tahun ajaran
-   [ ] Data induk terhubung ke siswa
-   [ ] Nilai terhubung ke siswa
-   [ ] Laporan dapat mengambil data siswa

### Role

-   [ ] Super Admin dapat mengelola
-   [ ] Tata Usaha dapat mengelola
-   [ ] Wali Kelas hanya melihat kelasnya
-   [ ] Kepala Sekolah hanya monitoring

------------------------------------------------------------------------

# 80. KESIMPULAN

BIDUK dirancang sebagai sistem informasi sekolah yang menggunakan konsep
**data terintegrasi dan berbasis role**.

Fitur Data Siswa menjadi salah satu pusat sistem karena data siswa
digunakan oleh:

``` text
Data Induk
Nilai Rapot
Absensi
Prestasi
Laporan
```

Pembagian akses harus mengikuti tanggung jawab masing-masing pengguna.

Yang paling penting dalam rancangan Data Siswa adalah:

``` text
SUPER ADMIN
→ seluruh siswa
→ CRUD

TATA USAHA
→ seluruh siswa
→ CRUD

WALI KELAS
→ hanya siswa kelasnya
→ kelola data yang menjadi kewenangannya

KEPALA SEKOLAH
→ monitoring
→ read only
```

Untuk Wali Kelas, aturan sistem adalah:

``` text
1 Guru
   ↓
1 Kelas
   ↓
1 Tahun Ajaran
```

Artinya ketika Wali Kelas masuk ke Data Siswa, sistem tidak perlu
meminta pengguna memilih kelas. Sistem mengambil kelas berdasarkan
assignment wali kelas pada tahun ajaran aktif dan langsung menampilkan
daftar siswa dari kelas tersebut.

Konsep ini membuat BIDUK lebih sederhana bagi pengguna, lebih aman dari
sisi akses data, dan lebih konsisten dengan proses kerja wali kelas.

------------------------------------------------------------------------

# CATATAN PENTING UNTUK TAHAP BERIKUTNYA

Dokumen ini adalah **rancangan awal/blueprint**. Field Data Induk Siswa
yang sangat rinci sebaiknya tidak langsung dianggap final sebelum
disesuaikan dengan format Buku Induk SDN 204 yang menjadi acuan.

Langkah berikutnya yang paling tepat adalah membuat dokumen khusus:

**`FITUR-DATA-SISWA.md`**

yang membahas secara lebih teknis:

1.  Detail semua field Data Siswa.
2.  Detail semua field Data Induk.
3.  Struktur tabel database lengkap.
4.  ERD.
5.  Relasi Laravel Model.
6.  Migration lengkap.
7.  Form tambah/edit.
8.  Hak akses per role.
9.  Flow Wali Kelas.
10. Flow Super Admin.
11. Flow Tata Usaha.
12. Flow Kepala Sekolah.
13. Search/filter/pagination.
14. Import Excel.
15. Export Excel.
16. Soft delete.
17. Activity log.
18. Controller.
19. Form Request validation.
20. Policy/middleware.
21. Route.
22. Struktur Blade.
23. Skenario pengujian.
24. Acceptance criteria.
