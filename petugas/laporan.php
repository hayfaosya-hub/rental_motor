
<?php
session_start();

if (
    !isset($_SESSION['id_user']) ||
    $_SESSION['role'] != 'petugas'
) {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';
$sqlRekap = "SELECT
                COUNT(*) AS total_transaksi,
                SUM(status = 'menunggu') AS menunggu,
                SUM(status = 'disetujui') AS disetujui,
                SUM(status = 'ditolak') AS ditolak,
                SUM(status = 'selesai') AS selesai,
                COALESCE(
                    SUM(
                        CASE
                            WHEN status = 'selesai'
                            THEN total_harga
                            ELSE 0
                        END
                    ), 0
                ) AS total_pendapatan
             FROM penyewaan";

$queryRekap = mysqli_query($koneksi, $sqlRekap);

if (!$queryRekap) {
    die("Gagal mengambil laporan: " . mysqli_error($koneksi));
}

$rekap = mysqli_fetch_assoc($queryRekap);
$sqlData = "SELECT
                penyewaan.*,
                user.nama,
                motor.nama_motor,
                motor.merk
            FROM penyewaan
            JOIN user ON penyewaan.id_user = user.id_user
            JOIN motor ON penyewaan.id_motor = motor.id_motor
            ORDER BY penyewaan.id_sewa DESC";

$queryData = mysqli_query($koneksi, $sqlData);

if (!$queryData) {
    die("Gagal mengambil data transaksi: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laporan Penyewaan - Rental Motor</title>

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

        .card {
            border: none;
            border-radius: 10px;
        }

        .table th {
            background: #334155;
            color: white;
            white-space: nowrap;
        }

        @media print {
            .sidebar,
            .no-print,
            .btn {
                display: none !important;
            }

            .col-md-10 {
                width: 100%;
            }

            body {
                background: white;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-3 no-print">
            <h5 class="text-white mb-4">RENTAL MOTOR</h5>

            <a href="dashboard.php">Dashboard</a>
            <a href="peminjaman.php">Data Penyewaan</a>
            <a href="pengembalian.php">Pengembalian</a>
            <a href="laporan.php" class="active">Laporan</a>
            <a href="../logout.php">Logout</a>
        </div>
        <div class="col-md-10 p-4">

            <div class="d-flex justify-content-between
                        align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <h3 class="fw-bold">Laporan Penyewaan</h3>
                    <p class="text-secondary mb-0">
                        Rekap transaksi Rental Motor.
                    </p>
                </div>

                <button onclick="window.print()"
                        class="btn btn-dark no-print">
                    Cetak Laporan
                </button>
            </div>
            <div class="row g-3 mb-4">

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm p-4 h-100">
                        <p class="text-secondary mb-2">
                            Total Transaksi
                        </p>
                        <h3 class="fw-bold">
                            <?= (int) $rekap['total_transaksi'] ?>
                        </h3>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm p-4 h-100">
                        <p class="text-secondary mb-2">
                            Penyewaan Menunggu
                        </p>
                        <h3 class="fw-bold">
                            <?= (int) $rekap['menunggu'] ?>
                        </h3>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm p-4 h-100">
                        <p class="text-secondary mb-2">
                            Sedang Disewa
                        </p>
                        <h3 class="fw-bold">
                            <?= (int) $rekap['disetujui'] ?>
                        </h3>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm p-4 h-100">
                        <p class="text-secondary mb-2">
                            Penyewaan Ditolak
                        </p>
                        <h3 class="fw-bold">
                            <?= (int) $rekap['ditolak'] ?>
                        </h3>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm p-4 h-100">
                        <p class="text-secondary mb-2">
                            Pengembalian Selesai
                        </p>
                        <h3 class="fw-bold">
                            <?= (int) $rekap['selesai'] ?>
                        </h3>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm p-4 h-100">
                        <p class="text-secondary mb-2">
                            Total Nilai Sewa Selesai
                        </p>
                        <h3 class="fw-bold">
                            Rp<?= number_format(
                                (float) $rekap['total_pendapatan'],
                                0,
                                ',',
                                '.'
                            ) ?>
                        </h3>
                    </div>
                </div>

            </div>
            <div class="card shadow-sm p-3">

                <h5 class="fw-bold mb-3">
                    Detail Transaksi Penyewaan
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered
                                  table-hover align-middle">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Penyewa</th>
                                <th>Motor</th>
                                <th>Tanggal Sewa</th>
                                <th>Tanggal Kembali</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (mysqli_num_rows($queryData) > 0) { ?>
                            <?php $no = 1; ?>

                            <?php while ($data = mysqli_fetch_assoc($queryData)) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>

                                    <td>
                                        <?= htmlspecialchars($data['nama']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['nama_motor']) ?>
                                        <br>
                                        <small class="text-secondary">
                                            <?= htmlspecialchars($data['merk']) ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['tanggal_sewa']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['tanggal_kembali']) ?>
                                    </td>

                                    <td>
                                        Rp<?= number_format(
                                            (float) $data['total_harga'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?php
                                        $status = $data['status'];

                                        if ($status == 'menunggu') {
                                            $badge = 'bg-warning text-dark';
                                        } elseif ($status == 'disetujui') {
                                            $badge = 'bg-success';
                                        } elseif ($status == 'ditolak') {
                                            $badge = 'bg-danger';
                                        } else {
                                            $badge = 'bg-secondary';
                                        }
                                        ?>

                                        <span class="badge <?= $badge ?>">
                                            <?= htmlspecialchars(ucfirst($status)) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    Belum ada transaksi penyewaan.
                                </td>
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
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
