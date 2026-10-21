
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

$pesan = '';
$id_sewa = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (isset($_POST['ubah_status'])) {
    $id_sewa = (int) $_POST['id_sewa'];
    $status_baru = $_POST['status'];

    if (!in_array($status_baru, ['disetujui', 'ditolak'])) {
        $pesan = 'Status tidak valid.';
    } else {
        mysqli_begin_transaction($koneksi);

        try {
            
            $stmt = mysqli_prepare(
                $koneksi,
                "SELECT id_motor, status
                 FROM penyewaan
                 WHERE id_sewa = ?
                 FOR UPDATE"
            );

            mysqli_stmt_bind_param($stmt, "i", $id_sewa);
            mysqli_stmt_execute($stmt);
            $hasil = mysqli_stmt_get_result($stmt);
            $sewa = mysqli_fetch_assoc($hasil);
            mysqli_stmt_close($stmt);

            if (!$sewa || $sewa['status'] != 'menunggu') {
                throw new Exception(
                    'Penyewaan tidak ditemukan atau sudah diproses.'
                );
            }

            // Jika disetujui, periksa stok motor
            if ($status_baru == 'disetujui') {
                $stmt = mysqli_prepare(
                    $koneksi,
                    "SELECT stok FROM motor
                     WHERE id_motor = ?
                     FOR UPDATE"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "i",
                    $sewa['id_motor']
                );
                mysqli_stmt_execute($stmt);
                $hasil = mysqli_stmt_get_result($stmt);
                $motor = mysqli_fetch_assoc($hasil);
                mysqli_stmt_close($stmt);

                if (!$motor || $motor['stok'] < 1) {
                    throw new Exception(
                        'Stok motor tidak tersedia.'
                    );
                }

                
                $stmt = mysqli_prepare(
                    $koneksi,
                    "UPDATE motor
                     SET stok = stok - 1
                     WHERE id_motor = ? AND stok > 0"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "i",
                    $sewa['id_motor']
                );
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) != 1) {
                    mysqli_stmt_close($stmt);
                    throw new Exception(
                        'Stok motor gagal diperbarui.'
                    );
                }

                mysqli_stmt_close($stmt);
            }

            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE penyewaan
                 SET status = ?
                 WHERE id_sewa = ? AND status = 'menunggu'"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $status_baru,
                $id_sewa
            );
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) != 1) {
                mysqli_stmt_close($stmt);
                throw new Exception(
                    'Status gagal diperbarui.'
                );
            }

            mysqli_stmt_close($stmt);
            mysqli_commit($koneksi);

            header("Location: peminjaman.php?berhasil=1");
            exit;

        } catch (Throwable $e) {
            mysqli_rollback($koneksi);
            $pesan = $e->getMessage();
        }
    }
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT penyewaan.*, user.nama, motor.nama_motor,
            motor.stok
     FROM penyewaan
     JOIN user ON penyewaan.id_user = user.id_user
     JOIN motor ON penyewaan.id_motor = motor.id_motor
     WHERE penyewaan.id_sewa = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id_sewa);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proses Penyewaan</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body style="background:#f1f5f9;color:#1e293b;">

<nav class="navbar navbar-dark" style="background:#1e293b;">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            PETUGAS RENTAL MOTOR
        </a>
        <a href="peminjaman.php" class="btn btn-outline-light btn-sm">
            Kembali
        </a>
    </div>
</nav>

<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width:650px;">
        <div class="card-body p-4">
            <h3 class="mb-4">Proses Penyewaan Motor</h3>

            <?php if ($pesan != ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan) ?>
                </div>
            <?php endif; ?>

            <?php if (!$data): ?>
                <div class="alert alert-warning">
                    Data penyewaan tidak ditemukan.
                </div>
            <?php else: ?>

                <table class="table">
                    <tr>
                        <th>Nama Penyewa</th>
                        <td><?= htmlspecialchars($data['nama']) ?></td>
                    </tr>
                    <tr>
                        <th>Motor</th>
                        <td><?= htmlspecialchars($data['nama_motor']) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Sewa</th>
                        <td><?= htmlspecialchars($data['tanggal_sewa']) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Kembali</th>
                        <td><?= htmlspecialchars($data['tanggal_kembali']) ?></td>
                    </tr>
                    <tr>
                        <th>Total Harga</th>
                        <td>Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= htmlspecialchars($data['status']) ?></td>
                    </tr>
                </table>

                <?php if ($data['status'] == 'menunggu'): ?>
                    <form method="POST">
                        <input type="hidden" name="id_sewa"
                               value="<?= (int) $data['id_sewa'] ?>">

                        <label class="form-label">Keputusan Petugas</label>
                        <select name="status" class="form-select mb-3" required>
                            <option value="disetujui">Setujui Penyewaan</option>
                            <option value="ditolak">Tolak Penyewaan</option>
                        </select>

                        <button type="submit" name="ubah_status"
                                class="btn btn-dark">
                            Simpan Keputusan
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-secondary">
                        Penyewaan ini sudah diproses.
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
