# Requirement Status PENA MAS - Setelah Konsultasi Pak Citra

## Sudah dikonfirmasi / dipakai pada v0.5

1. Jalur `62710` dan `62711` mempunyai sequence terpisah dan keduanya dapat mulai dari `001`.
2. Jalur 62710 tidak menerima tanggal H-; 62711 lebih fleksibel.
3. Jika menggunakan anggaran, Jenis Arsip otomatis Fasilitatif dan Kelompok KKA otomatis KU.
4. USER hanya membuat surat dengan sifat Biasa.
5. ADMIN dapat membuat Biasa, Rahasia, dan Sangat Rahasia; opsi Penting tidak digunakan.
6. Agenda USER menampilkan semua surat Biasa, termasuk yang dibuat user/admin lain.
7. Dashboard USER hanya statistik surat buatan akun itu; Dashboard ADMIN statistik global.
8. Rahasia/Sangat Rahasia hanya dibuat dan dilihat ADMIN.
9. Saat membuka detail Rahasia/Sangat Rahasia, ADMIN wajib memasukkan password akun kembali.
10. Admin perlu dapat reset password user.
11. Akun yang dihapus harus memungkinkan email lamanya digunakan kembali tanpa merusak histori.
12. Sistem diupayakan memiliki Agenda Surat Masuk dan user/petugas khusus Surat Masuk.
13. Surat Masuk menyimpan nomor dari pengirim, asal, perihal, kepada, tanggal surat, tanggal diterima, dan tanggal pencatatan otomatis.
14. Panduan penggunaan merupakan output yang disarankan.

## Tetap sebagai working rule / perlu konfirmasi jika akan masuk produksi

1. Apakah sequence reset per tahun secara resmi. Prototype saat ini memisahkan sequence per `numbering_rule_id + year` mengikuti desain sebelumnya.
2. Efek SRIKANDI/Non Srikandi terhadap format nomor (saat ini disimpan sebagai metadata).
3. Mapping final semua jenis surat ke rule 62710/62711/Form Permintaan.
4. Kebijakan durasi re-auth surat rahasia; prototype memakai grant 5 menit dan dapat diatur lewat `.env`.
5. Workflow lanjutan Surat Masuk seperti disposisi/status dibuka-belum dibuka belum diimplementasikan pada v0.5.

## Roadmap jangka panjang

- Import master KKA/rule dengan template CSV.
- Generate isi/template surat dan export/print PDF.
- Surat masuk + disposisi yang lebih lengkap.
