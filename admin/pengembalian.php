
<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$sql = "SELECT penyewaan.*, user.nama, motor.nama_motor
        FROM penyewaan
        JOIN user ON penyewaan.id_user = user.id_user
        JOIN motor ON penyewaan.id_motor = motor.id_motor
        WHERE penyewaan.status IN ('disetujui', 'selesai')
        ORDER BY penyewaan.id_sewa DESC";

$query = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Pengembalian - Rental Motor</title>

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
            <a href="dashboard.php">Dashboard</a>
            <a href="user.php">Data User</a>
            <a href="motor.php">Data Motor</a>
            <a href="kategori.php">Kategori</a>
            <a href="peminjaman.php">Data Peminjaman</a>
            <a href="pengembalian.php" class="active" >Pengembalian</a>
            <a href="log_aktivitas.php">Log Aktivitas</a>
            <a href="../logout.php">Logout</a>
        </div>
        <div class="col-md-9 col-lg-10 p-4">

            <h3 class="fw-bold judul">Data Pengembalian</h3>

            <p class="text-secondary">
                Kelola pengembalian motor pelanggan.
            </p>

            <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil') { ?>
                <div class="alert alert-success">
                    Pengembalian berhasil dikonfirmasi.
                </div>
            <?php } elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal') { ?>
                <div class="alert alert-warning">
                    Pengembalian gagal diproses atau sudah dikonfirmasi.
                </div>
            <?php } ?>

            <div class="card shadow-sm p-4 mt-3">

                <h5 class="fw-bold mb-3">
                    📋 Daftar Pengembalian Motor
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
                                        <?php if ($data['status'] == 'disetujui') { ?>
                                            <span class="badge bg-success">
                                                Sedang Disewa
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">
                                                Selesai
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
                                        Belum ada data pengembalian.
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
