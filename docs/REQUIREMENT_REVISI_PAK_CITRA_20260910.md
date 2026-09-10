# Requirement Revisi PENA MAS - Konsultasi Pak Citra

## P0 - Core Surat Keluar

1. Sequence 62710 dan 62711 terpisah; masing-masing dapat mulai dari 001.
2. 62710 tidak menerima tanggal surat sebelum hari ini; 62711 fleksibel.
3. Ada Anggaran = Ya -> Fasilitatif + KU otomatis.
4. USER hanya membuat surat Biasa.
5. ADMIN dapat membuat Biasa, Rahasia, Sangat Rahasia.
6. Agenda USER melihat seluruh surat Biasa dari semua pembuat.
7. Dashboard USER adalah statistik pribadi; Dashboard ADMIN global.
8. Rahasia/Sangat Rahasia hanya dapat dibuat dan dilihat ADMIN.
9. ADMIN harus memasukkan kembali password sebelum detail Rahasia/Sangat Rahasia dibuka.

## P1 - Penyempurnaan

1. Search KKA menggunakan kata/kode/nama.
2. Admin reset password user.
3. Email akun yang sudah dihapus dapat digunakan kembali tanpa memutus histori FK.
4. Show/hide password.
5. Surat Masuk dengan petugas/role khusus.
6. ADMIN mempunyai akses Surat Keluar dan Surat Masuk.

## Surat Masuk

Data minimum:
- nomor surat dari pengirim;
- asal surat;
- perihal;
- kepada;
- tanggal surat;
- tanggal diterima;
- tanggal/waktu pencatatan otomatis;
- petugas pencatat;
- catatan opsional.

Role INCOMING tidak membutuhkan Dashboard Surat Keluar dan diarahkan langsung ke Agenda Surat Masuk.

## Roadmap, bukan core sprint

- Import master KKA/peraturan baru menggunakan template CSV.
- Generate dokumen/template surat dan export/print PDF.
