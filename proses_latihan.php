
<?php
require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Hanya menerima pengiriman melalui POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: materi.php");
    exit;
}

// Validasi CSRF
$csrfToken = $_POST["csrf_token"] ?? "";
$sessionToken = $_SESSION["csrf_latihan"] ?? "";

if (
    empty($csrfToken) ||
    empty($sessionToken) ||
    !hash_equals($sessionToken, $csrfToken)
) {
    die("Sesi latihan tidak valid. Silakan kembali dan mulai latihan lagi.");
}

// Token hanya boleh digunakan sekali
unset($_SESSION["csrf_latihan"]);

// Ambil data dari form
$materiId = isset($_POST["materi_id"]) ? (int) $_POST["materi_id"] : 0;
$bagian = $_POST["bagian"] ?? "";
$jawabanSiswa = $_POST["jawaban"] ?? [];

$siswaId = (int) $_SESSION["siswa_id"];

// Validasi materi dan bagian
if ($materiId <= 0 || !in_array($bagian, ["A", "B", "C", "ESAI"], true)) {
    die("Data latihan tidak valid.");
}

if (!is_array($jawabanSiswa)) {
    die("Format jawaban tidak valid.");
}

// Pastikan materi merupakan materi Vektor yang dipublikasikan
$stmtMateri = mysqli_prepare(
    $conn,
    "SELECT id, judul FROM materi
     WHERE id = ? AND judul = 'Vektor'
     AND status_publikasi = 'publik'
     LIMIT 1"
);

mysqli_stmt_bind_param($stmtMateri, "i", $materiId);
mysqli_stmt_execute($stmtMateri);
$resultMateri = mysqli_stmt_get_result($stmtMateri);
$materi = mysqli_fetch_assoc($resultMateri);
mysqli_stmt_close($stmtMateri);

if (!$materi) {
    die("Materi tidak ditemukan atau belum dipublikasikan.");
}

// Tentukan jenis soal
$jenisSoal = ($bagian === "ESAI") ? "esai" : "pilihan_ganda";

// Ambil soal yang sesuai dengan materi dan bagian
$stmtSoal = mysqli_prepare(
    $conn,
    "SELECT id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d,
            jawaban_benar, pembahasan, kunci_esai
     FROM soal
     WHERE materi_id = ? AND bagian = ? AND jenis_soal = ?
     ORDER BY id ASC"
);

mysqli_stmt_bind_param($stmtSoal, "iss", $materiId, $bagian, $jenisSoal);
mysqli_stmt_execute($stmtSoal);

$resultSoal = mysqli_stmt_get_result($stmtSoal);

$daftarSoal = [];

while ($row = mysqli_fetch_assoc($resultSoal)) {
    $daftarSoal[] = $row;
}

mysqli_stmt_close($stmtSoal);

if (empty($daftarSoal)) {
    die("Soal untuk bagian ini belum tersedia.");
}

// Pastikan jumlah jawaban sesuai dengan soal yang tersedia
if (count($jawabanSiswa) !== count($daftarSoal)) {
    die("Jumlah jawaban tidak sesuai. Silakan ulangi latihan.");
}

// Pastikan semua soal dijawab
foreach ($daftarSoal as $soal) {
    $soalId = (string) $soal["id"];

    if (!array_key_exists($soalId, $jawabanSiswa)) {
        die("Masih ada soal yang belum dijawab.");
    }

    if (!is_string($jawabanSiswa[$soalId])) {
        die("Format jawaban tidak valid.");
    }

    if (trim($jawabanSiswa[$soalId]) === "") {
        die("Masih ada soal yang belum dijawab.");
    }
}

// Proses penilaian
$jumlahBenar = 0;
$jumlahSalah = 0;
$totalSoal = count($daftarSoal);
$nilai = 0;

if ($jenisSoal === "pilihan_ganda") {

    $pilihanValid = ["a", "b", "c", "d"];

    foreach ($daftarSoal as $soal) {
        $soalId = (string) $soal["id"];
        $jawaban = strtolower(trim($jawabanSiswa[$soalId]));

        if (!in_array($jawaban, $pilihanValid, true)) {
            die("Ditemukan pilihan jawaban yang tidak valid.");
        }

        if (
            empty($soal["jawaban_benar"]) ||
            !in_array($soal["jawaban_benar"], $pilihanValid, true)
        ) {
            die("Kunci jawaban belum lengkap. Silakan hubungi pengelola.");
        }

        if ($jawaban === $soal["jawaban_benar"]) {
            $jumlahBenar++;
        } else {
            $jumlahSalah++;
        }
    }

    $nilai = ($totalSoal > 0)
        ? ($jumlahBenar / $totalSoal) * 100
        : 0;

} else {

    // Esai tidak dinilai otomatis
    foreach ($daftarSoal as $soal) {
        $soalId = (string) $soal["id"];
        $jawaban = trim($jawabanSiswa[$soalId]);

        if (strlen($jawaban) > 10000) {
            die("Jawaban esai terlalu panjang.");
        }
    }

    $jumlahBenar = 0;
    $jumlahSalah = 0;
    $nilai = 0;
}

// Simpan hasil dan jawaban ke database
mysqli_begin_transaction($conn);

try {

    // Simpan hasil latihan
    $stmtHasil = mysqli_prepare(
        $conn,
        "INSERT INTO hasil_latihan
         (siswa_id, materi_id, bagian, jumlah_benar, jumlah_salah, nilai)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmtHasil,
        "iisiid",
        $siswaId,
        $materiId,
        $bagian,
        $jumlahBenar,
        $jumlahSalah,
        $nilai
    );

    if (!mysqli_stmt_execute($stmtHasil)) {
        throw new Exception("Gagal menyimpan hasil latihan.");
    }

    $hasilId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmtHasil);

    // Simpan jawaban masing-masing soal
    $stmtJawaban = mysqli_prepare(
        $conn,
        "INSERT INTO jawaban_siswa
         (hasil_id, soal_id, jawaban, status_benar)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($daftarSoal as $soal) {

        $soalId = (int) $soal["id"];
        $jawaban = trim($jawabanSiswa[(string) $soalId]);

        if ($jenisSoal === "pilihan_ganda") {
            $jawaban = strtolower($jawaban);
            $statusBenar = ($jawaban === $soal["jawaban_benar"]) ? 1 : 0;
        } else {
            $statusBenar = null;
        }

        mysqli_stmt_bind_param(
            $stmtJawaban,
            "iisi",
            $hasilId,
            $soalId,
            $jawaban,
            $statusBenar
        );

        if (!mysqli_stmt_execute($stmtJawaban)) {
            throw new Exception("Gagal menyimpan jawaban siswa.");
        }
    }

    mysqli_stmt_close($stmtJawaban);

    mysqli_commit($conn);

} catch (Exception $error) {

    mysqli_rollback($conn);

    die("Terjadi kesalahan saat menyimpan latihan. Silakan coba kembali.");
}

// Simpan hasil sementara untuk ditampilkan
$_SESSION["hasil_latihan_terakhir"] = $hasilId;

// Ambil ulang data jawaban untuk ditampilkan
$stmtDetail = mysqli_prepare(
    $conn,
    "SELECT js.jawaban, js.status_benar,
            s.id, s.pertanyaan,
            s.pilihan_a, s.pilihan_b, s.pilihan_c, s.pilihan_d,
            s.jawaban_benar, s.pembahasan, s.kunci_esai
     FROM jawaban_siswa js
     JOIN soal s ON js.soal_id = s.id
     WHERE js.hasil_id = ?
     ORDER BY s.id ASC"
);

mysqli_stmt_bind_param($stmtDetail, "i", $hasilId);
mysqli_stmt_execute($stmtDetail);

$resultDetail = mysqli_stmt_get_result($stmtDetail);

$detailJawaban = [];

while ($row = mysqli_fetch_assoc($resultDetail)) {
    $detailJawaban[] = $row;
}

mysqli_stmt_close($stmtDetail);

// Siapkan tampilan
$pageTitle = "Hasil Latihan";
require_once "includes/head.php";
?>

<body class="bg-warmwhite min-h-screen">

<?php require_once "includes/navbar.php"; ?>

<main class="max-w-5xl mx-auto px-4 py-10">

    <!-- Judul hasil -->
    <div class="text-center mb-8">
        <span class="inline-block bg-blue-100 text-blue-700 text-sm font-semibold px-4 py-2 rounded-full mb-3">
            Hasil Latihan
        </span>

        <h1 class="text-3xl font-bold text-darktext mb-2">
            Latihan Bagian <?= e($bagian) ?>
        </h1>

        <p class="text-gray-600">
            Materi <?= e($materi["judul"]) ?>
        </p>
    </div>

    <?php if ($jenisSoal === "pilihan_ganda"): ?>

        <!-- Ringkasan nilai pilihan ganda -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">

            <div class="text-center mb-6">
                <p class="text-gray-500 font-medium mb-2">Nilai Kamu</p>

                <div class="text-5xl font-bold text-blue-700">
                    <?= number_format($nilai, 0) ?>
                </div>

                <p class="text-sm text-gray-500 mt-2">
                    dari 100
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">

                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-green-700 mb-1">Jawaban Benar</p>
                    <p class="text-3xl font-bold text-green-700">
                        <?= $jumlahBenar ?>
                    </p>
                </div>

                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-red-700 mb-1">Jawaban Salah</p>
                    <p class="text-3xl font-bold text-red-700">
                        <?= $jumlahSalah ?>
                    </p>
                </div>

            </div>

            <p class="text-center text-sm text-gray-500 mt-5">
                Total soal dikerjakan: <?= $totalSoal ?> soal
            </p>

        </div>

        <!-- Pembahasan setiap soal -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-darktext mb-5">
                Pembahasan Jawaban
            </h2>

            <div class="space-y-5">

                <?php foreach ($detailJawaban as $index => $detail): ?>

                    <?php
                    $pilihan = [
                        "a" => $detail["pilihan_a"],
                        "b" => $detail["pilihan_b"],
                        "c" => $detail["pilihan_c"],
                        "d" => $detail["pilihan_d"]
                    ];

                    $jawabanUser = $detail["jawaban"];
                    $jawabanBenar = $detail["jawaban_benar"];
                    $statusBenar = (int) $detail["status_benar"] === 1;
                    ?>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">

                        <div class="flex items-start justify-between gap-3 mb-4">

                            <h3 class="font-semibold text-darktext">
                                Soal <?= $index + 1 ?>
                            </h3>

                            <?php if ($statusBenar): ?>
                                <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    Benar
                                </span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    Salah
                                </span>
                            <?php endif; ?>

                        </div>

                        <p class="text-gray-800 mb-4">
                            <?= nl2br(e($detail["pertanyaan"])) ?>
                        </p>

                        <div class="space-y-2 mb-4">

                            <?php foreach ($pilihan as $huruf => $isi): ?>

                                <?php
                                $isBenar = ($huruf === $jawabanBenar);
                                $isPilihanSiswa = ($huruf === $jawabanUser);

                                $kelasPilihan = "border-gray-200 bg-white";

                                if ($isBenar) {
                                    $kelasPilihan = "border-green-300 bg-green-50";
                                } elseif ($isPilihanSiswa) {
                                    $kelasPilihan = "border-red-300 bg-red-50";
                                }
                                ?>

                                <div class="border rounded-lg p-3 <?= $kelasPilihan ?>">

                                    <span class="font-semibold uppercase">
                                        <?= e($huruf) ?>.
                                    </span>

                                    <?= e($isi) ?>

                                    <?php if ($isBenar): ?>
                                        <span class="text-green-700 text-xs font-semibold ml-2">
                                            (Kunci jawaban)
                                        </span>
                                    <?php elseif ($isPilihanSiswa): ?>
                                        <span class="text-red-700 text-xs font-semibold ml-2">
                                            (Jawaban kamu)
                                        </span>
                                    <?php endif; ?>

                                </div>

                            <?php endforeach; ?>

                        </div>

                        <?php if (!empty($detail["pembahasan"])): ?>
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <p class="font-semibold text-blue-800 mb-1">
                                    Pembahasan
                                </p>

                                <p class="text-gray-700 text-sm leading-relaxed">
                                    <?= nl2br(e($detail["pembahasan"])) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>
        </div>

    <?php else: ?>

        <!-- Ringkasan esai -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">

            <div class="text-center">
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl text-blue-700">✓</span>
                </div>

                <h2 class="text-xl font-bold text-darktext mb-2">
                    Jawaban Berhasil Disimpan!
                </h2>

                <p class="text-gray-600 text-sm leading-relaxed">
                    Latihan esai tidak dinilai secara otomatis.
                    Silakan bandingkan jawabanmu dengan pedoman jawaban
                    untuk melakukan evaluasi mandiri.
                </p>

                <p class="text-blue-700 font-semibold mt-4">
                    Total <?= $totalSoal ?> soal esai telah dikerjakan.
                </p>
            </div>

        </div>

        <!-- Jawaban dan pedoman esai -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-darktext mb-5">
                Evaluasi Jawaban Esai
            </h2>

            <div class="space-y-5">

                <?php foreach ($detailJawaban as $index => $detail): ?>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">

                        <h3 class="font-semibold text-darktext mb-3">
                            Soal <?= $index + 1 ?>
                        </h3>

                        <p class="text-gray-800 mb-4">
                            <?= nl2br(e($detail["pertanyaan"])) ?>
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 mb-4">
                            <p class="font-semibold text-gray-700 text-sm mb-2">
                                Jawaban Kamu
                            </p>

                            <p class="text-gray-600 text-sm leading-relaxed">
                                <?= nl2br(e($detail["jawaban"])) ?>
                            </p>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                            <p class="font-semibold text-blue-800 text-sm mb-2">
                                Pedoman Jawaban
                            </p>

                            <p class="text-gray-700 text-sm leading-relaxed">
                                <?= !empty($detail["kunci_esai"])
                                    ? nl2br(e($detail["kunci_esai"]))
                                    : "Pedoman jawaban belum tersedia." ?>
                            </p>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>
        </div>

    <?php endif; ?>

    <!-- Tombol navigasi -->
    <div class="flex flex-col sm:flex-row justify-center gap-3 mt-8">

        <a href="latihan.php?materi_id=<?= $materiId ?>&bagian=<?= e($bagian) ?>"
           class="bg-blue-700 hover:bg-blue-800 text-white font-semibold text-center px-6 py-3 rounded-xl transition">
            Ulangi Latihan
        </a>

        <a href="materi-detail.php?id=<?= $materiId ?>"
           class="bg-white hover:bg-gray-100 text-darktext border border-gray-200 font-semibold text-center px-6 py-3 rounded-xl transition">
            Kembali ke Materi
        </a>

    </div>

</main>

<?php require_once "includes/footer.php"; ?>

</body>
</html>