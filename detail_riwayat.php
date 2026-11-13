<?php
$pageTitle = "Detail Hasil Latihan";

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$siswaId = (int) $_SESSION["siswa_id"];
$hasilId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($hasilId <= 0) {
    header("Location: riwayat_latihan.php");
    exit;
}

// Ambil data hasil latihan milik siswa yang sedang login
$queryHasil = "
    SELECT
        h.id,
        h.materi_id,
        h.bagian,
        h.jumlah_benar,
        h.jumlah_salah,
        h.nilai,
        h.created_at,
        m.judul
    FROM hasil_latihan h
    INNER JOIN materi m ON h.materi_id = m.id
    WHERE h.id = ?
      AND h.siswa_id = ?
      AND m.judul = 'Vektor'
    LIMIT 1
";

$stmtHasil = mysqli_prepare($conn, $queryHasil);
mysqli_stmt_bind_param($stmtHasil, "ii", $hasilId, $siswaId);
mysqli_stmt_execute($stmtHasil);

$resultHasil = mysqli_stmt_get_result($stmtHasil);
$hasil = mysqli_fetch_assoc($resultHasil);

mysqli_stmt_close($stmtHasil);

if (!$hasil) {
    http_response_code(404);
    die("Riwayat latihan tidak ditemukan atau kamu tidak memiliki akses.");
}

// Tentukan jenis latihan
$isEsai = ($hasil["bagian"] === "ESAI");

// Ambil detail jawaban
$queryDetail = "
    SELECT
        js.jawaban,
        js.status_benar,
        s.id AS soal_id,
        s.pertanyaan,
        s.pilihan_a,
        s.pilihan_b,
        s.pilihan_c,
        s.pilihan_d,
        s.jawaban_benar,
        s.pembahasan,
        s.kunci_esai
    FROM jawaban_siswa js
    INNER JOIN soal s ON js.soal_id = s.id
    WHERE js.hasil_id = ?
    ORDER BY s.id ASC
";

$stmtDetail = mysqli_prepare($conn, $queryDetail);
mysqli_stmt_bind_param($stmtDetail, "i", $hasilId);
mysqli_stmt_execute($stmtDetail);

$resultDetail = mysqli_stmt_get_result($stmtDetail);

$daftarDetail = [];

while ($row = mysqli_fetch_assoc($resultDetail)) {
    $daftarDetail[] = $row;
}

mysqli_stmt_close($stmtDetail);

$totalSoal = count($daftarDetail);

require_once "includes/head.php";
?>

<body class="bg-warmwhite min-h-screen">

<?php require_once "includes/navbar.php"; ?>

<main class="max-w-5xl mx-auto px-4 py-8">

    <!-- Navigasi kembali -->
    <a href="riwayat_latihan.php"
       class="inline-flex items-center gap-2 text-sm text-blue-700 hover:text-blue-900 font-semibold mb-6">
        ← Kembali ke Riwayat Latihan
    </a>

    <!-- Header -->
    <div class="mb-7">
        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full mb-3">
            Detail Pengerjaan
        </span>

        <h1 class="text-3xl font-bold text-darktext mb-2">
            <?= $isEsai ? "Hasil Latihan Esai" : "Hasil Latihan Bagian " . e($hasil["bagian"]) ?>
        </h1>

        <p class="text-gray-500 text-sm">
            Materi <?= e($hasil["judul"]) ?> ·
            <?= date("d M Y, H:i", strtotime($hasil["created_at"])) ?>
        </p>
    </div>

    <!-- Ringkasan hasil -->
    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 mb-8">

        <?php if (!$isEsai): ?>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-blue-700 mb-1">Nilai</p>
                    <p class="text-3xl font-bold text-blue-800">
                        <?= number_format((float) $hasil["nilai"], 0) ?>
                    </p>
                </div>

                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-green-700 mb-1">Benar</p>
                    <p class="text-3xl font-bold text-green-700">
                        <?= (int) $hasil["jumlah_benar"] ?>
                    </p>
                </div>

                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-red-700 mb-1">Salah</p>
                    <p class="text-3xl font-bold text-red-700">
                        <?= (int) $hasil["jumlah_salah"] ?>
                    </p>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-600 mb-1">Total Soal</p>
                    <p class="text-3xl font-bold text-darktext">
                        <?= $totalSoal ?>
                    </p>
                </div>

            </div>

        <?php else: ?>

            <div class="text-center py-3">
                <div class="w-14 h-14 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl">✓</span>
                </div>

                <h2 class="font-bold text-xl text-darktext mb-2">
                    Jawaban Esai Tersimpan
                </h2>

                <p class="text-sm text-gray-600">
                    Kamu telah mengerjakan <?= $totalSoal ?> soal esai.
                    Penilaian dilakukan secara mandiri menggunakan pedoman jawaban.
                </p>
            </div>

        <?php endif; ?>

    </div>

    <!-- Daftar jawaban -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-darktext mb-5">
            <?= $isEsai ? "Jawaban dan Pedoman Evaluasi" : "Detail Jawaban dan Pembahasan" ?>
        </h2>

        <?php if (empty($daftarDetail)): ?>

            <div class="bg-white border border-gray-100 rounded-xl p-8 text-center">
                <p class="text-gray-500">
                    Detail jawaban belum tersedia.
                </p>
            </div>

        <?php else: ?>

            <div class="space-y-5">

                <?php foreach ($daftarDetail as $index => $detail): ?>

                    <?php
                    $pilihan = [
                        "a" => $detail["pilihan_a"],
                        "b" => $detail["pilihan_b"],
                        "c" => $detail["pilihan_c"],
                        "d" => $detail["pilihan_d"]
                    ];

                    $jawabanUser = strtolower(trim($detail["jawaban"] ?? ""));
                    $jawabanBenar = $detail["jawaban_benar"];
                    $statusBenar = (int) $detail["status_benar"] === 1;
                    ?>

                    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">

                        <!-- Nomor soal dan status -->
                        <div class="flex items-center justify-between gap-3 mb-4">

                            <h3 class="font-bold text-darktext">
                                Soal <?= $index + 1 ?>
                            </h3>

                            <?php if (!$isEsai): ?>

                                <?php if ($statusBenar): ?>
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                                        Benar
                                    </span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                                        Salah
                                    </span>
                                <?php endif; ?>

                            <?php else: ?>

                                <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                                    Esai
                                </span>

                            <?php endif; ?>

                        </div>

                        <!-- Pertanyaan -->
                        <div class="text-gray-800 leading-relaxed mb-5">
                            <?= nl2br(e($detail["pertanyaan"])) ?>
                        </div>

                        <?php if (!$isEsai): ?>

                            <!-- Pilihan jawaban -->
                            <div class="space-y-2 mb-5">

                                <?php foreach ($pilihan as $huruf => $isi): ?>

                                    <?php
                                    $isBenar = ($huruf === $jawabanBenar);
                                    $isPilihanSiswa = ($huruf === $jawabanUser);

                                    $kelas = "border-gray-200 bg-white";

                                    if ($isBenar) {
                                        $kelas = "border-green-300 bg-green-50";
                                    } elseif ($isPilihanSiswa) {
                                        $kelas = "border-red-300 bg-red-50";
                                    }
                                    ?>

                                    <div class="border rounded-xl p-3 <?= $kelas ?>">

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

                            <!-- Pembahasan -->
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">

                                <p class="font-semibold text-blue-800 text-sm mb-2">
                                    Pembahasan
                                </p>

                                <p class="text-gray-700 text-sm leading-relaxed">
                                    <?= !empty($detail["pembahasan"])
                                        ? nl2br(e($detail["pembahasan"]))
                                        : "Pembahasan belum tersedia." ?>
                                </p>

                            </div>

                        <?php else: ?>

                            <!-- Jawaban siswa -->
                            <div class="bg-gray-50 rounded-xl p-4 mb-4">

                                <p class="font-semibold text-gray-700 text-sm mb-2">
                                    Jawaban Kamu
                                </p>

                                <p class="text-gray-600 text-sm leading-relaxed">
                                    <?= nl2br(e($detail["jawaban"])) ?>
                                </p>

                            </div>

                            <!-- Pedoman jawaban -->
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

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

    <!-- Tombol bawah -->
    <div class="flex flex-col sm:flex-row justify-center gap-3 mt-8">

        <a href="latihan.php?materi_id=<?= (int) $hasil["materi_id"] ?>&bagian=<?= e($hasil["bagian"]) ?>"
           class="bg-blue-700 hover:bg-blue-800 text-white text-center font-semibold px-6 py-3 rounded-xl transition">
            Kerjakan Lagi
        </a>

        <a href="riwayat_latihan.php"
           class="bg-white hover:bg-gray-100 border border-gray-200 text-darktext text-center font-semibold px-6 py-3 rounded-xl transition">
            Kembali ke Riwayat
        </a>

    </div>

</main>

<?php require_once "includes/footer.php"; ?>