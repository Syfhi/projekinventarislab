# Dokumentasi Process Folder

Folder ini berisi file-file yang menangani proses/logic bisnis aplikasi Inventaris Lab.

## Daftar File Process

### 1. **tambah_barang.php**
Menangani proses penambahan barang ke dalam inventaris.

**Form Data yang diterima:**
- `nama` - Nama barang
- `kategori` - Kategori barang (Hardware, Software, Networking, Lainnya)
- `stok` - Jumlah stok awal
- `lokasi` - Lokasi penyimpanan barang
- `kondisi` - Kondisi barang (Tersedia, Maintenance, Rusak)

**Fitur:**
- Validasi input kosong
- Penyimpanan notifikasi success/error ke session
- Redirect ke halaman inventaris

---

### 2. **edit_barang.php**
Menangani proses edit/update data barang yang sudah ada.

**Form Data yang diterima:**
- `id_barang` - ID barang yang akan diupdate
- `nama` - Nama barang baru
- `kategori` - Kategori barang baru
- `stok` - Stok barang baru
- `lokasi` - Lokasi barang baru
- `kondisi` - Kondisi barang baru

**Fitur:**
- Validasi input kosong
- Update data di database
- Notifikasi success/error
- Redirect ke halaman inventaris

---

### 3. **hapus_barang.php**
Menangani proses penghapusan barang dari inventaris.

**Parameter yang diterima:**
- `id` (GET) - ID barang yang akan dihapus

**Fitur:**
- Validasi ID tidak kosong
- Menghapus data dari database
- Notifikasi success/error
- Redirect ke halaman inventaris

---

### 4. **proses_peminjaman.php**
Menangani proses peminjaman barang oleh user.

**Form Data yang diterima:**
- `id_barang` - ID barang yang dipinjam
- `durasi` - Durasi peminjaman dalam hari
- `keterangan` - Keterangan/catatan peminjaman

**Fitur:**
- Validasi input kosong
- Cek ketersediaan stok barang
- Mencatat data peminjaman ke tabel `peminjaman`
- Otomatis mengurangi stok barang
- Menentukan tanggal kembali otomatis
- Notifikasi success/error dengan tanggal kembali
- Redirect ke halaman peminjaman

---

### 5. **proses_pengembalian.php**
Menangani proses pengembalian barang yang telah dipinjam.

**Form Data yang diterima:**
- `id_peminjaman` - ID record peminjaman
- `id_barang` - ID barang yang dikembalikan
- `kondisi` - Kondisi barang saat dikembalikan

**Fitur:**
- Update status peminjaman menjadi "Dikembalikan"
- Mencatat tanggal kembali aktual
- Otomatis menambah stok barang kembali
- Mencatat riwayat pengembalian
- Update kondisi barang berdasarkan kondisi pengembalian
- Notifikasi success/error
- Redirect ke halaman peminjaman

---

### 6. **proses_pengaturan.php**
Menangani proses perubahan pengaturan/profil user.

**Form Data untuk Update Profil:**
- `nama` - Nama lengkap user
- `email` - Email user

**Form Data untuk Update Password:**
- `password_lama` - Password lama untuk verifikasi
- `password_baru` - Password baru
- `konfirmasi_password` - Konfirmasi password baru

**Fitur Update Profil:**
- Validasi input kosong
- Cek email tidak digunakan user lain
- Update data profil di database
- Update session nama
- Notifikasi success/error

**Fitur Update Password:**
- Validasi input kosong
- Verifikasi password lama dengan yang tersimpan
- Validasi password baru minimal 6 karakter
- Validasi konfirmasi password sesuai
- Hash password baru
- Update password di database
- Notifikasi success/error

---

## Penggunaan dalam Pages

### inventaris.php
- Menggunakan `tambah_barang.php` untuk menambah barang
- Menggunakan `edit_barang.php` untuk edit barang
- Menggunakan `hapus_barang.php` untuk hapus barang
- Menampilkan notifikasi dari session menggunakan function `showAlert()`

### peminjaman.php
- Menggunakan `proses_peminjaman.php` untuk peminjaman barang
- Menggunakan `proses_pengembalian.php` untuk pengembalian barang

### pengaturan.php
- Menggunakan `proses_pengaturan.php` untuk update profil
- Menggunakan `proses_pengaturan.php` untuk update password

---

## Alert/Notification

Semua proses menggunakan session untuk menyimpan pesan:
- `$_SESSION['success']` - Pesan berhasil
- `$_SESSION['error']` - Pesan error

Untuk menampilkan notifikasi, gunakan function `showAlert()` yang sudah didefinisikan di `config/alert.php`:

```php
<?php
include '../config/alert.php';
// ...
showAlert(); // Tampilkan notifikasi
?>
```

---

## Catatan Security

⚠️ **Penting:** Kode ini masih menggunakan query langsung (SQL Injection Risk). 
Untuk production, gunakan Prepared Statements:

```php
$stmt = $conn->prepare("INSERT INTO barang (nama_barang, kategori, stok) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $nama, $kategori, $stok);
$stmt->execute();
```

---

## Struktur Tabel Database yang Dibutuhkan

```sql
-- Tabel barang
CREATE TABLE barang (
    id_barang INT PRIMARY KEY AUTO_INCREMENT,
    nama_barang VARCHAR(100),
    kategori VARCHAR(50),
    stok INT,
    lokasi VARCHAR(100),
    kondisi VARCHAR(50)
);

-- Tabel peminjaman
CREATE TABLE peminjaman (
    id_peminjaman INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT,
    id_barang INT,
    tanggal_pinjam DATE,
    tanggal_kembali DATE,
    tanggal_kembali_actual DATE,
    durasi INT,
    keterangan TEXT,
    status VARCHAR(50)
);

-- Tabel riwayat
CREATE TABLE riwayat (
    id_riwayat INT PRIMARY KEY AUTO_INCREMENT,
    id_peminjaman INT,
    keterangan TEXT,
    tanggal DATE
);

-- Tabel users
CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255),
    role VARCHAR(20)
);
```
