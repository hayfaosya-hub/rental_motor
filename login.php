
<?php
session_start();
include 'koneksi.php';
require_once 'log_helper.php';

$pesan = '';
$mode = isset($_GET['daftar']) ? 'daftar' : 'login';
$id_motor = isset($_GET['motor']) ? (int) $_GET['motor'] : 0;

if (isset($_POST['daftar'])) {
    $mode = 'daftar';

    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $id_motor = isset($_POST['id_motor'])
        ? (int) $_POST['id_motor']
        : 0;

    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id_user FROM user WHERE username = ?"
    );

    mysqli_stmt_bind_param($cek, "s", $username);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
        $pesan = "Username sudah digunakan!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO user (nama, username, password, role)
             VALUES (?, ?, ?, 'penyewa')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $nama,
            $username,
            $hash
        );

        if (mysqli_stmt_execute($stmt)) {
            $pesan = "Pendaftaran berhasil! Silakan login.";
            $mode = 'login';
        } else {
            $pesan = "Pendaftaran gagal!";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_stmt_close($cek);
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Pertahankan ID motor setelah login
    $id_motor = isset($_POST['id_motor'])
        ? (int) $_POST['id_motor']
        : 0;

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id_user, nama, username, password, role
         FROM user
         WHERE username = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        catatAktivitas(
            $koneksi,
            $_SESSION['id_user'],
            'Login ke sistem'
        );

        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
            exit;

        } elseif ($user['role'] == 'petugas') {
            header("Location: petugas/dashboard.php");
            exit;

    
        } elseif ($user['role'] == 'penyewa') {

            if ($id_motor > 0) {
                header("Location: penyewa/sewa.php?id=" . $id_motor);
            } else {
                header("Location: index.php");
            }

            exit;

        } else {
            $pesan = "Role akun tidak valid!";
            $mode = 'login';
        }

    } else {
        $pesan = "Username atau password salah!";
        $mode = 'login';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Login / Daftar - Rental Motor</title>

    <link rel="stylesheet"
          href="assets/css/bootstrap.min.css">

    <style>
        body {
            background: #f1f5f9;
            color: #1e293b;
        }

        .card {
            border: none;
            border-radius: 15px;
        }

        .btn-rental {
            background: #334155;
            color: white;
        }

        .btn-rental:hover {
            background: #1e293b;
            color: white;
        }
    </style>
</head>

<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h3 class="text-center fw-bold">
                        <?= $mode == 'daftar'
                            ? 'Daftar Akun'
                            : 'Login' ?>
                    </h3>

                    <p class="text-center text-secondary">
                        Rental Motor
                    </p>

                    <?php if ($pesan != '') { ?>
                        <div class="alert alert-info">
                            <?= htmlspecialchars($pesan) ?>
                        </div>
                    <?php } ?>

                    <?php if ($mode == 'daftar') { ?>

                        <form method="POST">
                            <input type="hidden"
                                   name="id_motor"
                                   value="<?= $id_motor ?>">

                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Lengkap
                                </label>
                                <input type="text"
                                       name="nama"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Username
                                </label>
                                <input type="text"
                                       name="username"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Password
                                </label>
                                <input type="password"
                                       name="password"
                                       class="form-control"
                                       minlength="8"
                                       required>
                            </div>

                            <button type="submit"
                                    name="daftar"
                                    class="btn btn-rental w-100">
                                Daftar
                            </button>
                        </form>

                        <p class="text-center mt-3">
                            Sudah punya akun?
                            <a href="login.php<?= $id_motor > 0
                                ? '?motor=' . $id_motor
                                : '' ?>">
                                Login
                            </a>
                        </p>

                    <?php } else { ?>

                        <form method="POST">
                            <input type="hidden"
                                   name="id_motor"
                                   value="<?= $id_motor ?>">

                            <div class="mb-3">
                                <label class="form-label">
                                    Username
                                </label>
                                <input type="text"
                                       name="username"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Password
                                </label>
                                <input type="password"
                                       name="password"
                                       class="form-control"
                                       required>
                            </div>

                            <button type="submit"
                                    name="login"
                                    class="btn btn-rental w-100">
                                Login
                            </button>
                        </form>

                        <p class="text-center mt-3">
                            Belum punya akun?
                            <a href="login.php?daftar=1<?= $id_motor > 0
                                ? '&motor=' . $id_motor
                                : '' ?>">
                                Daftar
                            </a>
                        </p>

                    <?php } ?>

                    <div class="text-center mt-2">
                        <a href="index.php"
                           class="text-secondary">
                            Kembali ke Dashboard
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
