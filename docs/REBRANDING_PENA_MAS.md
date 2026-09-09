# Rebranding PENA MAS

Nama aplikasi diperbarui dari **SIGAS-BPS** menjadi **PENA MAS**.

**PENA MAS** = **Penomoran Agenda dan Manajemen Arsip Surat**.

Rebranding ini hanya mengubah identitas aplikasi/antarmuka dan tidak mengubah working rule penomoran yang masih perlu dikonfirmasi kepada BPS.

## Aset logo

- `public/assets/images/penamas-icon.png` — icon utama untuk sidebar, favicon, dan mobile.
- `public/assets/images/penamas-logo-full.png` — logo horizontal/full.
- File `*-original.png` disertakan sebagai arsip aset asli yang diberikan tim.

## Fitur yang ikut dipertahankan/dilengkapi

- Full KKA v0.3 dan dynamic numbering rules.
- Login ADMIN/USER.
- Ganti password mandiri.
- Manajemen pengguna: edit, bekukan/aktifkan, soft delete.
- Audit log.
- Timezone WIB (`Asia/Jakarta`, database `+07:00`).
- Root `/` diarahkan ke `/login` agar tidak memunculkan toast login palsu.
- Sidebar fixed dengan navigation scroll untuk layar pendek dan drawer/hamburger pada mobile.

## Upgrade database lama

Untuk database yang sudah ada, jalankan migration berikut sekali:

`database/migrations/20260907_user_account_management.sql`

Fresh install cukup menggunakan `database/mysql_schema_seed.sql` karena kolom `deleted_at` sudah tersedia di schema tersebut.
