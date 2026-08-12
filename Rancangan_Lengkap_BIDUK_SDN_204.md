# Rancangan Lengkap Aplikasi BIDUK SDN 204

**BIDUK — Buku Induk Digital SDN 204**

Dokumen ini menjadi acuan fungsional dan teknis untuk pengembangan aplikasi BIDUK berdasarkan pembahasan kebutuhan, struktur menu, rancangan Figma, role pengguna, alur data, dan kebutuhan Laravel.

## 1. Tujuan

BIDUK dirancang untuk membantu SDN 204 mengelola buku induk dan administrasi sekolah secara terpusat.

Tujuan utama:
- mengelola data siswa, pegawai, dan kelas;
- mengelola tahun ajaran, semester, mata pelajaran, dan nilai rapor;
- menyimpan data absensi dan prestasi;
- menghasilkan buku induk dan laporan;
- menyediakan website publik sekolah;
- menerapkan hak akses sesuai role;
- menyimpan histori siswa;
- menyediakan audit/log aktivitas.

---

## 2. Gambaran Sistem

BIDUK terdiri dari dua bagian:

1. **Website publik / Landing Page**
2. **Sistem internal / Dashboard setelah login**

```text
                         BIDUK SDN 204
                               |
                 +-------------+-------------+
                 |                           |
                 v                           v
          WEBSITE PUBLIK               SISTEM INTERNAL
          / LANDING PAGE                   / LOGIN
                 |                           |
       +---------+---------+         +-------+-------+--------+
       |         |         |         |       |       |        |
       v         v         v         v       v       v        v
    Beranda   Profil    Berita     Super    TU     Guru    Kepala
              Sekolah             Admin           Sekolah
                 |
               Galeri
```

---

# 3. Teknologi / Fullstack

## Backend
- PHP 8.4.x
- Laravel 13
- Composer
- Eloquent ORM
- Authentication
- Middleware
- Gate/Policy
- Validation

## Frontend
- HTML5
- CSS3
- Bootstrap 5
- Bootstrap Icons
- JavaScript
- Blade

## Database
- MySQL/MariaDB
- Laragon

## Development
- VS Code
- Git
- GitHub
- Browser modern

## Arsitektur

```text
Browser
   |
   v
Blade + Bootstrap + JavaScript
   |
   v
Laravel Route
   |
   v
Middleware / Authorization
   |
   v
Controller
   |
   v
Service / Business Logic
   |
   v
Eloquent Model
   |
   v
MySQL
```

Untuk versi awal tidak perlu REST API penuh. Blade + Laravel sudah cukup.

---

# 4. Role Pengguna

Ada empat role:

1. Super Admin
2. Admin / Tata Usaha
3. Guru
4. Kepala Sekolah

## Matriks Hak Akses

| Fitur | Super Admin | TU | Guru | Kepala Sekolah |
|---|---|---|---|---|
| Dashboard | Full | Full | Terbatas | Monitoring |
| Data Siswa | CRUD | CRUD | Kelas yang diajar | View |
| Data Pegawai | CRUD | CRUD | - | View |
| Data Kelas | CRUD | CRUD | Kelas terkait | View |
| Tahun Ajaran | CRUD | CRUD | - | View |
| Mata Pelajaran | CRUD | CRUD | - | View |
| Semester | CRUD | CRUD | - | View |
| Nilai Raport | CRUD | Tidak mengelola | Input/edit sesuai kewenangan | View |
| Buku Induk | Semua | Semua | Kelasnya | Semua |
| Rekap Absensi | Semua | Semua | Kelasnya | Semua |
| Rekap Raport | Semua | Semua | Kelasnya | Semua |
| Rekap Prestasi | Semua | Semua | Kelasnya | Semua |
| Manajemen User | CRUD | - | - | - |
| Hak Akses Role | CRUD | - | - | - |
| Profile Sekolah | CRUD | CRUD | - | View |
| Berita | CRUD | CRUD | - | View |
| Ekstrakurikuler | CRUD | CRUD | - | View |
| Galeri | CRUD | CRUD | - | View |
| Log Aktivitas | View | - | - | View |

**Catatan:** Guru tidak hanya dibatasi pada menu, tetapi juga pada data. Guru hanya dapat mengakses siswa/kelas yang menjadi kewenangannya.

---

# 5. Landing Page

Landing page hanya satu untuk semua pengunjung.

## Navigasi

```text
Beranda
Profil Sekolah
    - Informasi Sekolah
    - Visi & Misi
    - Struktur Organisasi
    - Prestasi Sekolah
    - Ekstrakurikuler
Berita
Galeri
Login
```

Tidak memasukkan:
- Sejarah Sekolah
- Fasilitas
- Informasi Akademik

## Beranda

### Hero
- logo;
- nama SDN 204;
- tagline;
- deskripsi;
- foto sekolah/kegiatan;
- tombol Profil Sekolah;
- tombol Login.

### Sekilas Sekolah
- foto;
- nama;
- deskripsi singkat;
- informasi ringkas.

### Berita Terbaru
Mengambil data dari `news`.

### Prestasi Sekolah
Menampilkan preview prestasi sekolah.

### Ekstrakurikuler
Informasi publik seperti:
- Pramuka;
- Seni;
- Olahraga;
- Keagamaan.

Ekstrakurikuler bukan modul akademik nilai.

### Galeri
Foto kegiatan sekolah.

### Footer
- nama sekolah;
- alamat;
- telepon;
- email;
- website;
- Instagram;
- Facebook;
- YouTube;
- copyright.

Data kontak sebaiknya berasal dari database, bukan hardcode.

---

# 6. Profil Sekolah

```text
Profil Sekolah
|
+-- Informasi Sekolah
+-- Visi & Misi
+-- Struktur Organisasi
+-- Prestasi Sekolah
+-- Ekstrakurikuler
```

Tidak ada:
- Sejarah;
- Fasilitas;
- Informasi Akademik.

---

# 7. Berita

Berita berdiri sendiri.

Fitur:
- daftar;
- detail;
- pencarian;
- pagination;
- thumbnail;
- draft/published;
- CRUD untuk role yang berwenang.

Hanya berita `Published` yang tampil publik.

---

# 8. Galeri

Fitur:
- daftar foto;
- upload;
- edit;
- hapus;
- publikasi;
- album jika diperlukan.

Versi sederhana cukup menggunakan satu tabel `galleries`. Jika kebutuhan berkembang, dapat dibuat `gallery_albums` dan `gallery_images`.

---

# 9. Login

Halaman login:
- username;
- password;
- tampil/sembunyikan password;
- lupa password;
- tombol Login.

Tidak perlu tombol "Login sebagai Kepala Sekolah". Role ditentukan oleh akun.

---

# 10. Dashboard

Dashboard merupakan ringkasan sistem, bukan tempat mengedit seluruh data.

Contoh:
- total siswa;
- total pegawai;
- total kelas;
- tahun ajaran aktif;
- siswa laki-laki/perempuan;
- status pengisian nilai;
- aktivitas terbaru.

Dashboard dapat disesuaikan per role.

---

# 11. Master Data Siswa

Data utama:

```text
id
nis
nisn
nik
name
nickname
gender
birth_place
birth_date
religion
address
kk_number
father_name
father_nik
father_job
mother_name
mother_nik
mother_job
guardian_name
guardian_phone
photo
status
created_at
updated_at
```

Status:
- Aktif;
- Pindah;
- Lulus;
- Alumni;
- Nonaktif.

Siswa pindah/lulus jangan dihapus karena data historis dibutuhkan.

## Detail Siswa

Dapat menampilkan:
- identitas;
- orang tua/wali;
- riwayat kelas;
- nilai;
- absensi;
- prestasi.

---

# 12. Master Data Pegawai

Mencakup guru dan tenaga kependidikan.

```text
id
nip
nuptk
name
gender
birth_place
birth_date
position
employee_type
employment_status
phone
email
address
photo
status
created_at
updated_at
```

Jenis:
- Guru;
- Tenaga Kependidikan;
- Kepala Sekolah.

---

# 13. Master Data Kelas

Kolom utama:

```text
id
academic_year_id
name
grade_level
homeroom_teacher_id
status
created_at
updated_at
```

Contoh:

```text
Kelas: 4A
Tingkat: 4
Wali Kelas: Bu Ani
Tahun Ajaran: 2026/2027
Status: Aktif
```

## Detail Kelas

Menampilkan:
- nama kelas;
- tingkat;
- tahun ajaran;
- wali kelas;
- jumlah siswa;
- daftar siswa.

Daftar:

| No | NIS | NISN | Nama | Jenis Kelamin | Status | Aksi |
|---|---|---|---|---|---|---|

Detail kelas fokus pada komposisi anggota kelas. Rekap nilai/absensi/prestasi dapat dibuka melalui fitur masing-masing.

---

# 14. Riwayat Kelas Siswa

Jangan menjadikan kelas sebagai atribut permanen siswa.

Gunakan:

```text
class_student
```

```text
id
class_id
student_id
status
entry_date
exit_date
created_at
updated_at
```

Contoh:

```text
2025/2026 -> 4A
2026/2027 -> 5A
2027/2028 -> 6A
```

Jika siswa pindah:
- status = Pindah;
- exit_date = tanggal pindah.

Histori tetap ada.

---

# 15. Akademik — Tahun Ajaran

Tampilan:

```text
No
Tahun Ajaran
Tanggal Mulai
Tanggal Selesai
Status
Aksi
```

Contoh:

```text
2026/2027
01-07-2026
30-06-2027
Aktif
```

Sebaiknya hanya ada satu tahun ajaran aktif.

---

# 16. Akademik — Mata Pelajaran

Tampilan:

```text
No
Mata Pelajaran
Kode
Kategori
Aksi
```

Kategori dapat digunakan untuk membedakan kebutuhan sekolah, misalnya:
- utama;
- muatan lokal;
- agama;
- Bahasa Inggris;
- PJOK;
- seni.

Jika kategori sudah mencukupi, tidak perlu menambah kolom `jenis` yang fungsinya sama.

---

# 17. Akademik — Semester

Tampilan:

```text
No
Semester
Tahun Ajaran
Aksi
```

Contoh:

```text
Ganjil | 2026/2027
Genap  | 2026/2027
```

Relasi:

```text
Tahun Ajaran
    |
    +-- Ganjil
    +-- Genap
```

---

# 18. Akademik — Nilai Raport

Tampilan utama:

```text
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
- Input Nilai.

Status:
- Sudah Terisi;
- Belum;
- Belum Selesai.

## Arti Status

`Belum`: belum ada nilai.

`Belum Selesai`: sebagian mata pelajaran sudah memiliki nilai.

`Sudah Terisi`: seluruh mata pelajaran wajib memiliki nilai.

Status sebaiknya dihitung otomatis dari database, bukan diketik manual.

---

# 19. Struktur Nilai

Nilai terkait:

```text
Siswa
Kelas
Semester
Mata Pelajaran
Guru
```

Contoh:

```text
Andi
2026/2027
Ganjil

Matematika       88
Bahasa Indonesia 90
IPAS             87
PJOK             92
```

---

# 20. Absensi

Absensi dibutuhkan karena terdapat laporan rekap absensi.

Absensi dapat dikelola dari konteks kelas/siswa tanpa harus menambah menu utama sidebar.

Contoh:

```text
Guru
 |
 v
Kelas 4A
 |
 v
Daftar Siswa
 |
 +-- Absensi
```

Status:
- Hadir;
- Sakit;
- Izin;
- Alpa.

---

# 21. Prestasi Siswa

Dikelola melalui detail siswa.

```text
id
student_id
name
category
level
organizer
achievement_date
rank
description
proof_file
created_by
created_at
updated_at
```

Contoh:
- Juara 1 Olimpiade Matematika;
- Akademik;
- Tingkat Kota;
- Tahun 2026.

**Prestasi siswa berbeda dengan Prestasi Sekolah pada landing page.**

---

# 22. Laporan Buku Induk

Buku induk adalah gabungan data:

```text
Identitas Siswa
Data Orang Tua/Wali
Alamat
Riwayat Kelas
Nilai
Absensi
Prestasi
```

Laporan mengambil data dari tabel yang sudah ada. Jangan membuat input data buku induk terpisah jika tidak diperlukan.

---

# 23. Rekap Absensi

Filter:
- tahun ajaran;
- semester;
- kelas;
- siswa;
- periode.

Contoh:

| No | Nama | Sakit | Izin | Alpa |
|---|---|---:|---:|---:|
| 1 | Andi | 2 | 1 | 0 |
| 2 | Budi | 0 | 2 | 1 |

Output:
- preview;
- print;
- PDF;
- Excel.

---

# 24. Rekap Raport

Filter:
- tahun ajaran;
- semester;
- kelas;
- siswa.

Contoh:

| No | NIS | Nama | Matematika | Bahasa Indonesia | IPAS |
|---|---|---|---:|---:|---:|

---

# 25. Rekap Prestasi

Filter:
- tahun;
- kelas;
- jenis;
- tingkat.

Output:

| No | Nama | Kelas | Prestasi | Tingkat | Tanggal | Peringkat |
|---|---|---|---|---|---|---|

---

# 26. Pengaturan — Manajemen User

Hanya Super Admin.

Kolom:

```text
No
Nama
Username
Role
Status Akun
Terakhir Login
Aksi
```

Aksi:
- detail;
- edit;
- aktif/nonaktif;
- reset password.

---

# 27. Pegawai vs User

Keduanya harus dipisahkan.

```text
EMPLOYEE
Bu Ani
NIP 123456
      |
      v
USER
username: buani
role: Guru
status: Aktif
```

`employees` menyimpan identitas orang. `users` menyimpan akun login.

---

# 28. Hak Akses Role

Gunakan:

```text
roles
permissions
role_permissions
```

Contoh:

```text
student.view
student.create
student.update
student.delete
```

Dengan ini hak akses lebih mudah dikembangkan.

---

# 29. Profile Sekolah

Data untuk landing page:

```text
school_name
npsn
nss
address
phone
email
website
logo
school_photo
vision
mission
organization_structure
instagram
facebook
youtube
```

Jika nomor telepon diubah dari dashboard, footer otomatis berubah.

---

# 30. Ekstrakurikuler

Sebagai konten publik:

```text
id
name
description
image
status
created_at
updated_at
```

Bukan modul nilai akademik.

---

# 31. Log Aktivitas

Untuk audit.

Contoh:

```text
10 Agustus 2026 10:15
Super Admin
Menambahkan data siswa
Andi Saputra
```

Field:

```text
id
user_id
action
module
description
ip_address
created_at
```

---

# 32. Rancangan Database

Tabel inti:

```text
users
roles
permissions
role_permissions

employees
students

academic_years
semesters
subjects
classes
class_student

grades
attendances
achievements

school_profiles
news
galleries
extracurriculars

activity_logs
```

---

# 33. Struktur `users`

```text
users
-------------------------
id
employee_id
username
password
role_id
status
last_login_at
remember_token
created_at
updated_at
```

`employee_id` dapat nullable jika ada akun yang tidak terkait langsung dengan pegawai.

---

# 34. Struktur `roles`

```text
roles
----------------
id
name
description
created_at
updated_at
```

Data awal:
- Super Admin;
- Admin;
- Guru;
- Kepala Sekolah.

---

# 35. Struktur `permissions`

```text
permissions
----------------
id
name
description
created_at
updated_at
```

---

# 36. Struktur `role_permissions`

```text
role_permissions
-------------------------
id
role_id
permission_id
created_at
updated_at
```

---

# 37. Struktur `employees`

```text
employees
-----------------------
id
nip
nuptk
name
gender
birth_place
birth_date
position
employee_type
employment_status
phone
email
address
photo
status
created_at
updated_at
```

---

# 38. Struktur `students`

```text
students
-----------------------
id
nis
nisn
nik
name
nickname
gender
birth_place
birth_date
religion
address
kk_number
father_name
father_nik
father_job
mother_name
mother_nik
mother_job
guardian_name
guardian_phone
photo
status
created_at
updated_at
```

---

# 39. Struktur `academic_years`

```text
academic_years
--------------------
id
name
start_date
end_date
status
created_at
updated_at
```

---

# 40. Struktur `semesters`

```text
semesters
--------------------
id
academic_year_id
name
created_at
updated_at
```

---

# 41. Struktur `subjects`

```text
subjects
------------------
id
code
name
category
status
created_at
updated_at
```

---

# 42. Struktur `classes`

```text
classes
-------------------------
id
academic_year_id
name
grade_level
homeroom_teacher_id
status
created_at
updated_at
```

`homeroom_teacher_id` mengarah ke pegawai yang berstatus guru.

---

# 43. Struktur `class_student`

```text
class_student
-------------------------
id
class_id
student_id
status
entry_date
exit_date
created_at
updated_at
```

Tabel ini menghubungkan siswa dan kelas serta menjaga histori.

---

# 44. Struktur `grades`

Untuk nilai akhir rapor sederhana:

```text
grades
--------------------------
id
student_id
class_id
semester_id
subject_id
teacher_id
score
description
created_at
updated_at
```

Jika kelak sekolah membutuhkan tugas, PTS, PAS, atau komponen nilai lain, struktur dapat diperluas.

---

# 45. Struktur `attendances`

```text
attendances
-----------------------
id
student_id
class_id
semester_id
date
status
note
created_by
created_at
updated_at
```

Status:
- Hadir;
- Sakit;
- Izin;
- Alpa.

---

# 46. Struktur `achievements`

```text
achievements
-------------------------
id
student_id
name
category
level
organizer
achievement_date
rank
description
proof_file
created_by
created_at
updated_at
```

---

# 47. Struktur `school_profiles`

```text
school_profiles
-------------------------
id
school_name
npsn
nss
address
phone
email
website
logo
school_photo
vision
mission
organization_structure
instagram
facebook
youtube
created_at
updated_at
```

---

# 48. Struktur `news`

```text
news
-----------------------
id
title
slug
thumbnail
content
published_at
status
author_id
created_at
updated_at
```

---

# 49. Struktur `galleries`

Versi sederhana:

```text
galleries
---------------------
id
title
description
image
published_at
status
created_at
updated_at
```

---

# 50. Struktur `extracurriculars`

```text
extracurriculars
------------------------
id
name
description
image
status
created_at
updated_at
```

---

# 51. Struktur `activity_logs`

```text
activity_logs
-------------------------
id
user_id
action
module
description
ip_address
created_at
```

---

# 52. ERD Konseptual

```text
ROLES
  |
  +----< USERS >---- EMPLOYEES
                         |
                         +----< CLASSES
                         |
                         +----< GRADES

ACADEMIC_YEARS
  |
  +----< SEMESTERS
  |
  +----< CLASSES

CLASSES
  |
  +----< CLASS_STUDENT >---- STUDENTS
                                  |
                                  +----< GRADES
                                  |
                                  +----< ATTENDANCES
                                  |
                                  +----< ACHIEVEMENTS

SUBJECTS
  |
  +----< GRADES

SEMESTERS
  |
  +----< GRADES
  |
  +----< ATTENDANCES

USERS
  |
  +----< ACTIVITY_LOGS

SCHOOL_PROFILES
NEWS
GALLERIES
EXTRACURRICULARS
```

---

# 53. Relasi Utama

## Siswa — Kelas
Many-to-many melalui `class_student`.

## Kelas — Tahun Ajaran
Banyak kelas berada pada satu tahun ajaran.

## Tahun Ajaran — Semester
Satu tahun ajaran memiliki semester Ganjil dan Genap.

## Siswa — Nilai
One-to-many.

## Mata Pelajaran — Nilai
One-to-many.

## Semester — Nilai
One-to-many.

## Siswa — Absensi
One-to-many.

## Siswa — Prestasi
One-to-many.

## Pegawai — User
Umumnya one-to-one untuk akun pegawai.

---

# 54. Data Scope Guru

Misalnya Bu Ani hanya berwenang atas 4A.

Maka:

```text
Guru
 |
 +--> Siswa 4A
 +--> Nilai 4A
 +--> Absensi 4A
 +--> Prestasi siswa 4A
 +--> Laporan 4A
```

Guru tidak boleh mengakses 5A/6A.

Pembatasan harus diterapkan di backend, bukan hanya menyembunyikan menu.

---

# 55. Import Excel

Karena data sekolah dapat banyak, import Excel sangat disarankan.

Alur:

```text
Upload Excel
      |
      v
Validasi Header
      |
      v
Validasi Data
      |
      v
Deteksi Duplikasi
      |
      v
Preview
      |
      v
Konfirmasi
      |
      v
Insert Database
```

Validasi minimal:
- NIS unik;
- NISN unik;
- field wajib;
- format tanggal;
- jenis kelamin;
- status.

---

# 56. Export

## Master Siswa
Excel.

## Master Pegawai
Excel.

## Buku Induk
Preview, print, PDF.

## Rekap Nilai
Preview, Excel, PDF.

## Rekap Absensi
Preview, Excel, PDF.

## Rekap Prestasi
Preview, Excel, PDF.

---

# 57. Struktur Laravel

```text
app/
|
+-- Models/
|   +-- User.php
|   +-- Role.php
|   +-- Permission.php
|   +-- Employee.php
|   +-- Student.php
|   +-- AcademicYear.php
|   +-- Semester.php
|   +-- Subject.php
|   +-- SchoolClass.php
|   +-- Grade.php
|   +-- Attendance.php
|   +-- Achievement.php
|   +-- SchoolProfile.php
|   +-- News.php
|   +-- Gallery.php
|   +-- Extracurricular.php
|   +-- ActivityLog.php
|
+-- Http/
|   +-- Controllers/
|   +-- Requests/
|
+-- Policies/
|
+-- Services/
```

---

# 58. Controller

```text
DashboardController
StudentController
EmployeeController
ClassController
AcademicYearController
SubjectController
SemesterController
GradeController
AttendanceController
AchievementController
UserController
RoleController
SchoolProfileController
NewsController
GalleryController
ExtracurricularController
ActivityLogController
ReportController
```

---

# 59. Blade

```text
resources/views/
|
+-- layouts/
|   +-- app.blade.php
|   +-- sidebar.blade.php
|   +-- navbar.blade.php
|   +-- footer.blade.php
|
+-- auth/
|   +-- login.blade.php
|
+-- landing/
|   +-- home.blade.php
|   +-- profile.blade.php
|   +-- news.blade.php
|   +-- gallery.blade.php
|
+-- dashboard/
|
+-- students/
+-- employees/
+-- classes/
+-- academic-years/
+-- subjects/
+-- semesters/
+-- grades/
+-- reports/
+-- settings/
```

---

# 60. Sidebar

Gunakan satu sidebar:

```text
sidebar.blade.php
```

Jangan membuat sidebar terpisah untuk setiap role jika desainnya sama.

Menu ditampilkan berdasarkan permission, tetapi backend tetap melakukan authorization.

---

# 61. Pola Halaman CRUD

Pola konsisten:

```text
[Judul Halaman]

[Search] [Filter]          [+ Tambah Data]

------------------------------------------------
| No | Data | Data | Status | Aksi |
------------------------------------------------
| 1  | ...  | ...  | Aktif  | ...  |
------------------------------------------------

Pagination
```

Halaman Figma sebaiknya mengikuti design system yang sama:
- warna;
- font;
- spacing;
- card;
- tombol;
- badge;
- tabel;
- modal;
- pagination.

---

# 62. Validasi

Backend wajib melakukan validation.

Contoh siswa:
- NIS wajib;
- NISN wajib;
- nama wajib;
- tanggal valid;
- NIS unik;
- NISN unik.

Frontend validation hanya pelengkap.

---

# 63. Keamanan

Minimal:
- password hashing;
- CSRF;
- authorization;
- validation;
- file validation;
- data scope guru;
- log aktivitas;
- session management.

Upload file harus memvalidasi:
- tipe file;
- ukuran;
- nama file;
- lokasi penyimpanan.

---

# 64. Logika Status Nilai

Misalnya ada 10 mata pelajaran wajib:

```text
0/10  -> Belum
4/10  -> Belum Selesai
10/10 -> Sudah Terisi
```

Status dihitung berdasarkan jumlah nilai yang tersedia.

---

# 65. Tahun Ajaran dan Semester Aktif

Sistem perlu mengetahui:

```text
Tahun Ajaran Aktif
Semester Aktif
```

Contoh:

```text
2026/2027
Ganjil
```

Input nilai dapat menggunakan periode aktif secara otomatis, sementara laporan historis tetap dapat memilih periode lama.

---

# 66. Histori

Jangan menghapus data historis secara sembarangan.

Jika siswa lulus:

```text
status = Lulus
```

Jika pindah:

```text
status = Pindah
exit_date = ...
```

Tujuan buku induk digital adalah menjaga histori.

---

# 67. Prestasi Sekolah vs Prestasi Siswa

## Prestasi Sekolah
Untuk landing page:

```text
SDN 204 Juara ...
```

## Prestasi Siswa
Untuk data internal:

```text
Andi - Juara 1 Olimpiade Matematika
```

Keduanya sebaiknya menggunakan data berbeda.

---

# 68. Urutan Pembangunan

## Tahap 1 — Environment
- PHP;
- Composer;
- Node/NPM;
- Laravel;
- MySQL;
- Laragon.

## Tahap 2 — Git
- repository;
- main;
- develop;
- feature branch.

## Tahap 3 — Database
Buat migration inti.

## Tahap 4 — Authentication
- login;
- logout;
- forgot password.

## Tahap 5 — Authorization
- role;
- permission;
- middleware;
- policy.

## Tahap 6 — Master Data

Urutan:

```text
Pegawai
Siswa
Tahun Ajaran
Semester
Mata Pelajaran
Kelas
Penempatan Siswa
```

## Tahap 7 — Transaksi

```text
Nilai
Absensi
Prestasi
```

## Tahap 8 — Laporan

```text
Buku Induk
Rekap Absensi
Rekap Raport
Rekap Prestasi
```

## Tahap 9 — Pengaturan

```text
Manajemen User
Hak Akses
Profile Sekolah
Berita
Galeri
Ekstrakurikuler
Log Aktivitas
```

## Tahap 10 — Landing Page

Hubungkan konten publik ke database.

## Tahap 11 — Testing

Test setiap role dan data scope.

---

# 69. Pembagian GitHub

Jangan membagi hanya berdasarkan frontend vs backend.

Gunakan modul:

```text
develop
|
+-- feature/auth-role
+-- feature/master-siswa
+-- feature/master-pegawai
+-- feature/master-kelas
+-- feature/akademik
+-- feature/nilai
+-- feature/laporan
+-- feature/landing-page
+-- feature/pengaturan
```

Setiap feature idealnya mencakup migration, model, controller, route, view, validation, dan authorization yang berkaitan.

---

# 70. Urutan Migration

Karena foreign key membutuhkan tabel induk, urutan konseptual:

```text
1. roles
2. permissions
3. role_permissions
4. employees
5. users
6. students
7. academic_years
8. semesters
9. subjects
10. classes
11. class_student
12. grades
13. attendances
14. achievements
15. school_profiles
16. news
17. galleries
18. extracurriculars
19. activity_logs
```

---

# 71. Pengujian Role

## Super Admin

Harus dapat:
- CRUD seluruh data;
- mengelola user;
- mengelola role;
- profile sekolah;
- log.

## TU

Dapat:
- siswa;
- pegawai;
- kelas;
- tahun ajaran;
- mata pelajaran;
- semester;
- laporan;
- profile sekolah;
- konten publik sesuai kewenangan.

Tidak:
- manajemen user;
- role;
- nilai rapor jika aturan bisnis final memang demikian.

## Guru

Dapat:
- data siswa yang menjadi kewenangannya;
- nilai;
- absensi;
- prestasi;
- laporan kelasnya.

Tidak:
- user management;
- role management;
- data kelas lain.

## Kepala Sekolah

Dapat:
- dashboard;
- melihat data;
- preview laporan;
- monitoring.

Tidak:
- create;
- update;
- delete.

---

# 72. Checklist Sebelum Coding

Kunci terlebih dahulu:

1. Nama role.
2. Permission.
3. Data siswa.
4. Data pegawai.
5. Struktur kelas.
6. Tahun ajaran.
7. Semester.
8. Mata pelajaran.
9. Struktur nilai.
10. Struktur absensi.
11. Struktur prestasi.
12. Relasi siswa-kelas.
13. Batas akses guru.
14. Status siswa.
15. Status nilai.
16. Konten landing page.
17. Profile sekolah.
18. Berita.
19. Galeri.
20. Ekstrakurikuler.
21. Log aktivitas.

---

# 73. Kesimpulan Arsitektur

```text
                         BIDUK
                           |
       +-------------------+-------------------+
       |                                       |
       v                                       v
 LANDING PAGE                            DASHBOARD
       |                                       |
       |                         +-------------+-------------+
       |                         |             |             |
       |                         v             v             v
       |                    MASTER DATA     AKADEMIK      LAPORAN
       |                         |             |             |
       |                         +-------------+-------------+
       |                                       |
       |                                       v
       |                                  PENGATURAN
       |
       +-- Beranda
       +-- Profil Sekolah
       +-- Berita
       +-- Galeri
       +-- Ekstrakurikuler
```

Inti relasi akademik:

```text
Tahun Ajaran
     |
     +---- Semester
     |
     +---- Kelas
              |
              +---- Wali Kelas
              |
              +---- Siswa
                       |
                       +---- Nilai
                       |
                       +---- Absensi
                       |
                       +---- Prestasi
```

Inti keamanan:

```text
USER
 |
 +-- ROLE
 |     |
 |     +-- PERMISSIONS
 |
 +-- DATA SCOPE
       |
       +-- Guru hanya dapat mengakses kelas/siswa yang menjadi kewenangannya
```

---

# 74. Prinsip Final yang Harus Dipertahankan

BIDUK jangan diperlakukan sebagai kumpulan halaman CRUD. Sistem harus dipandang sebagai alur:

```text
Master Data
     |
     v
Penempatan Siswa
     |
     v
Tahun Ajaran + Semester
     |
     v
Nilai / Absensi / Prestasi
     |
     v
Laporan
```

Sedangkan:

```text
Profile Sekolah
     |
     v
Landing Page
     |
     +-- Beranda
     +-- Profil
     +-- Berita
     +-- Galeri
     +-- Ekstrakurikuler
```

Sebelum implementasi besar dimulai, database, relasi, role, permission, dan data scope sebaiknya disepakati terlebih dahulu. Jika ada perubahan kebutuhan, ubah dulu rancangan bisnis dan relasinya, kemudian baru migration/model/controller/view.
