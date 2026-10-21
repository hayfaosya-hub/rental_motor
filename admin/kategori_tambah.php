
<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$pesan = '';

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama_kategori']);

    if ($nama == '') {
        $pesan = 'Nama kategori harus diisi.';
    } else {
        $query = mysqli_prepare(
            $koneksi,
            "INSERT INTO kategori (nama_kategori) VALUES (?)"
        );
        mysqli_stmt_bind_param($query, "s", $nama);

        if (mysqli_stmt_execute($query)) {
            header("Location: kategori.php");
            exit;
        } else {
            $pesan = 'Kategori gagal ditambahkan.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kategori</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body style="background:#f1f5f9;color:#1e293b">

<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width:600px">
        <div class="card-body p-4">
            <h3 class="mb-4">Tambah Kategori</h3>

            <?php if ($pesan != '') { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan); ?>
                </div>
            <?php } ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori"
                           class="form-control" required>
                </div>

                <button type="submit" name="simpan" class="btn btn-dark">
                    Simpan
                </button>
                <a href="kategori.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
