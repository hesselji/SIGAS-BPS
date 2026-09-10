# PENA MAS — UI Upgrade v0.2

Versi ini merupakan redesign visual penuh dari prototype v0.1 tanpa mengubah schema database.

## Yang diubah

- Branding aplikasi menjadi **PENA MAS**.
- Logo PENA MAS menggunakan `public/assets/images/penamas-icon.png`.
- Dark dashboard modern dengan aksen biru, oranye, dan hijau BPS.
- Sidebar desktop + drawer mobile.
- Global search menuju Agenda Surat.
- Dark/light theme tersimpan di `localStorage`.
- Dashboard metric cards, status donut, statistik tim, quick actions, agenda terbaru.
- Generate Nomor dengan form bertahap, cascading KKA, live number preview, sticky action.
- Daftar Agenda dengan modern search/filter/table.
- Detail Agenda dengan hero nomor, copy number, audit status, cancellation panel.
- Admin Pengguna dengan UI baru.
- Login full redesign dan branding BPS.
- Toast notification, loading state, responsive layout, keyboard shortcut Ctrl/Cmd+K.

## File UI utama

- `app/Views/layouts/header.php`
- `app/Views/layouts/footer.php`
- `app/Views/auth/login.php`
- `app/Views/dashboard/index.php`
- `app/Views/letters/create.php`
- `app/Views/letters/index.php`
- `app/Views/letters/show.php`
- `app/Views/admin/users/index.php`
- `app/Views/admin/users/create.php`
- `app/Core/helpers.php`
- `public/assets/css/style.css`
- `public/assets/js/app.js`
- `public/assets/images/penamas-icon.png`

## Upgrade project lama

Karena v0.2 tidak mengubah schema DB, database `agenda_surat_bps` yang sudah di-import dapat tetap dipakai.

1. Backup project/repo lama atau commit dulu.
2. Copy file v0.2 menimpa project lokal.
3. Jangan timpa `.env` milik lokal; ZIP ini memang hanya membawa `.env.example`.
4. Pastikan `.env` memiliki `APP_NAME="PENA MAS"` jika ingin title browser ikut berubah.
5. Restart PHP server.
6. Hard refresh browser: `Ctrl+F5`.

## Git yang disarankan

```bash
git checkout -b feature/ui-ux-redesign
git add .
git commit -m "Redesign PENA MAS UI with BPS visual identity"
git push -u origin feature/ui-ux-redesign
```

Lalu buat Pull Request ke `main` agar teman magang dapat review.
