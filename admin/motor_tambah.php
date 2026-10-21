
<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$pesan = '';

if (isset($_POST['simpan'])) {
    $id_kategori = $_POST['id_kategori'];
    $nama_motor = $_POST['nama_motor'];
    $merk = $_POST['merk'];
    $harga_sewa = $_POST['harga_sewa'];
    $stok = $_POST['stok'];
    $status = $_POST['status'];

    $gambar = '';

    // Upload gambar jika dipilih
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file = $_FILES['gambar']['tmp_name'];
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ekstensi, $ekstensi_valid)) {
            $pesan = 'Format gambar harus JPG, JPEG, PNG, atau WEBP.';
        } else {
            $gambar = uniqid('motor_') . '.' . $ekstensi;

            if (!move_uploaded_file($tmp_file, '../assets/img/' . $gambar)) {
                $pesan = 'Gagal mengunggah gambar.';
            }
        }
    }

    if ($pesan == '') {
        $query = mysqli_prepare($koneksi, "
            INSERT INTO motor
            (id_kategori, nama_motor, merk, harga_sewa, stok, gambar, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $query,
            "issdiss",
            $id_kategori,
            $nama_motor,
            $merk,
            $harga_sewa,
            $stok,
            $gambar,
            $status
        );

        if (mysqli_stmt_execute($query)) {
            header("Location: motor.php");
            exit;
        } else {
            $pesan = 'Data motor gagal disimpan.';
        }
    }
}

$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Motor</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

    <style>
        body {
            background: #f1f5f9;
            color: #1e293b;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .btn-dark {
            background: #334155;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h3 class="mb-4">Tambah Motor</h3>

                    <?php if ($pesan != '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="id_kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>

                                <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
                                    <option value="<?= $k['id_kategori']; ?>">
                                        <?= htmlspecialchars($k['nama_kategori']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Motor</label>
                            <input type="text" name="nama_motor"
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Merk</label>
                            <input type="text" name="merk"
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga Sewa per Hari (Rp)</label>
                            <input type="number" name="harga_sewa"
                                   class="form-control" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok"
                                   class="form-control" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar Motor</label>
                            <input type="file" name="gambar"
                                   class="form-control"
                                   accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-secondary">
                                Gambar boleh dikosongkan.
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="tidak tersedia">Tidak Tersedia</option>
                            </select>
                        </div>

                        <button type="submit" name="simpan"
                                class="btn btn-dark">Simpan</button>

                        <a href="motor.php"
                           class="btn btn-secondary">Kembali</a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
