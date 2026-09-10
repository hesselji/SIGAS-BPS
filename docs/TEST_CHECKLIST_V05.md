# Smoke Test PENA MAS v0.5

## Setup
- [ ] Fresh DB berhasil import `mysql_schema_seed.sql`.
- [ ] Login ADMIN, USER, INCOMING berhasil.
- [ ] Timezone metadata sesuai WIB.

## 62710 / 62711
- [ ] Generate 62710 pertama -> sequence 001.
- [ ] Generate 62711 pertama -> sequence 001.
- [ ] Generate 62710 kedua -> sequence 002 (tidak ikut 62711).
- [ ] 62710 menolak tanggal kemarin.
- [ ] 62711 dapat memilih tanggal sebelumnya.

## Anggaran & KKA
- [ ] Ada Anggaran=Ya mengunci Fasilitatif.
- [ ] Ada Anggaran=Ya mengunci KU.
- [ ] Backend tetap memaksa Fasilitatif+KU bila POST dimodifikasi.
- [ ] Search KKA menemukan kode berdasarkan kata, misalnya `pajak` jika tersedia pada katalog.

## Hak akses
- [ ] USER hanya melihat Biasa pada form generate.
- [ ] USER tidak dapat POST sensitivity Rahasia secara manual.
- [ ] USER A melihat surat Biasa dari USER B pada Agenda.
- [ ] Dashboard USER A hanya menghitung surat buatan USER A.
- [ ] ADMIN melihat statistik global.
- [ ] USER tidak menemukan record Rahasia/Sangat Rahasia.

## Rahasia
- [ ] ADMIN generate Rahasia.
- [ ] Klik detail meminta password lagi.
- [ ] Password salah -> akses ditolak.
- [ ] Password benar -> detail terbuka.
- [ ] Audit access success/failure tercatat.
- [ ] Tidak ada password/password_hash di metadata audit.

## Surat Masuk
- [ ] Login `incoming@demo.local` langsung ke `/incoming`.
- [ ] Role INCOMING tidak dapat membuka `/dashboard` atau `/letters`.
- [ ] Tambah Surat Masuk berhasil.
- [ ] Tanggal pencatatan otomatis WIB.
- [ ] ADMIN dapat membuka Agenda Surat Masuk.

## Pengguna
- [ ] Admin buat USER/INCOMING.
- [ ] Admin edit role/tim.
- [ ] Bekukan -> akun gagal login.
- [ ] Aktifkan -> akun dapat login lagi.
- [ ] Reset password -> password baru berhasil.
- [ ] Delete -> akun hilang, histori tetap, email lama bisa dibuat lagi.
- [ ] Admin tidak bisa delete/freeze dirinya sendiri.

## UI
- [ ] Sidebar desktop fixed.
- [ ] Mobile hamburger bekerja.
- [ ] Tabel baru dapat discroll pada mobile.
- [ ] Dark/light mode tetap bekerja.
