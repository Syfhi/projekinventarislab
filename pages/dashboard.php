<?php
session_start();
include '../config/koneksi.php';

// PROTEKSI
if(!isset($_SESSION['login'])){
    header("Location: login.php");
    exit;
}

// Statistik dashboard
$totalBarang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang"))['total'];
$totalTersedia = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang WHERE kondisi='Tersedia' OR stok > 0"))['total'];
$totalMaintenance = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang WHERE kondisi='Maintenance'"))['total'];
$totalKritis = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang WHERE stok <= 5 OR kondisi='Rusak'"))['total'];

// Inventaris terbaru
$latestInventory = mysqli_query($conn, "SELECT * FROM barang ORDER BY id_barang DESC LIMIT 3");

// Aktivitas terakhir
$recentActivities = mysqli_query($conn, "SELECT r.*, b.nama_barang, p.status FROM riwayat r LEFT JOIN peminjaman p ON r.id_peminjaman = p.id_peminjaman LEFT JOIN barang b ON p.id_barang = b.id_barang ORDER BY r.tanggal DESC LIMIT 3");
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
                    <h3 class="fw-bold mb-0"><?= number_format($totalBarang); ?></h3>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card stat-card">
                    <div class="icon-box bg-success-subtle text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="text-secondary small fw-medium">Tersedia</div>
                    <h3 class="fw-bold mb-0"><?= number_format($totalTersedia); ?></h3>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card stat-card">
                    <div class="icon-box bg-warning-subtle text-warning">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div class="text-secondary small fw-medium">Maintenance</div>
                    <h3 class="fw-bold mb-0"><?= number_format($totalMaintenance); ?></h3>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card stat-card">
                    <div class="icon-box bg-danger-subtle text-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="text-secondary small fw-medium">Stok Kritis</div>
                    <h3 class="fw-bold mb-0"><?= number_format($totalKritis); ?></h3>
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
                            <a href="inventaris.php" class="btn btn-sm btn-primary">Lihat Inventaris</a>
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
                                <?php if(mysqli_num_rows($latestInventory) > 0): ?>
                                    <?php while($item = mysqli_fetch_assoc($latestInventory)): ?>
                                        <?php
                                            $statusBadge = 'bg-secondary-subtle text-secondary';
                                            $statusText = $item['kondisi'];

                                            if(strtolower($item['kondisi']) === 'tersedia'){
                                                $statusBadge = 'bg-success-subtle text-success';
                                            } elseif(strtolower($item['kondisi']) === 'maintenance'){
                                                $statusBadge = 'bg-warning-subtle text-warning';
                                            } elseif(strtolower($item['kondisi']) === 'rusak' || $item['stok'] <= 0){
                                                $statusBadge = 'bg-danger-subtle text-danger';
                                                $statusText = 'Stok Habis';
                                            }
                                        ?>
                                        <tr>
                                            <td class="px-4">
                                                <div class="fw-bold"><?= htmlspecialchars($item['nama_barang']); ?></div>
                                                <div class="text-muted small">ID: <?= htmlspecialchars($item['id_barang']); ?></div>
                                            </td>
                                            <td><?= htmlspecialchars($item['kategori']); ?></td>
                                            <td><span class="fw-semibold"><?= number_format($item['stok']); ?></span></td>
                                            <td><span class="badge <?= $statusBadge; ?> badge-status text-uppercase" style="font-size: 0.65rem;"><?= htmlspecialchars($statusText); ?></span></td>
                                            <td class="text-end px-4">
                                                <a href="inventaris.php" class="btn btn-light btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data inventaris.</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-light text-center">
                            <a href="inventaris.php" class="text-decoration-none small fw-bold">Lihat Semua Inventaris</a>
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
                            <?php if(mysqli_num_rows($recentActivities) > 0): ?>
                                <?php while($activity = mysqli_fetch_assoc($recentActivities)): ?>
                                    <?php
                                        $icon = 'bi-arrow-repeat';
                                        $circleClass = 'bg-warning-subtle text-warning';
                                        $title = 'Aktivitas Baru';

                                        if(stripos($activity['keterangan'], 'ditambahkan') !== false) {
                                            $icon = 'bi-plus';
                                            $circleClass = 'bg-primary-subtle text-primary';
                                            $title = 'Barang Baru Ditambahkan';
                                        } elseif(stripos($activity['keterangan'], 'dikembalikan') !== false) {
                                            $icon = 'bi-person-check';
                                            $circleClass = 'bg-info-subtle text-info';
                                            $title = 'Peminjaman Dikembalikan';
                                        }

                                        $timeLabel = date('d M Y', strtotime($activity['tanggal']));
                                    ?>
                                    <div class="d-flex gap-3">
                                        <div class="<?= $circleClass; ?> rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i class="bi <?= $icon; ?>"></i>
                                        </div>
                                        <div>
                                            <div class="small fw-bold"><?= htmlspecialchars($title); ?></div>
                                            <p class="small text-muted mb-0"><?= htmlspecialchars($activity['keterangan']); ?></p>
                                            <span class="text-muted" style="font-size: 0.7rem;"><?= $timeLabel; ?></span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center text-muted">Belum ada aktivitas terbaru.</div>
                            <?php endif; ?>
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