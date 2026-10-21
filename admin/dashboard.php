
<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$qUser = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user");
$totalUser = mysqli_fetch_assoc($qUser)['total'];

$qMotor = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM motor");
$totalMotor = mysqli_fetch_assoc($qMotor)['total'];

$qPeminjaman = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM penyewaan"
);
$totalPeminjaman = mysqli_fetch_assoc($qPeminjaman)['total'];

$qPengembalian = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM penyewaan
     WHERE status = 'selesai'"
);
$totalPengembalian = mysqli_fetch_assoc($qPengembalian)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Rental Motor</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

        
    <style>
        body { background: #f1f5f9; color: #1e293b; }
        .sidebar { min-height: 100vh; background: #1e293b; }
        .sidebar a {
            display: block; color: white; text-decoration: none;
            padding: 12px; border-radius: 6px; margin-bottom: 5px;
        }
        .sidebar a:hover, .sidebar a.active { background: #475569; }
        .content { padding: 25px; }
        .table th { background: #334155; color: white; }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-2 sidebar p-3">
            <h5 class="text-white mb-4">RENTAL MOTOR</h5>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="user.php">Data User</a>
            <a href="motor.php">Data Motor</a>
            <a href="kategori.php">Kategori</a>
            <a href="peminjaman.php" >Data Peminjaman</a>
            <a href="pengembalian.php">Pengembalian</a>
            <a href="log_aktivitas.php">Log Aktivitas</a>
            <a href="../logout.php">Logout</a>
        </div>

        <div class="col-md-9 col-lg-10 p-4">

            <h3 class="fw-bold judul">Dashboard Admin</h3>

            <p class="text-secondary">
                Selamat datang,
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?>!
            </p>

            <div class="row g-3 mt-3">

                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm p-4">
                        <p class="text-secondary mb-2">
                            Total User
                        </p>
                        <h2 class="fw-bold">
                            <?= $totalUser ?>
                        </h2>
                        <a href="user.php" class="text-decoration-none">
                            Kelola User →
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm p-4">
                        <p class="text-secondary mb-2">
                            Total Motor
                        </p>
                        <h2 class="fw-bold">
                            <?= $totalMotor ?>
                        </h2>
                        <a href="motor.php" class="text-decoration-none">
                            Kelola Motor →
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm p-4">
                        <p class="text-secondary mb-2">
                            Data Peminjaman
                        </p>
                        <h2 class="fw-bold"><?= $totalPeminjaman ?></h2>
                        <a href="peminjaman.php" class="text-decoration-none">
                            Kelola Peminjaman →
                        </a>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm p-4">
                        <p class="text-secondary mb-2">
                            Data Pengembalian
                        </p>
                        <h2 class="fw-bold"><?= $totalPengembalian ?></h2>
                        <a href="pengembalian.php" class="text-decoration-none">
                            Kelola Pengembalian →
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm p-4 mt-4">
                <h5 class="fw-bold">
                    <i class="me-2">📋</i> Aktivitas Terbaru
                </h5>

                <p class="text-secondary mb-0">
                    Lihat dan pantau aktivitas pengelolaan data
                    pada sistem Rental Motor.
                </p>

                <a href="log_aktivitas.php"
                class="btn btn-sm mt-3 text-white align-self-start"
                style="background:#334155;">
                    Lihat Aktivitas
                </a>
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
