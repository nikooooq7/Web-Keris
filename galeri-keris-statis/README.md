# Galeri Keris Nusantara — Situs Statis (Dinamis dari data.js)

## Cara mengubah isi (tanpa menyentuh HTML)
Buka `assets/js/data.js`:
- **Kontak** (alamat, telepon, WhatsApp, email, jam buka): bagian `SITE.kontak`
- **Foto profil**: `SITE.foto.profil` (lebar) dan `SITE.foto.profilBulat` (persegi, untuk header/footer)
- **Teks** Beranda, Profil Kami, visi/misi: bagian `SITE.beranda` dan `SITE.profil`
- **Produk**: salin satu blok `{ ... }` di `PRODUK`, ganti isinya. Taruh foto di `uploads/products/` dan versi kecil (±640 px) di `uploads/products/thumbs/` dengan nama yang sama.
- Harga `null` = tampil "Hubungi Kami".

Header, footer, filter koleksi, halaman detail (`produk.html?p=slug`), tombol WhatsApp dan form kontak dibuat otomatis dari data tersebut oleh `assets/js/site.js`.

## Deploy
Seret seluruh folder ini ke https://app.netlify.com/drop (atau GitHub Pages/Vercel). Tidak perlu PHP atau database. Dapat juga dibuka langsung dengan klik dua kali `index.html`.
