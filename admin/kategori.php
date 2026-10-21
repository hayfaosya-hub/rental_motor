
<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY id_kategori ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Kategori - Rental Motor</title>
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
            <a href="kategori.php" class="active">Kategori</a>
            <a href="peminjaman.php">Data Peminjaman</a>
            <a href="pengembalian.php">Pengembalian</a>
            <a href="log_aktivitas.php">Log Aktivitas</a>
            <a href="../logout.php">Logout</a>
        </div>

        <div class="col-md-10 content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3>Data Kategori</h3>
                    <p class="text-secondary">Kelola kategori motor.</p>
                </div>
                <a href="kategori_tambah.php" class="btn btn-dark">
                    + Tambah Kategori
                </a>
            </div>

            <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'terpakai') { ?>
                <div class="alert alert-warning">
                    Kategori tidak bisa dihapus karena masih digunakan oleh data motor.
                </div>
            <?php } elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal') { ?>
                <div class="alert alert-danger">
                    Kategori gagal dihapus.
                </div>
            <?php } elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil') { ?>
                <div class="alert alert-success">
                    Kategori berhasil dihapus.
                </div>
            <?php } ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (mysqli_num_rows($query) > 0) {
                                while ($kategori = mysqli_fetch_assoc($query)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($kategori['nama_kategori']); ?></td>
                                <td>
                                    <a href="kategori_edit.php?id=<?= $kategori['id_kategori']; ?>"
                                       class="btn btn-warning btn-sm">Edit</a>

                                    <form action="kategori_hapus.php" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        <input type="hidden" name="id_kategori"
                                               value="<?= $kategori['id_kategori']; ?>">
                                        <button type="submit"
                                                class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="3" class="text-center">Belum ada kategori.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
