
<?php
session_start();

if (
    !isset($_SESSION['id_user']) ||
    $_SESSION['role'] != 'penyewa'
) {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$id_user = (int) $_SESSION['id_user'];

$sql = "SELECT penyewaan.*, motor.nama_motor, motor.merk,
               motor.gambar
        FROM penyewaan
        JOIN motor ON penyewaan.id_motor = motor.id_motor
        WHERE penyewaan.id_user = ?
        ORDER BY penyewaan.id_sewa DESC";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);

$query = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Riwayat Penyewaan - Rental Motor</title>

    <link rel="stylesheet"
          href="../assets/css/bootstrap.min.css">

    <style>
        body {
            background: #f1f5f9;
            color: #1e293b;
        }

        .navbar {
            background: #1e293b;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .table th {
            white-space: nowrap;
        }

        .motor-img {
            width: 65px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../index.php">
            RENTAL MOTOR
        </a>

        <div>
            <a href="../index.php"
               class="btn btn-outline-light btn-sm">
                Halaman Utama
            </a>

            <a href="../logout.php"
               class="btn btn-light btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <div class="mb-4">
        <h2 class="fw-bold">Riwayat Penyewaan</h2>
        <p class="text-secondary">
            Halo, <?= htmlspecialchars($_SESSION['nama']) ?>.
            Berikut daftar penyewaan motor kamu.
        </p>
    </div>

    <?php if (isset($_GET['berhasil'])) { ?>
        <div class="alert alert-success">
            Pengajuan penyewaan berhasil dikirim.
            Silakan tunggu konfirmasi petugas.
        </div>
    <?php } ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Motor</th>
                            <th>Tanggal Sewa</th>
                            <th>Tanggal Kembali</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (mysqli_num_rows($query) > 0) { ?>

                        <?php $no = 1; ?>

                        <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($data['gambar'])) { ?>
                                            <img
                                                src="../assets/img/<?= htmlspecialchars($data['gambar']) ?>"
                                                class="motor-img"
                                                alt="Motor">
                                        <?php } ?>

                                        <div>
                                            <div class="fw-bold">
                                                <?= htmlspecialchars($data['nama_motor']) ?>
                                            </div>
                                            <small class="text-secondary">
                                                <?= htmlspecialchars($data['merk']) ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['tanggal_sewa']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['tanggal_kembali']) ?>
                                </td>

                                <td class="fw-bold">
                                    Rp<?= number_format(
                                        $data['total_harga'],
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
                                        $label = 'Menunggu';
                                    } elseif ($status == 'disetujui') {
                                        $badge = 'bg-success';
                                        $label = 'Disetujui';
                                    } elseif ($status == 'ditolak') {
                                        $badge = 'bg-danger';
                                        $label = 'Ditolak';
                                    } elseif ($status == 'selesai') {
                                        $badge = 'bg-secondary';
                                        $label = 'Selesai';
                                    } else {
                                        $badge = 'bg-dark';
                                        $label = ucfirst($status);
                                    }
                                    ?>

                                    <span class="badge <?= $badge ?>">
                                        <?= htmlspecialchars($label) ?>
                                    </span>
                                </td>
                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="6"
                                class="text-center py-5">
                                <h5>Belum Ada Penyewaan</h5>
                                <p class="text-secondary">
                                    Kamu belum memiliki transaksi penyewaan motor.
                                </p>

                                <a href="../index.php"
                                   class="btn btn-dark">
                                    Pilih Motor
                                </a>
                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="mt-3">
        <a href="../index.php"
           class="btn btn-outline-dark">
            Kembali ke Halaman Utama
        </a>
    </div>

</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
