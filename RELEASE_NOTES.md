# PENA MAS - Rebrand Release

Build ini merebrand antarmuka SIGAS-BPS menjadi **PENA MAS (Penomoran Agenda dan Manajemen Arsip Surat)** menggunakan logo baru yang diberikan tim.

Termasuk:
- Branding PENA MAS pada login, sidebar, title, favicon, footer, dashboard, dan administrasi pengguna.
- Asset icon/logo PENA MAS baru.
- Full KKA v0.3 dan dynamic numbering rules tetap dipertahankan.
- Ganti password mandiri.
- Edit, bekukan/aktifkan, dan soft-delete pengguna.
- Timezone WIB untuk PHP dan session MySQL.
- Root redirect `/` ke `/login`.
- Sidebar fixed + scroll nav + hamburger mobile.

Database existing v0.3: jalankan `database/migrations/20260907_user_account_management.sql` sekali jika kolom `users.deleted_at` belum ada.
