
<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: motor.php");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_prepare($koneksi, "SELECT * FROM motor WHERE id_motor = ?");
mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);
$hasil = mysqli_stmt_get_result($query);
$motor = mysqli_fetch_assoc($hasil);

if (!$motor) {
    header("Location: motor.php");
    exit;
}

$pesan = '';

if (isset($_POST['simpan'])) {
    $id_kategori = (int) $_POST['id_kategori'];
    $nama_motor = trim($_POST['nama_motor']);
    $merk = trim($_POST['merk']);
    $harga_sewa = (float) $_POST['harga_sewa'];
    $stok = (int) $_POST['stok'];
    $status = $_POST['status'];

    $gambar = $motor['gambar'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        if ($_FILES['gambar']['error'] != 0) {
            $pesan = 'Gagal mengunggah gambar.';
        } else {
            $ekstensi = strtolower(pathinfo(
                $_FILES['gambar']['name'], PATHINFO_EXTENSION
            ));

            if (!in_array($ekstensi, ['jpg', 'jpeg', 'png', 'webp'])) {
                $pesan = 'Format gambar harus JPG, JPEG, PNG, atau WEBP.';
            } else {
                $gambar_baru = uniqid('motor_') . '.' . $ekstensi;

                if (move_uploaded_file(
                    $_FILES['gambar']['tmp_name'],
                    '../gambar/' . $gambar_baru
                )) {
                    $gambar = $gambar_baru;
                } else {
                    $pesan = 'Gagal menyimpan gambar.';
                }
            }
        }
    }

    if ($pesan == '') {
        $query = mysqli_prepare($koneksi, "
            UPDATE motor SET
            id_kategori = ?, nama_motor = ?, merk = ?,
            harga_sewa = ?, stok = ?, gambar = ?, status = ?
            WHERE id_motor = ?
        ");

        mysqli_stmt_bind_param(
            $query, "issdissi",
            $id_kategori, $nama_motor, $merk,
            $harga_sewa, $stok, $gambar, $status, $id
        );

        if (mysqli_stmt_execute($query)) {
            header("Location: motor.php");
            exit;
        } else {
            $pesan = 'Data motor gagal diubah.';
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
    <title>Edit Motor</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body style="background:#f1f5f9; color:#1e293b">

<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width:700px">
        <div class="card-body p-4">
            <h3 class="mb-4">Edit Motor</h3>

            <?php if ($pesan != '') { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan); ?>
                </div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="id_kategori" class="form-select" required>
                        <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
                            <option value="<?= $k['id_kategori']; ?>"
                                <?= $k['id_kategori'] == $motor['id_kategori'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($k['nama_kategori']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Motor</label>
                    <input type="text" name="nama_motor" class="form-control"
                        value="<?= htmlspecialchars($motor['nama_motor']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Merk</label>
                    <input type="text" name="merk" class="form-control"
                        value="<?= htmlspecialchars($motor['merk']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga Sewa per Hari</label>
                    <input type="number" name="harga_sewa" class="form-control"
                        min="0" value="<?= $motor['harga_sewa']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control"
                        min="0" value="<?= $motor['stok']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar Motor</label>
                    <?php if (!empty($motor['gambar'])) { ?>
                        <p>
                            Gambar sekarang:
                            <?= htmlspecialchars($motor['gambar']); ?>
                        </p>
                    <?php } ?>
                    <input type="file" name="gambar" class="form-control"
                        accept=".jpg,.jpeg,.png,.webp">
                    <small class="text-secondary">Kosongkan jika tidak ingin mengganti gambar.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="tersedia"
                            <?= $motor['status'] == 'tersedia' ? 'selected' : ''; ?>>
                            Tersedia
                        </option>
                        <option value="tidak tersedia"
                            <?= $motor['status'] == 'tidak tersedia' ? 'selected' : ''; ?>>
                            Tidak Tersedia
                        </option>
                    </select>
                </div>

                <button type="submit" name="simpan" class="btn btn-dark">Simpan Perubahan</button>
                <a href="motor.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
