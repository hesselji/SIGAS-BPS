# Requirement - Batch / Multiple Tujuan Surat

Tanggal: 10 September 2026

## Kebutuhan

Untuk kegiatan yang melibatkan puluhan hingga ratusan orang, data surat seperti jenis surat, perihal, KKA, tanggal, tim kerja, sistem, anggaran, dan sifat dapat sama. Tujuan/nama penerima berbeda, dan **setiap tujuan harus memperoleh nomor surat berbeda**.

## Rule implementasi v0.6

- Satu batch menggunakan satu jenis surat / numbering rule.
- Satu batch menggunakan data bersama yang sama (tanggal, sifat, KKA, perihal, dan metadata lain pada form).
- Banyak tujuan menghasilkan banyak record `outgoing_letters`.
- Satu tujuan = satu nomor unik.
- Maksimal 200 tujuan per submit.
- Nama duplicate hanya dibuat sekali.
- Tujuan dapat diketik manual, dipaste banyak baris, atau dipilih dari akun aktif PENA MAS.
- Akun PENA MAS hanya quick picker, bukan master pegawai resmi.

## Integritas sequence

Seluruh batch harus atomic:

1. begin transaction;
2. resolve numbering rule;
3. ensure row `number_sequences`;
4. `SELECT ... FOR UPDATE` satu kali;
5. alokasikan nomor `current+1` sampai `current+jumlah_tujuan`;
6. insert semua surat;
7. update sequence ke nomor terakhir;
8. commit.

Jika satu proses gagal, rollback semua insert dan sequence.
