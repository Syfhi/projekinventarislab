<?php
session_start();
include '../config/koneksi.php';
include '../config/alert.php';

if(!isset($_SESSION['login'])){
    header("Location: login.php");
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'all';
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

$conditions = [];
if($q !== ''){
    $safe = mysqli_real_escape_string($conn, $q);
    $conditions[] = "(r.keterangan LIKE '%$safe%' OR b.nama_barang LIKE '%$safe%' OR u.nama LIKE '%$safe%')";
}

if($jenis !== 'all'){
    switch($jenis){
        case 'tambah':
            $conditions[] = "r.keterangan LIKE '%ditambahkan%'";
            break;
        case 'edit':
            $conditions[] = "(r.keterangan LIKE '%diubah%' OR r.keterangan LIKE '%diperbarui%')";
            break;
        case 'hapus':
            $conditions[] = "r.keterangan LIKE '%dihapus%'";
            break;
        case 'peminjaman':
            $conditions[] = "r.keterangan LIKE '%dipinjam%'";
            break;
        case 'pengembalian':
            $conditions[] = "r.keterangan LIKE '%dikembalikan%'";
            break;
    }
}

$where = '';
if(count($conditions) > 0){
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

$activities = mysqli_query($conn, "SELECT r.*, b.nama_barang, u.nama AS peminjam, p.status FROM riwayat r LEFT JOIN peminjaman p ON r.id_peminjaman = p.id_peminjaman LEFT JOIN barang b ON p.id_barang = b.id_barang LEFT JOIN users u ON p.id_user = u.id_user $where ORDER BY r.created_at DESC, r.tanggal DESC LIMIT 100");

$summaryTotal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM riwayat"))['total'];
$summaryToday = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM riwayat WHERE tanggal = CURDATE()"))['total'];
$summaryAdded = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM riwayat WHERE keterangan LIKE '%ditambahkan%'") )['total'];
$summaryDeleted = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM riwayat WHERE keterangan LIKE '%dihapus%'") )['total'];
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

        .activity-list {
            max-height: 520px;
            overflow-y: auto;
            padding-right: 8px;
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
            <a href="inventaris.php" class="nav-link">
                <i class="bi bi-box-seam"></i> Inventaris
            </a>
            <a href="riwayat.php" class="nav-link active">
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
    <h4 class="fw-bold mb-0">Riwayat Aktivitas</h4>
    <div class="d-flex align-items-center gap-3">
        <div class="search-wrapper">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control shadow-none" placeholder="Cari aktivitas...">
        </div>
        <img src="https://ui-avatars.com/api/?name=Admin+Lab&background=0D8ABC&color=fff" class="rounded-circle" width="35">
    </div>
</header>

<div class="row g-4">

    <!-- Filter Section -->
    <div class="col-12">
        <div class="card">
            <form class="d-flex flex-wrap gap-3 justify-content-between" method="GET" action="riwayat.php">

                <div class="d-flex gap-2 flex-wrap">
                    <input type="text" name="q" class="form-control" placeholder="Cari aktivitas atau nama barang..." value="<?= htmlspecialchars($q); ?>">

                    <select class="form-select" name="jenis">
                        <option value="all" <?= $jenis === 'all' ? 'selected' : ''; ?>>Semua Aktivitas</option>
                        <option value="tambah" <?= $jenis === 'tambah' ? 'selected' : ''; ?>>Tambah Barang</option>
                        <option value="edit" <?= $jenis === 'edit' ? 'selected' : ''; ?>>Edit Barang</option>
                        <option value="hapus" <?= $jenis === 'hapus' ? 'selected' : ''; ?>>Hapus Barang</option>
                        <option value="peminjaman" <?= $jenis === 'peminjaman' ? 'selected' : ''; ?>>Peminjaman</option>
                        <option value="pengembalian" <?= $jenis === 'pengembalian' ? 'selected' : ''; ?>>Pengembalian</option>
                    </select>

                    <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from); ?>">
                    <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to); ?>">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-funnel"></i> Terapkan Filter
                    </button>
                    <a href="riwayat.php" class="btn btn-light">Reset</a>
                </div>

            </form>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="col-12 col-xl-8">
        <div class="card">
            <div class="card-body">

                <h5 class="fw-bold mb-4">Log Aktivitas</h5>

                <div class="activity-list d-flex flex-column gap-4">
                    <?php if(mysqli_num_rows($activities) > 0): ?>
                        <?php while($activity = mysqli_fetch_assoc($activities)): ?>
                            <?php
                                $label = 'Aktivitas';
                                $icon = 'bi-arrow-repeat';
                                $circleClass = 'bg-warning-subtle text-warning';
                                $text = htmlspecialchars($activity['keterangan']);

                                if(stripos($activity['keterangan'], 'ditambahkan') !== false){
                                    $label = 'Barang Ditambahkan';
                                    $icon = 'bi-plus-lg';
                                    $circleClass = 'bg-success-subtle text-success';
                                } elseif(stripos($activity['keterangan'], 'dihapus') !== false){
                                    $label = 'Barang Dihapus';
                                    $icon = 'bi-trash';
                                    $circleClass = 'bg-danger-subtle text-danger';
                                } elseif(stripos($activity['keterangan'], 'dikembalikan') !== false){
                                    $label = 'Pengembalian';
                                    $icon = 'bi-person-check';
                                    $circleClass = 'bg-info-subtle text-info';
                                } elseif(stripos($activity['keterangan'], 'dipinjam') !== false){
                                    $label = 'Peminjaman Barang';
                                    $icon = 'bi-arrow-left-right';
                                    $circleClass = 'bg-info-subtle text-info';
                                } elseif(stripos($activity['keterangan'], 'diubah') !== false || stripos($activity['keterangan'], 'diperbarui') !== false){
                                    $label = 'Data Diperbarui';
                                    $icon = 'bi-pencil';
                                    $circleClass = 'bg-warning-subtle text-warning';
                                }

                                $activityTime = date('d M Y', strtotime($activity['tanggal']));
                            ?>
                            <div class="d-flex gap-3">
                                <div class="<?= $circleClass; ?> rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 45px; height: 45px;">
                                    <i class="bi <?= $icon; ?>"></i>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= $label; ?></div>
                                    <p class="text-muted small mb-1"><?= $text; ?></p>
                                    <span class="text-muted" style="font-size: 0.75rem;"><?= $activityTime; ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-muted">Belum ada aktivitas yang cocok.</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Summary Panel -->
    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Ringkasan Aktivitas</h5>

                <div class="d-flex flex-column gap-3">

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Aktivitas</span>
                        <span class="fw-bold"><?= number_format($summaryTotal); ?></span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Hari Ini</span>
                        <span class="fw-bold text-primary"><?= number_format($summaryToday); ?></span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Barang Ditambahkan</span>
                        <span class="fw-bold text-success"><?= number_format($summaryAdded); ?></span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Barang Dihapus</span>
                        <span class="fw-bold text-danger"><?= number_format($summaryDeleted); ?></span>
                    </div>

                </div>

                <hr>

                <small class="text-muted">
                    Data ini menampilkan aktivitas terbaru dalam sistem inventaris laboratorium.
                </small>

            </div>
        </div>
    </div>

</div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>