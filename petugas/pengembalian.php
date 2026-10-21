
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


if (isset($_POST['kembalikan'])) {
    $id_sewa = (int) $_POST['id_sewa'];

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

        if (!$sewa || $sewa['status'] != 'disetujui') {
            throw new Exception(
                'Penyewaan tidak ditemukan atau sudah dikembalikan.'
            );
        }

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE motor
             SET stok = stok + 1
             WHERE id_motor = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $sewa['id_motor']
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception('Stok motor gagal diperbarui.');
        }

        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE penyewaan
             SET status = 'selesai'
             WHERE id_sewa = ? AND status = 'disetujui'"
        );

        mysqli_stmt_bind_param($stmt, "i", $id_sewa);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) != 1) {
            mysqli_stmt_close($stmt);
            throw new Exception('Status pengembalian gagal diperbarui.');
        }

        mysqli_stmt_close($stmt);

        mysqli_commit($koneksi);

        header("Location: pengembalian.php?berhasil=1");
        exit;

    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
        $pesan = $e->getMessage();
    }
}

$sql = "SELECT penyewaan.*, user.nama, motor.nama_motor
        FROM penyewaan
        JOIN user ON penyewaan.id_user = user.id_user
        JOIN motor ON penyewaan.id_motor = motor.id_motor
        WHERE penyewaan.status = 'disetujui'
        ORDER BY penyewaan.id_sewa DESC";

$query = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengembalian Motor - Petugas</title>
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
            color: #cbd5e1;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #334155;
            color: white;
        }

        .content {
            padding: 30px;
        }

        .table th {
            white-space: nowrap;
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <h5 class="text-white mb-4">PETUGAS RENTAL</h5>

            <a href="dashboard.php">Dashboard</a>
            <a href="peminjaman.php">Data Peminjaman</a>
            <a href="pengembalian.php" class="active">
                Pengembalian Motor
            </a>
            <a href="laporan.php">Laporan</a>
            <a href="../logout.php"
               onclick="return confirm('Yakin ingin logout?')">
                Logout
            </a>
        </aside>

        <main class="col-md-9 col-lg-10 content">
            <h2 class="mb-1">Pengembalian Motor</h2>
            <p class="text-secondary mb-4">
                Kelola pengembalian motor dari penyewa.
            </p>

            <?php if (isset($_GET['berhasil'])): ?>
                <div class="alert alert-success">
                    Pengembalian berhasil diproses dan stok motor telah diperbarui.
                </div>
            <?php endif; ?>

            <?php if ($pesan != ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan) ?>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Penyewa</th>
                                    <th>Motor</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Total Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($data = mysqli_fetch_assoc($query)): ?>
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
                                                $data['total_harga'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>
                                        </td>

                                        <td>
                                            <form method="POST"
                                                  onsubmit="return confirm('Pastikan motor sudah diterima kembali. Selesaikan pengembalian ini?')">

                                                <input type="hidden"
                                                       name="id_sewa"
                                                       value="<?= (int) $data['id_sewa'] ?>">

                                                <button type="submit"
                                                        name="kembalikan"
                                                        class="btn btn-success btn-sm">
                                                    Selesaikan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7"
                                        class="text-center text-secondary py-4">
                                        Belum ada motor yang perlu dikembalikan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
