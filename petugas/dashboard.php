
<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$qMenunggu = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM penyewaan WHERE status = 'menunggu'"
);
$totalMenunggu = mysqli_fetch_assoc($qMenunggu)['total'];

$qDisetujui = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM penyewaan WHERE status = 'disetujui'"
);
$totalDisetujui = mysqli_fetch_assoc($qDisetujui)['total'];

$qSelesai = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM penyewaan WHERE status = 'selesai'"
);
$totalSelesai = mysqli_fetch_assoc($qSelesai)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Petugas - Rental Motor</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

    <style>
        body {
            background: #f1f5f9;
            color: #1e293b;
        }

        .sidebar {
            background: #1e293b;
            min-height: 100vh;
        }

        .sidebar a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px;
            margin-bottom: 5px;
            border-radius: 8px;
        }

        .sidebar a:hover,
        .sidebar .active {
            background: #334155;
            color: white;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .judul {
            color: #334155;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 sidebar p-3">
            <h5 class="text-white mb-4">🏍 Rental Motor</h5>

            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="peminjaman.php">Persetujuan Peminjaman</a>
            <a href="pengembalian.php">Pantau Pengembalian</a>
            <a href="laporan.php">Cetak Laporan</a>

            <hr class="border-secondary">

            <a href="../logout.php">Logout</a>
        </div>

        <div class="col-md-9 col-lg-10 p-4">

            <h3 class="fw-bold judul">Dashboard Petugas</h3>

            <p class="text-secondary">
                Selamat datang,
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Petugas') ?>!
            </p>

            <div class="row g-3 mt-3">

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm p-4">
                        <p class="text-secondary mb-2">
                            Menunggu Persetujuan
                        </p>
                        <h2 class="fw-bold"><?= $totalMenunggu ?></h2>
                        <a href="peminjaman.php" class="text-decoration-none">
                            Periksa Peminjaman →
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm p-4">
                        <p class="text-secondary mb-2">
                            Sedang Disewa
                        </p>
                        <h2 class="fw-bold"><?= $totalDisetujui ?></h2>
                        <a href="pengembalian.php" class="text-decoration-none">
                            Pantau Pengembalian →
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm p-4">
                        <p class="text-secondary mb-2">
                            Selesai
                        </p>
                        <h2 class="fw-bold"><?= $totalSelesai ?></h2>
                        <a href="laporan.php" class="text-decoration-none">
                            Lihat Laporan →
                        </a>
                    </div>
                </div>

            </div>

            <div class="card shadow-sm p-4 mt-4">
                <h5 class="fw-bold">📋 Tugas Petugas</h5>
                <p class="text-secondary mb-0">
                    Periksa pengajuan penyewaan, setujui atau tolak
                    permintaan, pantau pengembalian motor, dan cetak laporan.
                </p>
            </div>

            <p class="text-center text-secondary mt-4 small">
                &copy; <?= date('Y') ?> Rental Motor
            </p>

        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
