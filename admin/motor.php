
<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$query = mysqli_query($koneksi, "
    SELECT motor.*, kategori.nama_kategori
    FROM motor
    LEFT JOIN kategori
    ON motor.id_kategori = kategori.id_kategori
    ORDER BY id_motor DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Motor - Rental Motor</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

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

        .content {
            padding: 25px;
        }

        .table th {
            background: #334155;
            color: white;
        }

        .motor-img {
            width: 75px;
            height: 55px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-3">
            <h5 class="text-white mb-4">RENTAL MOTOR</h5>

            <a href="dashboard.php">Dashboard</a>
            <a href="user.php">Data User</a>
            <a href="motor.php" class="active">Data Motor</a>
            <a href="kategori.php">Kategori</a>
            <a href="peminjaman.php">Data Peminjaman</a>
            <a href="pengembalian.php">Pengembalian</a>
            <a href="log_aktivitas.php">Log Aktivitas</a>
            <a href="../logout.php">Logout</a>
        </div>
        <div class="col-md-10 content">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3>Data Motor</h3>
                    <p class="text-secondary">
                        Kelola data motor yang disewakan.
                    </p>
                </div>

                <a href="motor_tambah.php" class="btn btn-dark">
                    + Tambah Motor
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Nama Motor</th>
                                    <th>Merk</th>
                                    <th>Kategori</th>
                                    <th>Harga/Hari</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = 1;

                                if (mysqli_num_rows($query) > 0) {
                                    while ($motor = mysqli_fetch_assoc($query)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?php if (!empty($motor['gambar'])) { ?>
                                            <img
                                                src="../assets/img/<?= htmlspecialchars($motor['gambar']); ?>"
                                                class="motor-img"
                                                alt="Motor"
                                            >
                                        <?php } else { ?>
                                            <span class="text-secondary">
                                                Tidak ada gambar
                                            </span>
                                        <?php } ?>
                                    </td>

                                    <td><?= htmlspecialchars($motor['nama_motor']); ?></td>
                                    <td><?= htmlspecialchars($motor['merk']); ?></td>
                                    <td><?= htmlspecialchars($motor['nama_kategori'] ?? '-'); ?></td>

                                    <td>
                                        Rp <?= number_format($motor['harga_sewa'], 0, ',', '.'); ?>
                                    </td>

                                    <td><?= $motor['stok']; ?></td>

                                    <td>
                                        <?php if ($motor['status'] == 'tersedia') { ?>
                                            <span class="badge bg-success">Tersedia</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">
                                                <?= htmlspecialchars($motor['status']); ?>
                                            </span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <a
                                            href="motor_edit.php?id=<?= $motor['id_motor']; ?>"
                                            class="btn btn-sm btn-warning mb-1"
                                        >Edit</a>

                                        
                                    <form action="motor_hapus.php" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus motor ini?')">

                                        <input type="hidden" name="id_motor"
                                            value="<?= $motor['id_motor']; ?>">

                                        <button type="submit" class="btn btn-sm btn-danger mb-1">
                                            Hapus
                                        </button>
                                    </form>

                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        Belum ada data motor.
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
