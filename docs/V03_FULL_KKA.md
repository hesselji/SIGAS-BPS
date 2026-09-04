# SIGAS-BPS v0.3 - Full KKA

## Apa yang berubah

1. Working-set KKA dummy v0.2 diganti katalog klasifikasi dari `Kode.pdf`.
2. Master KKA tidak lagi bergantung pada scope dummy YF/TF/YS/TS untuk menentukan daftar kode.
3. `Ada Anggaran?` tetap direkam sebagai metadata, tetapi tidak digunakan untuk menyembunyikan KKA karena aturan hubungan anggaran -> klasifikasi belum didukung secara eksplisit oleh dokumen Kode.pdf.
4. Jenis Arsip menentukan kelompok KKA yang tersedia:
   - Substantif -> PS, SS, VS, KS.
   - Fasilitatif -> KU, KP, PR, HK, OT, HM, KA, RT, PL, DL, PK, IF, PW, TS.
5. Dropdown kedua berisi seluruh kode numerik yang tersedia di kelompok tersebut.
6. Preview nomor menggunakan pattern sebenarnya dari `numbering_rules`.
7. Admin memperoleh menu Katalog KKA dan Aturan Nomor.

## Normalisasi kode

Untuk pembentukan KKA:

- `0` -> `000`
- `10` -> `010`
- `21` -> `021`
- `100` -> `100`
- `1010` -> `1010`

`raw_code` tetap disimpan pada master catalog agar bentuk dari sumber tidak hilang.

## Catatan ekstraksi sumber

Pada bagian Perencanaan terdapat teks hasil ekstraksi `200 210 Usulan Unit Kerja...`. Dokumen yang sama pada ringkasan klasifikasi menyebut level `200` sebagai `USULAN RENCANA DAN PROGRAM KERJA`; v0.3 menggunakan informasi ringkasan tersebut untuk level 200 dan mempertahankan `210 Usulan Unit Kerja...` sebagai item berikutnya.

Kelompok `ES` disebut pada dokumen, tetapi rincian kode numerik tidak muncul pada bagian utama yang diekstrak. Karena itu `ES` disimpan pada catalog dalam keadaan nonaktif dan tidak ditawarkan pada form sampai sumber rincian resminya tersedia.

## Hal yang masih perlu konfirmasi BPS

- Mapping resmi setiap jenis surat ke `62710`, `62711`, atau format lain.
- Apakah sequence satu per rule/tahun atau memiliki scope lain.
- Apakah `SRIKANDI / Non SRIKANDI` memengaruhi format nomor.
- Hubungan pasti `Ada Anggaran?` terhadap pemilihan klasifikasi.
- Kebijakan akses Rahasia/Sangat Rahasia.
- Apakah Form Permintaan selalu memakai segmen `FP-2886.EB` atau berubah berdasarkan kegiatan.

Karena itu rule penomoran dibuat configurable oleh Admin.
