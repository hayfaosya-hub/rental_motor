
<?php
session_start();

if (
    !isset($_SESSION['id_user']) ||
    $_SESSION['role'] != 'admin'
) {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$sql = "SELECT log_aktivitas.*, user.nama
        FROM log_aktivitas
        LEFT JOIN user
        ON log_aktivitas.id_user = user.id_user
        ORDER BY log_aktivitas.id_log DESC";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die("Gagal mengambil data log aktivitas: " .
        mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Log Aktivitas - Rental Motor</title>

    <link rel="stylesheet"
          href="../assets/css/bootstrap.min.css">

    <style>
        body {
            background: #f1f5f9;
            color: #1e293b;
        }

        .sidebar {
            min-height: 100vh;
            background: #1e293b;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #475569;
        }

        .table th {
            background: #334155;
            color: white;
            white-space: nowrap;
        }

        .card {
            border: none;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-3">

            <h5 class="text-white mb-4">RENTAL MOTOR</h5>

            <a href="dashboard.php">Dashboard</a>
            <a href="user.php">Data User</a>
            <a href="motor.php">Data Motor</a>
            <a href="kategori.php">Kategori</a>
            <a href="peminjaman.php">Data Peminjaman</a>
            <a href="pengembalian.php">Pengembalian</a>
            <a href="log_aktivitas.php" class="active">
                Log Aktivitas
            </a>
            <a href="../logout.php">Logout</a>

        </div>

        <!-- Konten -->
        <div class="col-md-10 p-4">

            <h3 class="fw-bold">Log Aktivitas</h3>

            <p class="text-secondary">
                Riwayat aktivitas pada sistem Rental Motor.
            </p>

            <div class="card shadow-sm mt-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        Daftar Aktivitas
                    </h5>

                    <div class="table-responsive">

                        <table class="table table-bordered
                                      table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Pengguna</th>
                                    <th>Aktivitas</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php if (mysqli_num_rows($query) > 0) { ?>

                                <?php $no = 1; ?>

                                <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                                    <tr>
                                        <td><?= $no++ ?></td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $data['nama'] ?? 'Pengguna tidak ditemukan'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $data['aktivitas']
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $data['waktu']
                                            ) ?>
                                        </td>
                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="4"
                                        class="text-center py-4">
                                        Belum ada aktivitas yang tercatat.
                                    </td>
                                </tr>

                            <?php } ?>

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

            <p class="text-center text-secondary small mt-4">
                &copy; <?= date('Y') ?> Rental Motor
            </p>

        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
