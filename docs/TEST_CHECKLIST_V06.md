# Smoke Test PENA MAS v0.6 - Batch Tujuan

## Single generate
- [ ] Isi hanya `Tujuan manual` satu nama.
- [ ] Tombol menunjukkan `Generate & Simpan Nomor`.
- [ ] Submit menghasilkan satu record dan redirect ke detail.
- [ ] Sequence naik satu angka.

## Paste batch
- [ ] Paste 5 nama, satu nama per baris.
- [ ] Counter menunjukkan 5 tujuan.
- [ ] Tombol menunjukkan `Generate 5 Nomor Sekaligus`.
- [ ] Konfirmasi batch muncul sebelum submit.
- [ ] Submit menghasilkan 5 record dengan perihal/KKA sama dan tujuan berbeda.
- [ ] Kelima nomor berurutan dan unik.
- [ ] Sequence naik tepat 5 angka.

## Normalisasi / duplicate
- [ ] Paste `1. Andi`, `2. Budi`, `3) Citra` -> tersimpan tanpa prefix nomor list.
- [ ] Nama yang sama pada manual + paste hanya menghasilkan satu surat.
- [ ] Nama yang sama pada paste + checkbox akun hanya menghasilkan satu surat.
- [ ] Form menolak batch lebih dari 200 tujuan.

## Quick picker akun
- [ ] Daftar menampilkan akun ADMIN/USER aktif dan tidak menampilkan INCOMING.
- [ ] Search nama bekerja.
- [ ] Search email bekerja.
- [ ] Search tim kerja bekerja.
- [ ] `Pilih Semua Hasil` hanya mencentang hasil yang sedang terlihat.
- [ ] `Kosongkan` menghapus semua checkbox.
- [ ] Nama akun terpilih ikut dihitung counter.

## Atomic transaction
- [ ] Generate batch pada 62710 menghasilkan sequence 62710 saja.
- [ ] Generate batch pada 62711 menghasilkan sequence 62711 saja.
- [ ] Setelah batch 62710 sebanyak 5 dari current=10, current menjadi 15.
- [ ] Pastikan tidak ada nomor duplicate pada `outgoing_letters.letter_number`.
- [ ] Uji concurrency jika memungkinkan: dua submit bersamaan tidak memperoleh nomor yang sama.

## Regression
- [ ] Aturan H- 62710 tetap bekerja.
- [ ] 62711 tetap memiliki sequence terpisah.
- [ ] Ada Anggaran=Ya tetap memaksa Fasilitatif + KU.
- [ ] USER tetap hanya dapat membuat Biasa.
- [ ] Rahasia/Sangat Rahasia tetap ADMIN-only dan meminta password saat dibuka.
- [ ] Modul Surat Masuk tetap bekerja.
