
<?php
session_start();

if (
    !isset($_SESSION['id_user']) ||
    $_SESSION['role'] != 'penyewa'
) {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$id_motor = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$pesan = '';

if ($id_motor <= 0) {
    header("Location: ../index.php");
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM motor
     WHERE id_motor = ? AND status = 'tersedia'"
);

mysqli_stmt_bind_param($stmt, "i", $id_motor);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$motor = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$motor) {
    die("Motor tidak ditemukan atau sedang tidak tersedia.");
}

if (isset($_POST['sewa'])) {
    $tanggal_sewa = $_POST['tanggal_sewa'] ?? '';
    $tanggal_kembali = $_POST['tanggal_kembali'] ?? '';

    $tglSewa = DateTime::createFromFormat('!Y-m-d', $tanggal_sewa);
    $tglKembali = DateTime::createFromFormat('!Y-m-d', $tanggal_kembali);
    $hariIni = new DateTime('today');

    if (
        !$tglSewa || !$tglKembali ||
        $tglSewa->format('Y-m-d') != $tanggal_sewa ||
        $tglKembali->format('Y-m-d') != $tanggal_kembali
    ) {
        $pesan = "Tanggal sewa atau tanggal kembali tidak valid.";

    } elseif (
        $tglSewa < $hariIni ||
        $tglKembali <= $tglSewa
    ) {
        $pesan = "Tanggal kembali harus setelah tanggal sewa, dan tanggal sewa tidak boleh di masa lalu.";

    } else {
        $lama_sewa = $tglSewa->diff($tglKembali)->days;
        $harga = (int) $motor['harga_sewa'];
        $total_harga = $lama_sewa * $harga;

        // Periksa stok terbaru
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT stok FROM motor
             WHERE id_motor = ? AND status = 'tersedia'"
        );

        mysqli_stmt_bind_param($stmt, "i", $id_motor);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $stok_motor = mysqli_fetch_assoc($hasil);
        mysqli_stmt_close($stmt);

        if (!$stok_motor || (int) $stok_motor['stok'] < 1) {
            $pesan = "Maaf, stok motor sedang habis.";

        } else {
            $id_user = (int) $_SESSION['id_user'];

            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO penyewaan
                (id_user, id_motor, tanggal_sewa,
                 tanggal_kembali, total_harga, status)
                VALUES (?, ?, ?, ?, ?, 'menunggu')"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "iissi",
                $id_user,
                $id_motor,
                $tanggal_sewa,
                $tanggal_kembali,
                $total_harga
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header("Location: riwayat.php?berhasil=1");
                exit;
            } else {
                $pesan = "Penyewaan gagal disimpan.";
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Sewa Motor - Rental Motor</title>

    <link rel="stylesheet"
          href="../assets/css/bootstrap.min.css">

    <style>
        body {
            background: #f1f5f9;
            color: #1e293b;
        }

        .navbar {
            background: #1e293b;
        }

        .sewa-card {
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

        .motor-img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../index.php">
            RENTAL MOTOR
        </a>

        <a href="../logout.php"
           class="btn btn-outline-light btn-sm">
            Logout
        </a>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card sewa-card shadow-sm">
                <div class="card-body p-4 p-md-5">

                    <h2 class="fw-bold mb-1">
                        Form Penyewaan Motor
                    </h2>

                    <p class="text-secondary mb-4">
                        Silakan isi tanggal penyewaan.
                    </p>

                    <?php if ($pesan != '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan) ?>
                        </div>
                    <?php } ?>

                    <div class="row g-4">

                        <div class="col-md-5">
                            <?php if (!empty($motor['gambar'])) { ?>
                                <img
                                    src="../assets/img/<?= htmlspecialchars($motor['gambar']) ?>"
                                    class="motor-img mb-3"
                                    alt="Foto motor">
                            <?php } ?>

                            <h4 class="fw-bold">
                                <?= htmlspecialchars($motor['nama_motor']) ?>
                            </h4>

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

                            <p class="mb-0">
                                Stok tersedia:
                                <?= (int) $motor['stok'] ?> unit
                            </p>
                        </div>

                        <div class="col-md-7">
                            <form method="POST">

                                <div class="mb-3">
                                    <label class="form-label">
                                        Nama Penyewa
                                    </label>

                                    <input type="text"
                                           class="form-control"
                                           value="<?= htmlspecialchars($_SESSION['nama']) ?>"
                                           readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Tanggal Sewa
                                    </label>

                                    <input type="date"
                                           name="tanggal_sewa"
                                           id="tanggal_sewa"
                                           class="form-control"
                                           min="<?= date('Y-m-d') ?>"
                                           value="<?= htmlspecialchars($_POST['tanggal_sewa'] ?? '') ?>"
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Tanggal Kembali
                                    </label>

                                    <input type="date"
                                           name="tanggal_kembali"
                                           id="tanggal_kembali"
                                           class="form-control"
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                           value="<?= htmlspecialchars($_POST['tanggal_kembali'] ?? '') ?>"
                                           required>
                                </div>

                                <div class="alert alert-secondary">
                                    <div class="d-flex justify-content-between">
                                        <span>Lama sewa</span>
                                        <strong id="lama_sewa">-</strong>
                                    </div>

                                    <div class="d-flex justify-content-between mt-2">
                                        <span>Total harga</span>
                                        <strong id="total_harga">-</strong>
                                    </div>
                                </div>

                                <button type="submit"
                                        name="sewa"
                                        class="btn btn-rental w-100">
                                    Ajukan Penyewaan
                                </button>

                                <a href="../index.php"
                                   class="btn btn-outline-secondary w-100 mt-2">
                                    Kembali
                                </a>

                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const tanggalSewa = document.getElementById('tanggal_sewa');
const tanggalKembali = document.getElementById('tanggal_kembali');
const lamaSewa = document.getElementById('lama_sewa');
const totalHarga = document.getElementById('total_harga');

const hargaPerHari = <?= (int) $motor['harga_sewa'] ?>;

function hitungHarga() {
    if (!tanggalSewa.value || !tanggalKembali.value) {
        lamaSewa.textContent = '-';
        totalHarga.textContent = '-';
        return;
    }

    const mulai = new Date(tanggalSewa.value + 'T00:00:00');
    const kembali = new Date(tanggalKembali.value + 'T00:00:00');
    const selisih = Math.round(
        (kembali - mulai) / (1000 * 60 * 60 * 24)
    );

    if (selisih <= 0) {
        lamaSewa.textContent = '-';
        totalHarga.textContent = '-';
        return;
    }

    const total = selisih * hargaPerHari;

    lamaSewa.textContent = selisih + ' hari';
    totalHarga.textContent = 'Rp' +
        total.toLocaleString('id-ID');
}

tanggalSewa.addEventListener('change', function () {
    if (tanggalSewa.value) {
        const tanggalMinimal = new Date(
            tanggalSewa.value + 'T00:00:00'
        );

        tanggalMinimal.setDate(tanggalMinimal.getDate() + 1);

        const tahun = tanggalMinimal.getFullYear();
        const bulan = String(
            tanggalMinimal.getMonth() + 1
        ).padStart(2, '0');
        const hari = String(
            tanggalMinimal.getDate()
        ).padStart(2, '0');

        tanggalKembali.min = `${tahun}-${bulan}-${hari}`;

        if (
            tanggalKembali.value &&
            tanggalKembali.value <= tanggalSewa.value
        ) {
            tanggalKembali.value = '';
        }
    }

    hitungHarga();
});

tanggalKembali.addEventListener('change', hitungHarga);

hitungHarga();
</script>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
