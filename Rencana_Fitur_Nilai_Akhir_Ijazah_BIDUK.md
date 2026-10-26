# PROMPT PERBAIKAN FITUR KELOLA RIWAYAT KELAS PADA BIDUK

## 1. Tambahkan fitur Kelola Riwayat Kelas

Pada kartu kelas yang berstatus Belum Ada, sediakan tombol **Lengkapi Riwayat** atau **Kelola Riwayat**.

Saat tombol diklik, tampilkan formulir untuk mengisi:

- Tingkatan kelas.
- Tahun ajaran.
- Rombel.
- Status akhir tahun: Naik Kelas, Tinggal Kelas, atau Belum Diketahui.
- Catatan opsional.

Pilihan kelas, tahun ajaran, dan rombel harus mengambil data dari master yang sudah tersedia di BIDUK. Jangan membuat master baru atau mengulang proses pengaturan Admin.

Rombel harus mengikuti kelas dan tahun ajaran yang dipilih.

## 2. Pengelolaan riwayat kelas

Pengguna yang berwenang harus dapat:

- Menambahkan riwayat kelas yang belum tersedia.
- Melihat detail riwayat yang sudah tersimpan.
- Mengedit riwayat jika ada kesalahan berdasarkan data sekolah.
- Memeriksa tahun ajaran dan rombel setiap tingkatan kelas.

Setelah riwayat berhasil disimpan, kartu kelas harus menampilkan informasi yang benar, misalnya:

**Rapor Kelas 3**
- Status: Ada Data.
- Rombel: 3A.
- Tahun Ajaran: 2024/2025.
- Status nilai semester ganjil dan genap ditampilkan secara terpisah.

Jangan menganggap status riwayat kelas sudah lengkap berarti nilai rapornya juga sudah diisi.

## 3. Contoh penggunaan

Seorang siswa saat ini berada di kelas 5 pada tahun ajaran 2026/2027. Riwayat kelas 1 sampai kelas 4 belum tersedia karena siswa baru mulai menggunakan BIDUK ketika kelas 5.

Pengguna membuka kartu Rapor Kelas 3, menekan Lengkapi Riwayat, lalu mengisi:

- Kelas: 3.
- Tahun Ajaran: 2024/2025.
- Rombel: 3A.
- Status akhir tahun: Naik Kelas.

Setelah disimpan, riwayat kelas 3 tercatat untuk siswa tersebut.

Contoh ini hanya ilustrasi. Data sebenarnya harus sesuai dengan dokumen sekolah.

**Penting:** menyimpan riwayat kelas 3 tidak boleh mengubah penempatan siswa saat ini yang berada di kelas 5.

## 4. Aturan tahun ajaran dan kenaikan kelas

- Gunakan tahun ajaran yang dipilih berdasarkan riwayat sebenarnya.
- Jangan otomatis menggunakan tahun ajaran aktif untuk seluruh tingkatan kelas.
- Jangan menganggap siswa selalu naik kelas setiap tahun.
- Jika siswa tinggal kelas atau pindahan, simpan riwayat sesuai data yang benar.
- Jangan membuat riwayat resmi berdasarkan perkiraan tanpa verifikasi.
- Jangan menimpa riwayat lama saat siswa naik kelas.
- Cegah penempatan ganda yang tidak valid pada periode yang sama.

## 5. Integrasi nilai rapor

Riwayat kelas dan nilai rapor harus tetap terhubung dengan struktur data yang sudah ada.

- Nilai rapor tetap dikelola melalui fitur Nilai Rapor yang sekarang.
- Riwayat kelas tidak otomatis membuat nilai rapor.
- Jika nilai historis belum tersedia, status semester tetap Belum Diisi.
- Jika nilai lama tersedia, pengguna dapat mengisinya melalui mekanisme nilai rapor yang sudah ada.
- Jangan membuat fitur input nilai atau tabel nilai duplikat.
- Jangan mengubah fitur Rekap Rapor yang sudah berjalan.

## 6. Tampilan halaman

Pertahankan desain kartu riwayat rapor yang sekarang.

Perbaiki hal-hal berikut:

- Kartu yang belum memiliki riwayat menyediakan tombol Lengkapi Riwayat.
- Kartu yang sudah memiliki riwayat menyediakan opsi Lihat/Edit Riwayat sesuai hak akses.
- Tampilkan rombel tanpa pengulangan kata kelas, misalnya `Rombel: 3A`, bukan `Kelas 3 3A`.
- Tampilkan tahun ajaran berdasarkan riwayat yang tersimpan.
- Bedakan status penempatan kelas dan status pengisian nilai rapor.
- Pertahankan tampilan semester ganjil dan genap yang sudah ada.

## 7. Keamanan dan validasi

- Hanya pengguna yang memiliki izin yang dapat menambah atau mengubah riwayat.
- Validasi kelas, tahun ajaran, dan rombel di server.
- Pastikan riwayat tersimpan untuk siswa yang benar.
- Jangan mengubah kelas aktif siswa ketika mengedit riwayat lama.
- Jangan menghapus nilai rapor historis.
- Catat perubahan riwayat secara aman sesuai mekanisme audit yang tersedia.

## 8. Instruksi implementasi

Sebelum coding, periksa model, migration, controller, route, dan Blade yang digunakan halaman Riwayat Nilai Rapor Siswa.

Gunakan struktur dan master yang sudah tersedia. Jangan membuat fitur baru di luar kebutuhan ini, jangan membuat ulang Rekap Rapor, dan jangan mengubah fitur Nilai Akhir/Ijazah.

Implementasikan perubahan seminimal mungkin, kemudian uji penambahan, pengeditan, validasi, tampilan kartu, serta pastikan kelas siswa saat ini dan nilai rapor yang sudah ada tidak berubah.

Laporkan file yang diubah, hasil pengujian, dan cara menguji fitur melalui browser.
