<?php
session_start();
include '../config/koneksi.php';
include '../config/alert.php';

// proteksi login
if(!isset($_SESSION['login'])){
    header("Location: login.php");
    exit;
}

// handle hapus barang
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM barang WHERE id_barang='$id'");
    $_SESSION['success'] = 'Barang berhasil dihapus!';
    header("Location: inventaris.php");
}

// ambil data barang
$data = mysqli_query($conn, "SELECT * FROM barang");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabManager - Sistem Inventaris Lab</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-width: 260px;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: white;
            border-right: 1px solid #dee2e6;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            padding: 0.8rem 1.5rem;
            color: #6c757d;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            margin: 0.2rem 1rem;
        }

        .nav-link:hover {
            background-color: #f0f4f8;
            color: var(--primary-color);
        }

        .nav-link.active {
            background-color: var(--primary-color);
            color: white !important;
        }

        /* Main Content Styling */
        #main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s;
        }

        .header-top {
            background: white;
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Card Customization */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .stat-card {
            padding: 1.5rem;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Badge Styling */
        .badge-status {
            padding: 0.5em 1em;
            border-radius: 50px;
            font-weight: 500;
        }

        .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #6c757d;
            border-top: none;
        }

        .search-wrapper {
            position: relative;
        }

        .search-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
        }

        .search-wrapper input {
            padding-left: 35px;
            border-radius: 50px;
            background-color: #f1f3f5;
            border: none;
        }

        @media (max-width: 991.98px) {
            #sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            #main-content { margin-left: 0; }
            #sidebar.active { margin-left: 0; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <div class="bg-primary p-2 rounded text-white">
                <i class="bi bi-cpu-fill"></i>
            </div>
            <span class="fs-5 fw-bold">LabManager</span>
        </div>
        
        <div class="mt-3">
            <a href="dashboard.php" class="nav-link">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="inventaris.php" class="nav-link active">
                <i class="bi bi-box-seam"></i> Inventaris
            </a>
            <a href="riwayat.php" class="nav-link">
                <i class="bi bi-clock-history"></i> Riwayat Log
            </a>
            <a href="peminjaman.php" class="nav-link">
                <i class="bi bi-people"></i> Peminjaman
            </a>
            <a href="pengaturan.php" class="nav-link">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
        </div>

        <div style="position: absolute; bottom: 20px; width: 100%;">
            <a href="../logout.php" class="nav-link text-danger">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Header -->
        <header class="header-top">
            <h4 class="fw-bold mb-0">Manajemen Inventaris</h4>
            <div class="d-flex align-items-center gap-3">
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control shadow-none" placeholder="Cari barang...">
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                    <i class="bi bi-plus-lg"></i> Tambah Barang
                </button>
                <img src="https://ui-avatars.com/api/?name=Admin+Lab&background=0D8ABC&color=fff" class="rounded-circle" width="35">
            </div>
        </header>

        <!-- Alert Messages -->
        <div class="mb-3">
            <?php showAlert(); ?>
        </div>

        <!-- Filter & Table -->
        <div class="card">
            <div class="card-body p-0">
                
                <!-- Filter Section -->
                <div class="p-4 border-bottom d-flex flex-wrap gap-2 justify-content-between">
                    <div class="d-flex gap-2">
                        <select class="form-select">
                            <option>Semua Kategori</option>
                            <option>Hardware</option>
                            <option>Software</option>
                            <option>Networking</option>
                        </select>

                        <select class="form-select">
                            <option>Semua Status</option>
                            <option>Tersedia</option>
                            <option>Maintenance</option>
                            <option>Rusak</option>
                        </select>
                    </div>

                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th class="text-end px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $no=1; while($row = mysqli_fetch_assoc($data)) { ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['nama_barang']; ?></td>
                                    <td><?= $row['kategori']; ?></td>
                                    <td><?= $row['stok']; ?></td>
                                    <td><?= $row['lokasi']; ?></td>
                                    <td><span class="badge bg-success"><?= $row['kondisi']; ?></span></td>
                                    
                                    <td class="text-end px-4">
                                        <button class="btn btn-light btn-sm" title="Lihat"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-light btn-sm" title="Edit" data-bs-toggle="modal" data-bs-target="#modalEditBarang"><i class="bi bi-pencil"></i></button>
                                        <a href="?hapus=<?= $row['id_barang']; ?>" class="btn btn-light btn-sm text-danger" title="Hapus" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>

                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="p-3 bg-light d-flex justify-content-between align-items-center">
                    <small class="text-muted">Menampilkan data inventaris</small>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Tambah Barang -->
    <div class="modal fade" id="modalTambahBarang" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="../process/tambah_barang.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                                <option value="Networking">Networking</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="lokasi" class="form-label">Lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Contoh: Lemari A, Rak 1">
                        </div>
                        <div class="mb-3">
                            <label for="kondisi" class="form-label">Kondisi</label>
                            <select class="form-select" id="kondisi" name="kondisi">
                                <option value="Tersedia">Tersedia</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Rusak">Rusak</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>