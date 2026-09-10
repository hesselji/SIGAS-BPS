# PENA MAS v0.6 - Batch / Multiple Tujuan Surat

Build: 10 September 2026

## Revisi terbaru Pak Citra

Kasus utama: ketika perlu membuat surat dengan perihal/kode/tanggal yang sama untuk banyak orang, user tidak perlu mengulang form satu per satu. Setiap tujuan tetap menghasilkan **record surat sendiri dan nomor surat yang berbeda**.

## Implemented

- Generate satu surat tetap didukung seperti sebelumnya.
- Generate massal sampai 200 tujuan dalam satu submit.
- Tiga sumber tujuan yang dapat digabung:
  1. ketik satu tujuan manual;
  2. paste banyak nama, satu baris satu nama;
  3. pilih nama dari akun ADMIN/USER aktif PENA MAS sebagai shortcut.
- Search akun berdasarkan nama, email, atau tim kerja.
- `Pilih Semua Hasil` hanya memilih akun yang tampil setelah filter pencarian.
- Duplicate nama dari input manual/paste/akun otomatis dihapus.
- List bernomor sederhana seperti `1. Nama` atau `1) Nama` dibersihkan otomatis.
- Tombol generate menampilkan jumlah nomor yang akan dibuat.
- Konfirmasi sebelum generate batch.
- Engine batch mengunci satu row `number_sequences` dengan `FOR UPDATE`, mengalokasikan rentang nomor, insert seluruh record, lalu menaikkan sequence sekali.
- Jika satu insert gagal, seluruh transaksi batch di-rollback.
- Sequence 62710 dan 62711 tetap terpisah karena menggunakan `numbering_rule_id + year`.
- Audit `GENERATE_BATCH` menyimpan jumlah, nomor awal/akhir, rule, sensitivity, dan ID surat; tidak menyimpan data rahasia/password.

## Database

Tidak ada schema/migration baru untuk v0.6. Upgrade dari v0.5 cukup mengganti file source yang berubah.

## Catatan sumber nama

Daftar akun PENA MAS hanyalah **quick picker**, bukan master pegawai resmi. Untuk orang yang tidak memiliki akun, gunakan input manual atau paste banyak nama. Master pegawai dapat dibuat sebagai pengembangan berikutnya bila BPS memberikan sumber data resminya.
