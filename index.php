
<?php
session_start();
include 'koneksi.php';

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM motor WHERE status='tersedia'"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Rental Motor</title>

    <link rel="stylesheet"
          href="assets/css/bootstrap.min.css">

    <style>
        body {
            background: #f1f5f9;
            color: #1e293b;
        }

        .navbar {
            background: #1e293b;
        }

        .hero {
            background: #334155;
            color: white;
            padding: 65px 0;
            border-radius: 15px;
        }

        .motor-card {
            border: none;
            border-radius: 12px;
            transition: 0.2s;
        }

        .motor-card:hover {
            transform: translateY(-4px);
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

<nav class="navbar navbar-dark">
    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            RENTAL MOTOR
        </a>

        <div class="d-flex align-items-center gap-2">

            <?php if (
                isset($_SESSION['id_user']) &&
                $_SESSION['role'] == 'penyewa'
            ) { ?>

                <span class="text-white d-none d-md-inline">
                    Halo, <?= htmlspecialchars($_SESSION['nama']) ?>
                </span>

                <a href="penyewa/riwayat.php"
                   class="btn btn-outline-light btn-sm">
                    Riwayat Saya
                </a>

                <a href="logout.php"
                   class="btn btn-light btn-sm">
                    Logout
                </a>

            <?php } else { ?>

                <a href="login.php"
                   class="btn btn-light btn-sm">
                    Daftar
                </a>

            <?php } ?>

        </div>
    </div>
</nav>

<div class="container my-4">

    <div class="hero p-4 p-md-5">
        <h1 class="fw-bold">
            Rental Motor Mudah dan Praktis
        </h1>

        <p class="mt-3">
            Pilih motor favoritmu dan nikmati perjalanan
            dengan nyaman.
        </p>

        <a href="#daftar-motor"
           class="btn btn-light mt-2">
            Lihat Motor
        </a>
    </div>

    <div class="mt-5" id="daftar-motor">

        <h2 class="fw-bold">Daftar Motor</h2>

        <p class="text-secondary">
            Pilih motor sesuai kebutuhanmu.
        </p>

        <div class="row g-4">

            <?php while ($motor = mysqli_fetch_assoc($query)) { ?>

                <div class="col-md-4">

                    <div class="card motor-card shadow-sm h-100">

                        <?php if ($motor['gambar'] != '') { ?>
                            <img
                                src="assets/img/<?= htmlspecialchars($motor['gambar']) ?>"
                                class="card-img-top"
                                style="height:200px; object-fit:cover;"
                                alt="Motor">
                        <?php } ?>

                        <div class="card-body">

                            <h5 class="fw-bold">
                                <?= htmlspecialchars($motor['nama_motor']) ?>
                            </h5>

                            <p class="text-secondary mb-2">
                                <?= htmlspecialchars($motor['merk']) ?>
                            </p>

                            <h5 class="fw-bold">
                                Rp<?= number_format(
                                    $motor['harga_sewa'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                                <small class="text-secondary fs-6">
                                    /hari
                                </small>
                            </h5>

                            <p>
                                Stok: <?= (int) $motor['stok'] ?>
                            </p>

                            <?php if (
                                isset($_SESSION['id_user']) &&
                                $_SESSION['role'] == 'penyewa'
                            ) { ?>

                                <a
                                    href="penyewa/sewa.php?id=<?= (int) $motor['id_motor'] ?>"
                                    class="btn btn-rental w-100">
                                    Sewa Sekarang
                                </a>

                            <?php } else { ?>

                                <a
                                    href="login.php?motor=<?= (int) $motor['id_motor'] ?>"
                                    class="btn btn-rental w-100">
                                    Sewa Sekarang
                                </a>

                            <?php } ?>

                        </div>
                    </div>
                </div>

            <?php } ?>

        </div>
    </div>
</div>

<footer class="text-center text-secondary py-4">
    &copy; 2026 Rental Motor
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
