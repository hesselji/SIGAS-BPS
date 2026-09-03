# Agenda Surat BPS — Prototype MVP v0.1

Prototype web untuk mengubah alur Excel agenda surat menjadi aplikasi berbasis database.

> **PENTING:** rule penomoran, hak akses surat rahasia, sequence 62710/62711, dan master KKA pada versi ini masih **working set/dummy** berdasarkan hasil pemahaman awal terhadap Excel `Agenda Surat 2026.xlsx` dan penjelasan pembimbing lapangan. Jangan gunakan sebagai sistem produksi sebelum rule dikonfirmasi.

## Teknologi

- PHP 8.2+ Native OOP, pola MVC ringan
- MySQL / MariaDB (InnoDB)
- PDO prepared statements
- HTML + CSS responsive
- Vanilla JavaScript `fetch()`
- Session authentication + CSRF
- Database transaction + row locking (`FOR UPDATE`) untuk sequence nomor

Alasan dipilih untuk MVP: mudah dijalankan di Windows/XAMPP, tidak membutuhkan Composer/NPM, gampang dipindahkan ke server internal/shared hosting, dan cukup untuk membuktikan alur form → rule → nomor otomatis → database → dashboard.

## Fitur MVP

- Login ADMIN dan USER
- Generate nomor surat keluar otomatis
- Nomor unik dan sequence +1 per numbering rule/tahun
- Kondisi `Ada Anggaran` + `Jenis Arsip` menghasilkan scope YF/YS/TF/TS
- Cascading KKA Level 2 → Level 3
- Numbering rule configurable di database
- Record agenda surat keluar
- Search/filter
- Dashboard statistik
- Pembatalan nomor (ADMIN) tanpa menghapus record
- Audit log
- User management sederhana
- Responsive mobile

## Akun Demo

- Admin: `admin@demo.local` / `Admin123!`
- User: `user@demo.local` / `User123!`

## Setup Lokal — XAMPP (disarankan)

### 1. Persiapan

Pastikan XAMPP terpasang. Start **MySQL** dari XAMPP Control Panel. Apache tidak wajib jika memakai PHP built-in server.

Cek PHP:

```bat
C:\xampp\php\php.exe -v
```

### 2. Buat database

Buka `http://localhost/phpmyadmin` → **New** → buat database:

```text
agenda_surat_bps
```

Collation: `utf8mb4_unicode_ci`.

### 3. Import schema + seed

Pilih database `agenda_surat_bps` → **Import** → pilih:

```text
database/mysql_schema_seed.sql
```

Setelah berhasil harus ada tabel:

- work_teams
- users
- numbering_rules
- letter_types
- letter_sensitivities
- archive_types
- classifications
- number_sequences
- outgoing_letters
- audit_logs

### 4. Buat `.env`

Copy:

```bat
copy .env.example .env
```

Untuk XAMPP default, isi sudah cocok:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agenda_surat_bps
DB_USERNAME=root
DB_PASSWORD=
```

Jika MySQL kamu memakai password, isi `DB_PASSWORD`.

### 5. Jalankan aplikasi

Dari terminal di root project:

```bat
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public public\router.php
```

Buka:

```text
http://127.0.0.1:8080/login
```

## Cara Demo MVP

1. Login sebagai `user@demo.local`.
2. Klik **Generate Nomor**.
3. Pilih jenis surat, tim, SRIKANDI/Non Srikandi, tanggal, sifat.
4. Pilih **Ada Anggaran** dan **Jenis Arsip**.
5. Sistem menampilkan scope `YF/YS/TF/TS`.
6. Pilihan KKA Level 2 dimuat dari database.
7. Pilih KKA Level 2, lalu KKA Level 3.
8. Isi tujuan dan perihal.
9. Submit → server generate nomor unik dan menyimpan record.
10. Login Admin untuk melihat semua record dan pembatalan nomor.

Contoh working rule:

```text
B-001/62710/KU.000/2026
```

Komponen:

- `B` = prefix sifat surat
- `001` = sequence
- `62710` = unit code pada numbering rule
- `KU.000` = klasifikasi
- `2026` = tahun surat

## Kenapa sequence aman dari duplicate?

Generator menggunakan transaksi database dan `SELECT ... FOR UPDATE` pada row sequence. Dua user yang meminta nomor bersamaan tidak membaca sequence tanpa lock yang sama; transaksi pertama menyelesaikan nomor lebih dulu, lalu transaksi kedua memperoleh nomor berikutnya.

## Hal yang WAJIB dikonfirmasi sebelum produksi

1. Apakah sequence satu untuk semua surat atau terpisah per jenis/rule?
2. Kapan sequence reset?
3. Arti dan kondisi resmi 62710 vs 62711.
4. Format final setiap jenis surat termasuk Form Permintaan.
5. Prefix resmi untuk Biasa/Penting/Rahasia/Sangat Rahasia.
6. Apakah SRIKANDI memengaruhi penomoran atau hanya metadata?
7. Siapa yang boleh melihat Rahasia/Sangat Rahasia?
8. Apakah nomor batal tetap dianggap terpakai? Prototype: **ya**.
9. Apakah user biasa boleh melihat surat biasa milik user lain?
10. Master KKA final dan relasi kondisi YF/YS/TF/TS.

## Catatan Security

- Password di-hash BCRYPT.
- Query memakai prepared statement.
- Generate nomor menggunakan transaction + locking.
- CSRF diterapkan pada POST.
- Pembatalan hanya ADMIN pada prototype.
- Record batal tidak dihapus.
- `.env` jangan di-commit.

## Tahap Berikutnya

- Konfirmasi requirement dengan pembimbing lapangan.
- Import master KKA lengkap dari Excel ke database.
- Master data UI untuk admin.
- Role/permission lebih granular.
- Surat masuk.
- Disposisi.
- Template dokumen dan print/PDF.
- Deployment ke server internal BPS setelah review keamanan.
