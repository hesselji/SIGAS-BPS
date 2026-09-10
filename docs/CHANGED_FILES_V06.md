# Changed Files - PENA MAS v0.6

Dibandingkan PENA MAS v0.5, fitur batch tujuan hanya menyentuh file berikut:

## Logic / backend
- `app/Services/LetterNumberGenerator.php` — menambah `generateBatch()` atomic dengan satu transaksi + `FOR UPDATE`.
- `app/Controllers/OutgoingLetterController.php` — menggabungkan tujuan manual, bulk paste, dan selected user; validasi max 200; memanggil batch generator.
- `app/Models/User.php` — query kandidat quick picker serta resolve nama berdasarkan user ID secara server-side.

## UI
- `app/Views/letters/create.php` — UI 3 metode tujuan + counter batch.
- `public/assets/js/app.js` — filter user, select all visible, dedupe preview, counter, konfirmasi batch.
- `public/assets/css/style.css` — styling builder tujuan + responsive mobile.
- `app/Views/layouts/header.php` — cache-bust CSS v0.6.
- `app/Views/layouts/footer.php` — cache-bust JS v0.6.

## Dokumentasi
- `README.md`
- `RELEASE_NOTES.md`
- `docs/REQUIREMENT_BATCH_TUJUAN_20260910.md`
- `docs/TEST_CHECKLIST_V06.md`
- `docs/CHANGED_FILES_V06.md`

## Database
Tidak ada perubahan schema dan tidak ada migration baru untuk v0.6.
