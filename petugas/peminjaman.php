
<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$sql = "SELECT penyewaan.*, user.nama, motor.nama_motor
        FROM penyewaan
        JOIN user ON penyewaan.id_user = user.id_user
        JOIN motor ON penyewaan.id_motor = motor.id_motor
        ORDER BY penyewaan.id_sewa DESC";

$query = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Peminjaman - Petugas</title>

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
        .sidebar a.active {
            background: #334155;
            color: white;
        }

        .judul {
            color: #334155;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .table th {
            background: #334155;
            color: white;
            white-space: nowrap;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar p-3">
            <h5 class="text-white mb-4">🏍 Rental Motor</h5>

            <a href="dashboard.php">Dashboard</a>
            <a href="peminjaman.php" class="active">
                Persetujuan Peminjaman
            </a>
            <a href="pengembalian.php">Pengembalian</a>
            <a href="laporan.php">Laporan</a>

            <hr class="border-secondary">

            <a href="../logout.php">Logout</a>
        </div>
        <div class="col-md-9 col-lg-10 p-4">

            <h3 class="fw-bold judul">Data Peminjaman</h3>

            <p class="text-secondary">
                Periksa dan proses permintaan penyewaan motor.
            </p>

            <?php if (isset($_GET['pesan'])) { ?>
                <?php if ($_GET['pesan'] == 'berhasil') { ?>
                    <div class="alert alert-success">
                        Status penyewaan berhasil diperbarui.
                    </div>
                <?php } elseif ($_GET['pesan'] == 'gagal') { ?>
                    <div class="alert alert-warning">
                        Status gagal diperbarui atau permintaan sudah diproses.
                    </div>
                <?php } ?>
            <?php } ?>

            <div class="card shadow-sm p-4 mt-3">

                <h5 class="fw-bold mb-3">
                    📋 Daftar Penyewaan Motor
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Penyewa</th>
                                <th>Motor</th>
                                <th>Tanggal Sewa</th>
                                <th>Tanggal Kembali</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                        $no = 1;

                        if ($query && mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <?= htmlspecialchars($data['nama']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama_motor']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['tanggal_sewa']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['tanggal_kembali']) ?>
                                </td>

                                <td>
                                    Rp <?= number_format(
                                        $data['total_harga'], 0, ',', '.'
                                    ) ?>
                                </td>

                                <td>
                                    <?php if ($data['status'] == 'menunggu') { ?>
                                        <span class="badge bg-warning text-dark">
                                            Menunggu
                                        </span>
                                    <?php } elseif ($data['status'] == 'disetujui') { ?>
                                        <span class="badge bg-success">
                                            Disetujui
                                        </span>
                                    <?php } elseif ($data['status'] == 'ditolak') { ?>
                                        <span class="badge bg-danger">
                                            Ditolak
                                        </span>
                                    <?php } elseif ($data['status'] == 'selesai') { ?>
                                        <span class="badge bg-secondary">
                                            Selesai
                                        </span>
                                    <?php } ?>
                                </td>

                                <td>
                                    <?php if ($data['status'] == 'menunggu') { ?>
                                        <a
                                            href="peminjaman_status.php?id=<?= (int) $data['id_sewa'] ?>"
                                            class="btn btn-sm text-white"
                                            style="background:#334155;">
                                            Proses
                                        </a>
                                    <?php } else { ?>
                                        <span class="text-secondary small">
                                            Sudah diproses
                                        </span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="8"
                                    class="text-center text-secondary py-4">
                                    Belum ada data peminjaman.
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>
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
