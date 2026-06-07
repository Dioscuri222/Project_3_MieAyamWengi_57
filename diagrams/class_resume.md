# 📋 Resume Class, Attribute, dan Operation - Mie Ayam "Wengi 57"

Berdasarkan rancangan Class Diagram, berikut adalah resume fungsionalitas dari masing-masing kelas dalam sistem:

### A. Class `User` (Pelanggan)
Kelas ini mewakili pengguna akhir (pelanggan) yang berinteraksi langsung dengan katalog menu.
* **Attributes**:
  * `- customer_name : String` - Nama pelanggan.
  * `- table_number : String` - Nomor meja yang ditempati.
  * `- phone_number : String` - Nomor kontak WhatsApp/HP pelanggan.
  * `- notes : String` - Catatan khusus pesanan (misal: "tidak pakai daun bawang").
* **Operations**:
  * `+ melihatMenu() : void` - Mengakses halaman daftar menu aktif.
  * `+ membuatPesanan() : void` - Mengatur keranjang belanja (tambah, edit quantity).
  * `+ membayar() : void` - Memilih metode pembayaran tunai/non-tunai saat checkout.

### B. Class `Admin` (Administrator)
Kelas ini menangani otentikasi dan hak akses untuk mengontrol isi konten situs dan database pesanan.
* **Attributes**:
  * `- id : int` - Identifier utama administrator.
  * `- username : String` - Username masuk.
  * `- password : HashString` - Password akun terenkripsi (Bcrypt).
  * `- full_name : String` - Nama lengkap admin pemilik.
* **Operations**:
  * `+ login() : boolean` - Memeriksa kredensial pada tabel database `admins`.
  * `+ mengelolaMenu() : void` - Mengakses CRUD menu di `admin/menu-manage.php`.
  * `+ mengelolaPesanan() : void` - Mengakses panel status order di `admin/orders.php`.

### C. Class `MenuMieAyam` (Entitas Menu Kuliner)
Mewakili menu makanan atau minuman yang terdaftar dan siap dipesan.
* **Attributes**:
  * `- id : int` - ID unik menu.
  * `- name : String` - Nama menu (misal: "Mie Ayam Pangsit Basah").
  * `- description : String` - Deskripsi detail menu kuliner.
  * `- price : Decimal` - Nominal harga jual per porsi.
  * `- category : String` - Kategori menu ('Mie Ayam', 'Sampingan', 'Minuman').
  * `- image_path : String` - Path file lokal `/uploads/` atau URL gambar online.
  * `- is_available : Boolean` - Flag ketersediaan (1 = Ready, 0 = Sold Out).
* **Operations**:
  * `+ tambahMenu(data) : boolean` - Membuat baris menu baru di database.
  * `+ ubahMenu(id, data) : boolean` - Mengedit data menu yang sudah ada.
  * `+ hapusMenu(id) : boolean` - Menghapus menu dari database secara permanen.
  * `+ ubahStatusKetersediaan(id, status) : boolean` - Melakukan toggle aktif/nonaktif menu.

### D. Class `Pesanan` (Dokumen Order)
Mewakili transaksi pesanan pelanggan yang diproses oleh dapur dan kasir.
* **Attributes**:
  * `- id : int` - ID unik pesanan (Auto-increment).
  * `- customer_name : String` - Nama pemesan.
  * `- table_number : String` - Nomor meja pemesan.
  * `- total_amount : Decimal` - Total belanja bersih.
  * `- status : Enum('Pending', 'Cooking', 'Completed', 'Cancelled')` - Status proses pengerjaan makanan.
  * `- payment_method : String` - Metode pembayaran ('Tunai' atau 'QRIS').
  * `- created_at : Timestamp` - Tanggal dan jam masuk pesanan.
* **Operations**:
  * `+ hitungTotal() : Decimal` - Mengakumulasikan kuantitas dikalikan harga satuan menu.
  * `+ updateStatus(status) : boolean` - Mengupdate status pengerjaan makanan.

### E. Class `DetailPesanan` (Baris Item Order)
Mewakili item makanan/minuman spesifik di dalam satu nomor transaksi pesanan.
* **Attributes**:
  * `- id : int` - ID unik item order.
  * `- order_id : int` - Relasi foreign key ke tabel `orders`.
  * `- menu_id : int` - Relasi foreign key ke tabel `menus`.
  * `- quantity : int` - Jumlah porsi dipesan.
  * `- price : Decimal` - Harga menu saat dipesan (disimpan langsung untuk history log).
