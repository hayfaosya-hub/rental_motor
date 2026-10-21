
<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$id = (int) ($_GET['id'] ?? $_POST['id_user'] ?? 0);
$pesan = '';

if ($id <= 0) {
    header("Location: user.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($nama == '' || $username == '') {
        $pesan = "Nama dan username wajib diisi!";
    } elseif (!in_array($role, ['admin', 'petugas', 'penyewa'])) {
        $pesan = "Role tidak valid!";
    } elseif ($id == (int) $_SESSION['id_user'] && $role != 'admin') {
        $pesan = "Role akun sendiri tidak boleh diubah dari admin!";
    } else {
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id_user FROM user
             WHERE username = ? AND id_user != ?"
        );
        mysqli_stmt_bind_param($cek, "si", $username, $id);
        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil) > 0) {
            $pesan = "Username sudah digunakan!";
        } else {
            if ($password != '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare(
                    $koneksi,
                    "UPDATE user
                     SET nama=?, username=?, password=?, role=?
                     WHERE id_user=?"
                );
                mysqli_stmt_bind_param(
                    $stmt, "ssssi", $nama, $username, $hash, $role, $id
                );
            } else {
                $stmt = mysqli_prepare(
                    $koneksi,
                    "UPDATE user
                     SET nama=?, username=?, role=?
                     WHERE id_user=?"
                );
                mysqli_stmt_bind_param(
                    $stmt, "sssi", $nama, $username, $role, $id
                );
            }

            if (mysqli_stmt_execute($stmt)) {
                header("Location: user.php?status=edit");
                exit;
            } else {
                $pesan = "Gagal menyimpan perubahan!";
            }
        }
    }
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id_user, nama, username, role
     FROM user WHERE id_user = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($hasil);

if (!$user) {
    header("Location: user.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body style="background:#f1f5f9">
<div class="container py-5">
    <div class="card shadow-sm border-0 mx-auto" style="max-width:600px">
        <div class="card-body p-4">
            <h3 class="fw-bold mb-3">Edit User</h3>

            <?php if ($pesan != '') { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan) ?>
                </div>
            <?php } ?>

            <form method="POST" action="user_edit.php">
                <input type="hidden" name="id_user" value="<?= $id ?>">

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control"
                           value="<?= htmlspecialchars($user['nama']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                           value="<?= htmlspecialchars($user['username']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-secondary">
                        Kosongkan jika tidak ingin mengganti password.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin"
                            <?= $user['role'] == 'admin' ? 'selected' : '' ?>>
                            Admin
                        </option>
                        <option value="petugas"
                            <?= $user['role'] == 'petugas' ? 'selected' : '' ?>>
                            Petugas
                        </option>
                        <option value="penyewa"
                            <?= $user['role'] == 'penyewa' ? 'selected' : '' ?>>
                            Penyewa
                        </option>
                    </select>
                </div>

                <button type="submit" name="simpan"
                        class="btn text-white" style="background:#334155">
                    Simpan Perubahan
                </button>
                <a href="user.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
