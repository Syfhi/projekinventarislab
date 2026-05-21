<?php
session_start();

// PROTEKSI
if(!isset($_SESSION['login'])){
    header("Location: login.php");
    exit;
}
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
            <a href="dashboard.php" class="nav-link active">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="inventaris.php" class="nav-link">
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
            <h4 class="fw-bold mb-0">Dashboard Ringkasan</h4>
            <div class="d-flex align-items-center gap-3">
                <div class="search-wrapper d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control shadow-none" placeholder="Cari barang...">
                </div>
                <div class="dropdown">
                    <img src="https://ui-avatars.com/api/?name=Admin+Lab&background=0D8ABC&color=fff" class="rounded-circle" width="35" alt="Avatar">
                </div>
            </div>
        </header>

        <h2>Dashboard</h2>
        <p>Selamat datang, <?= $_SESSION['nama']; ?> 👋</p>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card stat-card">
                    <div class="icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="text-secondary small fw-medium">Total Barang</div>
                    <h3 class="fw-bold mb-0">1,240</h3>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card stat-card">
                    <div class="icon-box bg-success-subtle text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="text-secondary small fw-medium">Kondisi Baik</div>
                    <h3 class="fw-bold mb-0">1,120</h3>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card stat-card">
                    <div class="icon-box bg-warning-subtle text-warning">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div class="text-secondary small fw-medium">Perlu Perbaikan</div>
                    <h3 class="fw-bold mb-0">86</h3>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card stat-card">
                    <div class="icon-box bg-danger-subtle text-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="text-secondary small fw-medium">Stok Kritis</div>
                    <h3 class="fw-bold mb-0">12</h3>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Inventory Table -->
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
                            <h5 class="fw-bold mb-0">Inventaris Terbaru</h5>
                            <button class="btn btn-sm btn-primary">Tambah Barang</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3">Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Stok</th>
                                        <th>Status</th>
                                        <th class="text-end px-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold">Monitor Dell UltraSharp 24"</div>
                                            <div class="text-muted small">ID: LAB-MON-001</div>
                                        </td>
                                        <td>Hardware</td>
                                        <td><span class="fw-semibold">45</span></td>
                                        <td><span class="badge bg-success-subtle text-success badge-status text-uppercase" style="font-size: 0.65rem;">Tersedia</span></td>
                                        <td class="text-end px-4">
                                            <button class="btn btn-light btn-sm"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-light btn-sm text-danger"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold">PC Desktop i7 Gen 12</div>
                                            <div class="text-muted small">ID: LAB-PC-024</div>
                                        </td>
                                        <td>Unit PC</td>
                                        <td><span class="fw-semibold">20</span></td>
                                        <td><span class="badge bg-warning-subtle text-warning badge-status text-uppercase" style="font-size: 0.65rem;">Maintenance</span></td>
                                        <td class="text-end px-4">
                                            <button class="btn btn-light btn-sm"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-light btn-sm text-danger"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold">TP-Link Router AX1500</div>
                                            <div class="text-muted small">ID: LAB-NET-005</div>
                                        </td>
                                        <td>Networking</td>
                                        <td><span class="fw-semibold">2</span></td>
                                        <td><span class="badge bg-danger-subtle text-danger badge-status text-uppercase" style="font-size: 0.65rem;">Stok Habis</span></td>
                                        <td class="text-end px-4">
                                            <button class="btn btn-light btn-sm"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-light btn-sm text-danger"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-light text-center">
                            <a href="inventaris.html" class="text-decoration-none small fw-bold">Lihat Semua Inventaris</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Aktivitas Terakhir</h5>
                        <div class="d-flex flex-column gap-4">
                            <div class="d-flex gap-3">
                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="bi bi-plus"></i>
                                </div>
                                <div>
                                    <div class="small fw-bold">Barang Baru Ditambahkan</div>
                                    <p class="small text-muted mb-0">Keyboard Mechanical Rexus (5 Unit) oleh Admin</p>
                                    <span class="text-muted" style="font-size: 0.7rem;">10 Menit yang lalu</span>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="bi bi-arrow-repeat"></i>
                                </div>
                                <div>
                                    <div class="small fw-bold">Status Perubahan</div>
                                    <p class="small text-muted mb-0">PC-024 diubah ke Maintenance oleh Teknisi</p>
                                    <span class="text-muted" style="font-size: 0.7rem;">2 Jam yang lalu</span>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="bi bi-person-check"></i>
                                </div>
                                <div>
                                    <div class="small fw-bold">Peminjaman Baru</div>
                                    <p class="small text-muted mb-0">Proyektor Epson dipinjam oleh Dosen Budi</p>
                                    <span class="text-muted" style="font-size: 0.7rem;">Kemarin</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>