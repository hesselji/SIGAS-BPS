# PENA MAS - Penomoran Agenda dan Manajemen Arsip Surat

> Rebranding aplikasi dari SIGAS-BPS menjadi **PENA MAS**. Basis fitur tetap Full KKA v0.3 dengan peningkatan account management, ganti password, timezone WIB, dan perbaikan responsif.

**PENA MAS** adalah prototype **Penomoran Agenda dan Manajemen Arsip Surat** untuk project Magang Mandiri di BPS Kota Palangka Raya.

Versi **v0.3** berfokus pada pengembangan engine penomoran dan master Kode Klasifikasi Arsip (KKA). Katalog klasifikasi diimpor dari dokumen **Kode.pdf** yang diberikan untuk project.

## Teknologi

- PHP 8.2+ Native OOP/MVC
- MySQL / MariaDB (InnoDB)
- HTML + CSS
- Vanilla JavaScript
- Session authentication + CSRF + prepared statement
- Database transaction + row locking (`FOR UPDATE`) untuk sequence nomor

## Fitur v0.3

- Login Admin / User
- Dashboard agenda
- Generate nomor surat keluar
- Sequence otomatis dan aman terhadap request bersamaan
- Katalog KKA penuh dari `Kode.pdf`
- 19 kelompok klasifikasi tercatat
- 613 item kode klasifikasi terdata
- Klasifikasi dipisahkan menjadi Substantif dan Fasilitatif
- Kode numerik 1-3 digit dinormalisasi ke tiga digit untuk KKA (`0 -> 000`, `10 -> 010`, `21 -> 021`)
- Kode 4 digit tetap dipertahankan (`1010`, `1011`, dst.)
- Live preview nomor mengikuti pattern jenis surat
- Working rule `62710`, `62711`, dan Form Permintaan
- Admin dapat mengubah pattern rule tanpa mengedit PHP
- Admin dapat mengubah mapping Jenis Surat -> Numbering Rule
- Admin dapat menelusuri katalog KKA
- Agenda, search/filter, detail, cancel nomor, audit log
- Login form tidak lagi mengisi akun demo otomatis
- Ganti password mandiri untuk setiap user
- Admin dapat edit, bekukan/aktifkan, dan soft-delete akun pengguna
- Timezone aplikasi/database diselaraskan ke WIB (UTC+7)
- Sidebar tetap fixed dan navigasi dapat di-scroll pada layar pendek

## Penting: status rule penomoran

`Kode.pdf` cukup untuk membangun **master klasifikasi KKA**, tetapi dokumen tersebut tidak sendiri menetapkan seluruh mapping jenis surat ke kode unit `62710/62711` atau seluruh variasi format nomor.

Karena itu v0.3 melakukan dua hal:

1. Mengimplementasikan katalog klasifikasi secara penuh sesuai data yang tersedia.
2. Menjadikan rule penomoran **configurable oleh Admin**, sehingga setelah BPS mengonfirmasi mapping resmi, sistem dapat disesuaikan melalui menu **Administrasi -> Aturan Nomor** tanpa merombak source code.

## Fresh Install

### 1. Start MySQL

XAMPP Control Panel:

```text
MySQL -> Start
```

### 2. Buat database

```sql
CREATE DATABASE agenda_surat_bps
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

### 3. Import schema

Import:

```text
database/mysql_schema_seed.sql
```

melalui phpMyAdmin, atau:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root agenda_surat_bps < database\mysql_schema_seed.sql
```

### 4. Buat `.env`

```powershell
copy .env.example .env
```

Default XAMPP:

```env
APP_NAME="PENA MAS"
APP_ENV=local
APP_URL=http://127.0.0.1:8080
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
DB_TIMEZONE=+07:00

DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agenda_surat_bps
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan

```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public public\router.php
```

Buka:

```text
http://127.0.0.1:8080/login
```

## Upgrade dari v0.2 tanpa menghapus agenda lama

Jangan import `mysql_schema_seed.sql` ke database yang sudah memiliki data agenda karena file fresh install melakukan DROP TABLE.

Untuk upgrade database v0.2, import hanya:

```text
database/migrations/20260905_v03_full_classifications.sql
```

Melalui terminal:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root agenda_surat_bps < database\migrations\20260905_v03_full_classifications.sql
```

Migration tersebut:

- membuat tabel `classification_groups` dan `classification_items`;
- memasukkan katalog KKA penuh;
- menambah/update working numbering rules;
- menambah/update jenis surat;
- tidak menghapus record `outgoing_letters` yang sudah ada.

## Akun demo

Admin:

```text
admin@demo.local
Admin123!
```

User:

```text
user@demo.local
User123!
```

Akun ini hanya untuk prototype. Hapus/ganti sebelum production.

## Katalog KKA

Admin dapat membuka:

```text
Administrasi -> Katalog KKA
```

Data machine-readable juga tersedia di:

```text
database/classification_catalog.json
```

Ringkasan katalog:

### Substantif

- PS - Perumusan Kebijakan di Bidang Statistik
- SS - Sensus Penduduk, Sensus Pertanian dan Sensus Ekonomi
- VS - Survei
- KS - Konsolidasi Data Statistik
- ES - Evaluasi dan Pelaporan (dicatat tetapi nonaktif karena rincian numerik tidak tersedia pada bagian utama dokumen sumber)

### Fasilitatif

- KU - Keuangan
- KP - Kepegawaian
- PR - Perencanaan
- HK - Hukum
- OT - Organisasi dan Tata Laksana
- HM - Hubungan Masyarakat
- KA - Kearsipan
- RT - Kerumahtanggaan
- PL - Perlengkapan
- DL - Pendidikan dan Latihan
- PK - Kepustakaan
- IF - Informatika
- PW - Pengawasan
- TS - Transformasi Statistik

## Rule Penomoran

Working rule seed:

```text
MAIN_62710
{PREFIX}-{SEQ3}/{UNIT}/{KKA}/{YEAR}

SUBBAG_62711
{PREFIX}-{SEQ3}/{UNIT}/{KKA}/{YEAR}

FORM_PERMINTAAN
{SEQ3}/{UNIT}/{KKA}/{YEAR}
```

Supported token:

```text
{PREFIX}
{SEQ}
{SEQ3}
{SEQ4}
{UNIT}
{KKA}
{YEAR}
```

Contoh:

```text
Sifat       = Biasa
Sequence    = 7
Unit        = 62710
KKA         = KU.261
Tahun       = 2026

B-007/62710/KU.261/2026
```

## Struktur utama

```text
app/
├── Controllers/
├── Core/
├── Models/
├── Services/
└── Views/

database/
├── mysql_schema_seed.sql
├── classification_catalog.json
└── migrations/
    └── 20260905_v03_full_classifications.sql

public/
├── assets/
│   ├── css/
│   ├── images/
│   └── js/
├── index.php
└── router.php
```


## Upgrade account management pada database yang sudah ada

Jika database sudah berasal dari v0.3 dan belum memiliki kolom `users.deleted_at`, jalankan sekali:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root agenda_surat_bps < database\migrations\20260907_user_account_management.sql
```

Untuk fresh install, migration tersebut tidak diperlukan karena `database/mysql_schema_seed.sql` sudah memuat struktur terbaru.

## Keamanan dan data BPS

- Jangan commit `.env`.
- Jangan menyimpan password production di repository.
- Jangan upload data responden atau dokumen rahasia ke GitHub.
- Gunakan dummy/anonymized data selama development jika data sebenarnya sensitif.
- Nomor yang dibatalkan tidak dihapus dan tidak digunakan ulang.

## Git workflow yang disarankan

```text
PULL -> BRANCH -> CODE -> TEST -> COMMIT -> PUSH -> PR -> REVIEW -> MERGE
```

Contoh:

```powershell
git checkout main
git pull origin main
git checkout -b feature/full-kka
```

Setelah tes:

```powershell
git add .
git commit -m "Implement full KKA catalogue and dynamic numbering rules"
git push -u origin feature/full-kka
```

Kemudian buat Pull Request ke `main`.

## Dokumen pengembangan

Lihat:

```text
docs/V03_FULL_KKA.md
docs/REQUIREMENT_CONFIRMATION.md
```