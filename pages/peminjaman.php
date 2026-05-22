<?php
session_start();
include '../config/koneksi.php';
include '../config/alert.php';

if(!isset($_SESSION['login'])){
    header("Location: login.php");
    exit;
}

// Query parameters
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

$conditions = [];
if($q !== ''){
    $safe = mysqli_real_escape_string($conn, $q);
    $conditions[] = "(u.nama LIKE '%$safe%' OR b.nama_barang LIKE '%$safe%' OR p.keterangan LIKE '%$safe%')";
}

if($status !== 'all'){
    switch($status){
        case 'Dipinjam':
            $conditions[] = "p.status = 'Sedang Dipinjam'";
            break;
        case 'Dikembalikan':
            $conditions[] = "p.status = 'Dikembalikan'";
            break;
        case 'Terlambat':
            $conditions[] = "p.status != 'Dikembalikan' AND p.tanggal_kembali < CURDATE()";
            break;
    }
}

if($from !== ''){
    $safeFrom = mysqli_real_escape_string($conn, $from);
    $conditions[] = "p.tanggal_pinjam >= '$safeFrom'";
}

if($to !== ''){
    $safeTo = mysqli_real_escape_string($conn, $to);
    $conditions[] = "p.tanggal_kembali <= '$safeTo'";
}

$where = '';
if(count($conditions) > 0){
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

$peminjaman = mysqli_query($conn, "SELECT p.*, b.nama_barang, u.nama AS peminjam, b.kondisi AS kondisi_barang FROM peminjaman p LEFT JOIN barang b ON p.id_barang = b.id_barang LEFT JOIN users u ON p.id_user = u.id_user $where ORDER BY p.tanggal_pinjam DESC");
$availableBarang = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");

function formatStatus($row) {
    if($row['status'] !== 'Dikembalikan' && $row['tanggal_kembali'] < date('Y-m-d')){
        return 'Terlambat';
    }
    return $row['status'] === 'Dikembalikan' ? 'Dikembalikan' : 'Dipinjam';
}

function badgeClass($status) {
    if($status === 'Dikembalikan') return 'bg-success-subtle text-success';
    if($status === 'Terlambat') return 'bg-danger-subtle text-danger';
    return 'bg-warning-subtle text-warning';
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
            <a href="dashboard.php" class="nav-link">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="inventaris.php" class="nav-link">
                <i class="bi bi-box-seam"></i> Inventaris
            </a>
            <a href="riwayat.php" class="nav-link">
                <i class="bi bi-clock-history"></i> Riwayat Log
            </a>
            <a href="peminjaman.php" class="nav-link active">
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
    <h4 class="fw-bold mb-0">Manajemen Peminjaman</h4>
    <div class="d-flex align-items-center gap-3">
        <form class="search-wrapper" method="GET" action="peminjaman.php">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control shadow-none" name="q" placeholder="Cari peminjaman..." value="<?= htmlspecialchars($q); ?>">
            <input type="hidden" name="status" value="<?= htmlspecialchars($status); ?>">
            <input type="hidden" name="from" value="<?= htmlspecialchars($from); ?>">
            <input type="hidden" name="to" value="<?= htmlspecialchars($to); ?>">
        </form>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPeminjaman"><i class="bi bi-plus-lg"></i> Tambah Peminjaman</button>
        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']); ?>&background=0D8ABC&color=fff" class="rounded-circle" width="35">
    </div>
</header>

<div class="row g-4">

    <!-- Filter -->
    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex flex-wrap gap-3 justify-content-between">

                <form class="d-flex gap-2 flex-wrap" method="GET" action="peminjaman.php">
                    <input type="hidden" name="q" value="<?= htmlspecialchars($q); ?>">
                    <div>
                        <select class="form-select" name="status">
                            <option value="all" <?= $status === 'all' ? 'selected' : ''; ?>>Semua Status</option>
                            <option value="Dipinjam" <?= $status === 'Dipinjam' ? 'selected' : ''; ?>>Dipinjam</option>
                            <option value="Dikembalikan" <?= $status === 'Dikembalikan' ? 'selected' : ''; ?>>Dikembalikan</option>
                            <option value="Terlambat" <?= $status === 'Terlambat' ? 'selected' : ''; ?>>Terlambat</option>
                        </select>
                    </div>

                    <input type="date" class="form-control" name="from" value="<?= htmlspecialchars($from); ?>">
                    <input type="date" class="form-control" name="to" value="<?= htmlspecialchars($to); ?>">

                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-funnel"></i> Terapkan Filter
                    </button>
                </form>

                <a href="peminjaman.php" class="btn btn-light">Reset</a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">

                <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="fw-bold mb-0">Daftar Peminjaman</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Peminjam</th>
                                <th>Barang</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th class="text-end px-4">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                                <?php if(mysqli_num_rows($peminjaman) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($peminjaman)): ?>
                                        <?php $statusLabel = formatStatus($row); $badge = badgeClass($statusLabel); ?>
                                        <tr>
                                            <td class="px-4">
                                                <div class="fw-bold"><?= htmlspecialchars($row['peminjam']); ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($row['keterangan']); ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= $row['nama_barang'] ? htmlspecialchars($row['nama_barang']) : '<span class="text-muted">[Barang sudah dikembalikan]</span>'; ?></div>
                                                <div class="text-muted small">Kondisi: <?= $row['kondisi_barang'] ? htmlspecialchars($row['kondisi_barang']) : '-'; ?></div>
                                            </td>
                                            <td><?= date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td><?= date('d M Y', strtotime($row['tanggal_kembali'])); ?></td>
                                            <td>
                                                <span class="badge <?= $badge; ?> badge-status text-uppercase" style="font-size: 0.65rem;">
                                                    <?= $statusLabel; ?>
                                                </span>
                                            </td>
                                            <td class="text-end px-4">
                                                <button class="btn btn-light btn-sm" title="Lihat" data-bs-toggle="modal" data-bs-target="#modalDetailPeminjaman"
                                                    data-peminjam="<?= htmlspecialchars($row['peminjam'], ENT_QUOTES); ?>"
                                                    data-barang="<?= htmlspecialchars($row['nama_barang'], ENT_QUOTES); ?>"
                                                    data-tanggal_pinjam="<?= htmlspecialchars($row['tanggal_pinjam'], ENT_QUOTES); ?>"
                                                    data-tanggal_kembali="<?= htmlspecialchars($row['tanggal_kembali'], ENT_QUOTES); ?>"
                                                    data-durasi="<?= htmlspecialchars($row['durasi'], ENT_QUOTES); ?>"
                                                    data-status="<?= htmlspecialchars($statusLabel, ENT_QUOTES); ?>"
                                                    data-keterangan="<?= htmlspecialchars($row['keterangan'], ENT_QUOTES); ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <?php if($row['status'] !== 'Dikembalikan'): ?>
                                                    <button class="btn btn-light btn-sm text-success" title="Kembalikan" data-bs-toggle="modal" data-bs-target="#modalKembalikanBarang"
                                                        data-id_peminjaman="<?= $row['id_peminjaman']; ?>"
                                                        data-id_barang="<?= $row['id_barang']; ?>"
                                                        data-barang="<?= htmlspecialchars($row['nama_barang'], ENT_QUOTES); ?>"
                                                        data-peminjam="<?= htmlspecialchars($row['peminjam'], ENT_QUOTES); ?>">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada data peminjaman.</td>
                                    </tr>
                                <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="p-3 bg-light d-flex justify-content-between align-items-center">
                    <small class="text-muted">Menampilkan 1 - 10 dari 80 data</small>
                    <div>
                        <button class="btn btn-sm btn-light">Prev</button>
                        <button class="btn btn-sm btn-primary">1</button>
                        <button class="btn btn-sm btn-light">2</button>
                        <button class="btn btn-sm btn-light">Next</button>
                    </div>
                </div>

            </div>
            </div>
        </div>
    </div>

</div>

    <!-- Modal Tambah Peminjaman -->
    <div class="modal fade" id="modalTambahPeminjaman" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="../process/proses_peminjaman.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_barang" class="form-label">Pilih Barang</label>
                            <select class="form-select" id="id_barang" name="id_barang" required>
                                <option value="">Pilih barang tersedia</option>
                                <?php while($barang = mysqli_fetch_assoc($availableBarang)): ?>
                                    <option value="<?= $barang['id_barang']; ?>"><?= htmlspecialchars($barang['nama_barang']); ?> (Stok: <?= $barang['stok']; ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="durasi" class="form-label">Durasi (hari)</label>
                            <input type="number" class="form-control" id="durasi" name="durasi" min="1" value="7" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Peminjaman untuk praktikum" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="pinjam" class="btn btn-primary">Pinjam</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kembalikan Barang -->
    <div class="modal fade" id="modalKembalikanBarang" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kembalikan Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="../process/proses_pengembalian.php">
                    <div class="modal-body">
                        <input type="hidden" id="kembalikan_id_peminjaman" name="id_peminjaman">
                        <input type="hidden" id="kembalikan_id_barang" name="id_barang">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="kembalikan_nama_barang" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peminjam</label>
                            <input type="text" class="form-control" id="kembalikan_peminjam" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="kondisi" class="form-label">Kondisi Setelah Dikembalikan</label>
                            <select class="form-select" id="kembalikan_kondisi" name="kondisi" required>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Rusak">Rusak</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="kembalikan" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Peminjaman -->
    <div class="modal fade" id="modalDetailPeminjaman" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Peminjam</label>
                        <input type="text" class="form-control" id="detail_peminjam" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Barang</label>
                        <input type="text" class="form-control" id="detail_barang" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="text" class="form-control" id="detail_tanggal_pinjam" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kembali</label>
                        <input type="text" class="form-control" id="detail_tanggal_kembali" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durasi</label>
                        <input type="text" class="form-control" id="detail_durasi" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <input type="text" class="form-control" id="detail_status" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" id="detail_keterangan" rows="3" readonly></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const detailModal = document.getElementById('modalDetailPeminjaman');
            if(detailModal){
                detailModal.addEventListener('show.bs.modal', function(event){
                    const button = event.relatedTarget;
                    if(!button) return;

                    document.getElementById('detail_peminjam').value = button.getAttribute('data-peminjam');
                    document.getElementById('detail_barang').value = button.getAttribute('data-barang');
                    document.getElementById('detail_tanggal_pinjam').value = button.getAttribute('data-tanggal_pinjam');
                    document.getElementById('detail_tanggal_kembali').value = button.getAttribute('data-tanggal_kembali');
                    document.getElementById('detail_durasi').value = button.getAttribute('data-durasi') + ' hari';
                    document.getElementById('detail_status').value = button.getAttribute('data-status');
                    document.getElementById('detail_keterangan').value = button.getAttribute('data-keterangan');
                });
            }

            const returnModal = document.getElementById('modalKembalikanBarang');
            if(returnModal){
                returnModal.addEventListener('show.bs.modal', function(event){
                    const button = event.relatedTarget;
                    if(!button) return;

                    document.getElementById('kembalikan_id_peminjaman').value = button.getAttribute('data-id_peminjaman');
                    document.getElementById('kembalikan_id_barang').value = button.getAttribute('data-id_barang');
                    document.getElementById('kembalikan_nama_barang').value = button.getAttribute('data-barang');
                    document.getElementById('kembalikan_peminjam').value = button.getAttribute('data-peminjam');
                });
            }
        });
    </script>
</body>
</html>