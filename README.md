# PENA MAS v0.6 - Batch / Multiple Tujuan Surat

**PENA MAS** = **Penomoran Agenda dan Manajemen Arsip Surat**. Prototype internal untuk pengembangan proses agenda surat di BPS Kota Palangka Raya.

Versi ini dibangun di atas PENA MAS v0.5 dan menambahkan revisi terbaru Pak Citra: generate nomor surat massal untuk banyak tujuan dalam satu proses.

## Fitur utama

### Surat Keluar
- Sequence aman memakai transaksi MySQL + `SELECT ... FOR UPDATE`.
- Jalur `62710` dan `62711` mempunyai sequence **terpisah** per tahun.
- Jalur `62710`: tanggal surat H- (sebelum hari ini) ditolak di UI dan backend.
- Jalur `62711`: tanggal surat tetap fleksibel.
- Jika `Ada Anggaran = Ya`, sistem otomatis memaksa `FASILITATIF + KU`.
- Search KKA berdasarkan kode/nama/deskripsi (contoh: `pajak`).
- USER hanya dapat membuat sifat **Biasa**; backend tetap memaksa `BIASA` walaupun request dimodifikasi.
- ADMIN dapat membuat **Biasa, Rahasia, Sangat Rahasia**.
- Agenda USER menampilkan seluruh surat **Biasa** dari semua pengguna.
- Dashboard USER tetap statistik pribadi; Dashboard ADMIN statistik global.
- Rahasia/Sangat Rahasia tidak pernah tampil kepada USER.
- Detail Rahasia/Sangat Rahasia membutuhkan re-entry password ADMIN.
- Akses rahasia sukses/gagal dicatat pada audit log tanpa mencatat password.
- **Batch / multiple tujuan:** satu data surat yang sama dapat menghasilkan banyak record dan nomor unik untuk tujuan berbeda.
- Tujuan dapat berasal dari **ketik manual**, **paste banyak nama (satu nama per baris)**, atau **pilih akun aktif PENA MAS**.
- Tombol **Pilih Semua Hasil** tersedia pada pencarian akun; hanya akun yang sedang terlihat yang dipilih.
- Nama tujuan yang sama dari beberapa sumber otomatis di-deduplicate.
- Maksimal **200 tujuan** per batch.
- Engine batch memakai **satu transaksi + satu `SELECT ... FOR UPDATE`**; jika satu insert gagal, seluruh batch di-rollback.

### Surat Masuk
- Role baru `INCOMING` untuk petugas pencatatan surat masuk.
- Role INCOMING langsung diarahkan ke Agenda Surat Masuk setelah login (tanpa dashboard surat keluar).
- Field: nomor surat dari pengirim, asal, perihal, kepada, tanggal surat, tanggal diterima, catatan.
- Tanggal/waktu pencatatan otomatis saat submit.
- ADMIN juga dapat melihat dan menambah Surat Masuk.

### Pengguna
- ADMIN dapat tambah/edit akun.
- Role: `ADMIN`, `USER`, `INCOMING`.
- Bekukan/Aktifkan akun.
- Reset password user oleh ADMIN.
- User dapat ganti password sendiri dengan verifikasi password lama.
- Soft delete menjaga relasi histori, tetapi email asli dilepas sehingga dapat digunakan ulang untuk akun baru.
- Admin tidak dapat membekukan/menghapus akun sendiri dan admin aktif terakhir dilindungi.

### KKA & Rule
- Full KKA dari source sebelumnya tetap dipertahankan.
- Menu Katalog KKA dan Aturan Nomor tetap tersedia untuk ADMIN.
- Working rule tetap configurable untuk penyesuaian lanjutan.

## Fresh install lokal

1. Extract project, misalnya ke:

```text
C:\PENA-MAS
```

2. Start **MySQL** di XAMPP.

3. Buat database:

```sql
CREATE DATABASE agenda_surat_bps
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

4. Import **hanya**:

```text
database/mysql_schema_seed.sql
```

Untuk fresh install jangan jalankan migration satu-satu karena schema seed sudah berisi seluruh perubahan database v0.5. Fitur batch v0.6 tidak memerlukan perubahan schema database.

5. Copy `.env.example` menjadi `.env`:

```powershell
copy .env.example .env
```

Default lokal:

```env
APP_NAME="PENA MAS"
APP_ENV=local
APP_URL=http://127.0.0.1:8080
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
DB_TIMEZONE=+07:00
CONFIDENTIAL_REAUTH_SECONDS=300

DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agenda_surat_bps
DB_USERNAME=root
DB_PASSWORD=
```

6. Jalankan:

```powershell
cd C:\PENA-MAS
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public public\router.php
```

Buka:

```text
http://127.0.0.1:8080/login
```

## Akun demo fresh install

```text
ADMIN
admin@demo.local
Admin123!

USER SURAT KELUAR
user@demo.local
User123!

PETUGAS SURAT MASUK
incoming@demo.local
Incoming123!
```

Semua akun di atas hanya untuk testing.

## Upgrade database lama

### Jika database masih sebelum fitur user account management
Jalankan lebih dulu:

```text
database/migrations/20260907_user_account_management.sql
```

### Setelah database sudah v0.4
Jalankan:

```text
database/migrations/20260910_pakcitra_revision.sql
```

Migration 20260910:
- menambah role `INCOMING`;
- menonaktifkan pilihan `PENTING`;
- membuat tabel `incoming_letters`;
- tidak mereset agenda surat keluar;
- tidak mengubah sequence lama.

**Jangan import `mysql_schema_seed.sql` ke database yang ingin dipertahankan datanya**, karena file fresh install melakukan `DROP TABLE`.

## Penjelasan sequence 62710 / 62711

Tabel `number_sequences` memakai kunci unik:

```text
numbering_rule_id + year
```

Jenis surat 62710 menggunakan rule `MAIN_62710`, sedangkan Subbag menggunakan `SUBBAG_62711`. Karena rule berbeda, keduanya boleh menghasilkan:

```text
B-001/62710/...
B-001/62711/...
```

tanpa dianggap sequence yang sama.

## Hak akses

```text
ADMIN
- Dashboard global
- Surat Keluar: Biasa/Rahasia/Sangat Rahasia
- Agenda seluruh Surat Keluar
- Re-auth password untuk detail rahasia
- Surat Masuk
- Pengguna/KKA/Aturan Nomor

USER
- Dashboard pribadi
- Generate hanya Biasa
- Agenda seluruh surat Biasa
- Tidak dapat mengetahui/membuka Rahasia/Sangat Rahasia

INCOMING
- Agenda Surat Masuk
- Tambah Surat Masuk
- Ganti Password
- Tidak memiliki akses Surat Keluar/Admin
```

## Upgrade dari v0.5 ke v0.6

Tidak ada migration database baru. Cukup update source code. Data `outgoing_letters` tetap memakai satu record per tujuan sehingga histori dan agenda lama tetap kompatibel.

## File penting revisi

```text
app/Core/Auth.php
app/Models/User.php
app/Services/LetterNumberGenerator.php
app/Models/OutgoingLetter.php
app/Models/IncomingLetter.php
app/Models/MasterData.php
app/Controllers/OutgoingLetterController.php
app/Controllers/IncomingLetterController.php
app/Controllers/AdminUserController.php
app/Views/letters/create.php
app/Views/letters/confidential-lock.php
app/Views/incoming/
public/assets/js/app.js
public/assets/css/style.css
database/migrations/20260910_pakcitra_revision.sql
```

## Testing minimum sebelum merge/hosting

Lihat `docs/TEST_CHECKLIST_V06.md` untuk fitur batch dan `docs/TEST_CHECKLIST_V05.md` untuk regression test fitur sebelumnya.

## Security

- `.env` tidak boleh masuk GitHub.
- Jangan memakai data rahasia BPS pada prototype publik.
- Password disimpan dengan `password_hash()` dan diverifikasi dengan `password_verify()`.
- Password tidak pernah ditulis ke audit log.
- POST menggunakan CSRF.
- Query menggunakan PDO prepared statements.
- `APP_DEBUG=false` untuk hosting publik.
