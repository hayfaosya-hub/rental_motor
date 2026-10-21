
<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$dataUser = mysqli_query(
    $koneksi,
    "SELECT id_user, nama, username, role
     FROM user ORDER BY id_user DESC"
);

$pesan = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'tambah') $pesan = 'User berhasil ditambahkan!';
    if ($_GET['status'] == 'edit') $pesan = 'User berhasil diedit!';
    if ($_GET['status'] == 'hapus') $pesan = 'User berhasil dihapus!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data User</title>
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
            <a href="user.php" class="active">Data User</a>
            <a href="motor.php">Data Motor</a>
            <a href="kategori.php">Kategori</a>
            <a href="peminjaman.php" >Data Peminjaman</a>
            <a href="pengembalian.php" >Pengembalian</a>
            <a href="log_aktivitas.php">Log Aktivitas</a>
            <a href="../logout.php">Logout</a>
        </div>

    <div class="col-md-9 col-lg-10 p-4">
        <h3 class="fw-bold">Data User</h3>
        <p class="text-secondary">Kelola akun pengguna rental motor.</p>

        <?php if ($pesan != '') { ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php } ?>

        <div class="card shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Daftar User</h5>
                <a href="user_tambah.php" class="btn btn-dark">
                    + Tambah User
                </a>
            </div>

            <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; while ($u = mysqli_fetch_assoc($dataUser)) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($u['nama']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td>
                            <span class="badge text-bg-secondary">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="user_edit.php?id=<?= $u['id_user'] ?>"
                               class="btn btn-warning btn-sm">Edit</a>

                            <?php if ($u['id_user'] != $_SESSION['id_user']) { ?>
                            <form action="user_hapus.php" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                <input type="hidden" name="id_user"
                                       value="<?= $u['id_user'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </form>
                            <?php } ?>
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
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
