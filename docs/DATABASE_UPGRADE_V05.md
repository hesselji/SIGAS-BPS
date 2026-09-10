# Database Upgrade v0.5

## Fresh database
Cukup import:

`database/mysql_schema_seed.sql`

Jangan jalankan migration satu-satu setelah fresh seed.

## Database PENA MAS v0.4 yang sudah punya Full KKA + users.deleted_at
Jalankan sekali:

`database/migrations/20260910_pakcitra_revision.sql`

## Database lebih lama yang belum punya users.deleted_at
Urutan:

1. `20260907_user_account_management.sql`
2. `20260910_pakcitra_revision.sql`

Jika database belum Full KKA, lakukan migration Full KKA yang relevan terlebih dahulu sesuai versi sumbernya.
