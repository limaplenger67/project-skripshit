
<?php
require_once "config/database.php";
require_once "includes/auth.php";
require_once "includes/feedback_latihan.php";

requireLogin();

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

// Ambil ID hasil latihan
$hasilId = (int) ($_GET["id"] ?? 0);
$siswaId = (int) $_SESSION["siswa_id"];

if ($hasilId <= 0) {
    header("Location: riwayat_latihan.php");
    exit;
}

// Ambil hasil latihan milik siswa yang sedang login
$stmtHasil = mysqli_prepare(
    $conn,
    "SELECT h.*, m.judul, m.kelas
     FROM hasil_latihan h
     JOIN materi m ON m.id = h.materi_id
     WHERE h.id = ?
     AND h.siswa_id = ?
     AND m.judul = 'Vektor'
     LIMIT 1"
);

mysqli_stmt_bind_param($stmtHasil, "ii", $hasilId, $siswaId);
mysqli_stmt_execute($stmtHasil);

$resultHasil = mysqli_stmt_get_result($stmtHasil);
$hasil = mysqli_fetch_assoc($resultHasil);

mysqli_stmt_close($stmtHasil);

if (!$hasil) {
    exit("Data hasil latihan tidak ditemukan atau tidak dapat diakses.");
}

// Ambil jawaban siswa beserta data soal
$stmtJawaban = mysqli_prepare(
    $conn,
    "SELECT
        js.id AS jawaban_id,
        js.jawaban,
        js.status_benar,
        s.id AS soal_id,
        s.bagian,
        s.jenis_soal,
        s.pertanyaan,
        s.pilihan_a,
        s.pilihan_b,
        s.pilihan_c,
        s.pilihan_d,
        s.jawaban_benar,
        s.pembahasan,
        s.kunci_esai
     FROM jawaban_siswa js
     JOIN soal s ON s.id = js.soal_id
     WHERE js.hasil_id = ?
     ORDER BY s.id ASC"
);

mysqli_stmt_bind_param($stmtJawaban, "i", $hasilId);
mysqli_stmt_execute($stmtJawaban);

$resultJawaban = mysqli_stmt_get_result($stmtJawaban);

$daftarJawaban = [];

while ($row = mysqli_fetch_assoc($resultJawaban)) {
    $daftarJawaban[] = $row;
}

mysqli_stmt_close($stmtJawaban);

$isEsai = ($hasil["bagian"] === "ESAI");
$totalSoal = count($daftarJawaban);

// Siapkan umpan balik untuk latihan pilihan ganda
if (!$isEsai) {
    $feedback = getFeedbackLatihan($hasil["nilai"]);
}

$pageTitle = "Detail Hasil Latihan";
require_once "includes/head.php";
?>

<body class="bg-warmwhite text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main class="max-w-5xl mx-auto px-4 py-10">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">

        <div class="mb-8">

            <a href="riwayat_latihan.php"
               class="inline-flex items-center text-sm text-blue-700 hover:text-blue-900 font-semibold mb-5">
                ← Kembali ke Riwayat Latihan
            </a>

            <h1 class="text-2xl md:text-3xl font-bold text-darktext">
                Detail Hasil Latihan
            </h1>

            <p class="text-gray-500 mt-2">
                Materi Vektor - Bagian <?= e($hasil["bagian"]) ?>
            </p>

            <p class="text-sm text-gray-400 mt-1">
                Dikerjakan pada
                <?= date("d-m-Y H:i", strtotime($hasil["created_at"])) ?>
            </p>

        </div>

        <?php if (!$isEsai): ?>

            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-8">

                <p class="text-center text-sm text-gray-600 mb-2">
                    Nilai Latihan
                </p>

                <div class="text-center text-5xl font-bold text-blue-700 mb-6">
                    <?= number_format((float) $hasil["nilai"], 0) ?>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center">

                    <div class="bg-white rounded-xl p-4">
                        <p class="text-sm text-gray-500">Jumlah Soal</p>
                        <p class="text-2xl font-bold text-darktext">
                            <?= $totalSoal ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-4">
                        <p class="text-sm text-gray-500">Jawaban Benar</p>
                        <p class="text-2xl font-bold text-green-600">
                            <?= (int) $hasil["jumlah_benar"] ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-4 col-span-2 md:col-span-1">
                        <p class="text-sm text-gray-500">Jawaban Salah</p>
                        <p class="text-2xl font-bold text-red-500">
                            <?= (int) $hasil["jumlah_salah"] ?>
                        </p>
                    </div>

                </div>

                <?php $warnaFeedback = [
                    "green" => "bg-green-50 border-green-200",
                    "yellow" => "bg-yellow-50 border-yellow-200",
                    "red" => "bg-red-50 border-red-200"
                ]; ?>

                <div class="mt-6 p-4 rounded-xl border <?= $warnaFeedback[$feedback["warna"]] ?? "bg-gray-50 border-gray-200" ?>">

                    <h3 class="font-bold text-darktext mb-2">
                        Umpan Balik: <?= e($feedback["kategori"]) ?>
                    </h3>

                    <p class="text-sm text-gray-700 leading-relaxed mb-2">
                        <?= e($feedback["pesan"]) ?>
                    </p>

                    <p class="text-sm text-gray-600 leading-relaxed">
                        <strong>Saran belajar:</strong>
                        <?= e($feedback["saran"]) ?>
                    </p>

                </div>

            </div>

        <?php else: ?>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 mb-8">

                <h2 class="font-bold text-yellow-800 mb-2">
                    Latihan Esai
                </h2>

                <p class="text-sm text-yellow-800 leading-relaxed">
                    Latihan esai tidak memiliki nilai otomatis.
                    Periksa jawabanmu dengan membandingkannya terhadap
                    pedoman jawaban yang tersedia di bawah ini.
                </p>

                <p class="text-sm text-yellow-800 mt-2">
                    Jumlah soal yang tersimpan: <?= $totalSoal ?>
                </p>

            </div>

        <?php endif; ?>

        <div class="space-y-6">

            <h2 class="text-xl font-bold text-darktext border-b pb-3">
                <?= $isEsai ? "Jawaban dan Pedoman Jawaban" : "Pembahasan Soal" ?>
            </h2>

            <?php if (empty($daftarJawaban)): ?>

                <div class="bg-gray-50 rounded-xl p-5 text-center text-gray-500">
                    Belum ada jawaban yang tersimpan untuk latihan ini.
                </div>

            <?php else: ?>

                <?php foreach ($daftarJawaban as $index => $item): ?>

                    <?php
                    $jawabanPengguna = (string) ($item["jawaban"] ?? "");
                    $jawabanBenar = strtolower((string) ($item["jawaban_benar"] ?? ""));
                    $benar = ($item["status_benar"] !== null)
                        ? ((int) $item["status_benar"] === 1)
                        : false;
                    ?>

                    <div class="border border-gray-200 rounded-xl p-5">

                        <div class="flex items-start gap-3 mb-4">

                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                <?= $index + 1 ?>
                            </span>

                            <div class="font-semibold text-darktext leading-relaxed">
                                <?= nl2br(e($item["pertanyaan"])) ?>
                            </div>

                        </div>

                        <?php if (!$isEsai): ?>

                            <?php
                            $pilihan = [
                                "a" => $item["pilihan_a"],
                                "b" => $item["pilihan_b"],
                                "c" => $item["pilihan_c"],
                                "d" => $item["pilihan_d"]
                            ];
                            ?>

                            <div class="space-y-2 mb-4">

                                <?php foreach ($pilihan as $huruf => $isi): ?>

                                    <?php
                                    $terpilih = (strtolower($jawabanPengguna) === $huruf);
                                    $adalahBenar = ($jawabanBenar === $huruf);

                                    $kelasPilihan = "border-gray-200 bg-white";

                                    if ($adalahBenar) {
                                        $kelasPilihan = "border-green-300 bg-green-50";
                                    } elseif ($terpilih && !$adalahBenar) {
                                        $kelasPilihan = "border-red-300 bg-red-50";
                                    }
                                    ?>

                                    <div class="border rounded-lg p-3 text-sm <?= $kelasPilihan ?>">

                                        <span class="font-semibold uppercase">
                                            <?= e($huruf) ?>.
                                        </span>

                                        <?= e($isi) ?>

                                        <?php if ($adalahBenar): ?>
                                            <span class="text-green-700 font-semibold ml-2">
                                                (Jawaban benar)
                                            </span>
                                        <?php elseif ($terpilih): ?>
                                            <span class="text-red-600 font-semibold ml-2">
                                                (Jawaban kamu)
                                            </span>
                                        <?php endif; ?>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                            <div class="text-sm mb-3">
                                <strong>Jawaban kamu:</strong>

                                <span class="<?= $benar ? "text-green-700" : "text-red-600" ?>">
                                    <?= strtoupper(e($jawabanPengguna)) ?>
                                </span>
                            </div>

                            <div class="bg-blue-50 rounded-lg p-4 text-sm">

                                <p class="font-bold text-blue-800 mb-1">
                                    Pembahasan:
                                </p>

                                <p class="text-gray-700 leading-relaxed">
                                    <?= nl2br(e($item["pembahasan"] ?: "Belum ada pembahasan untuk soal ini.")) ?>
                                </p>

                            </div>

                        <?php else: ?>

                            <div class="bg-gray-50 rounded-lg p-4 mb-3 text-sm">

                                <p class="font-bold text-gray-700 mb-1">
                                    Jawaban kamu:
                                </p>

                                <p class="text-gray-700 leading-relaxed">
                                    <?= nl2br(e($jawabanPengguna)) ?>
                                </p>

                            </div>

                            <div class="bg-blue-50 rounded-lg p-4 text-sm">

                                <p class="font-bold text-blue-800 mb-1">
                                    Pedoman jawaban:
                                </p>

                                <p class="text-gray-700 leading-relaxed">
                                    <?= nl2br(e($item["kunci_esai"] ?: "Pedoman jawaban belum tersedia.")) ?>
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

        <div class="flex flex-col sm:flex-row gap-3 mt-8">

            <a href="latihan.php?materi_id=<?= (int) $hasil["materi_id"] ?>&bagian=<?= e($hasil["bagian"]) ?>"
               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-5 rounded-xl transition">
                Kerjakan Lagi
            </a>

            <a href="riwayat_latihan.php"
               class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-5 rounded-xl transition">
                Kembali ke Riwayat
            </a>

        </div>

    </div>

</main>

<?php require_once "includes/footer.php"; ?>