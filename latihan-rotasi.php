<?php
$pageTitle = "Latihan Soal Dinamika Rotasi";

require_once "config/database.php";
require_once "includes/auth.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireLogin();

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$bagian = strtoupper(trim($_GET["bagian"] ?? ""));

$daftarBagian = [
    "A", "B", "C", "D", "E", "ESAI"
];

if (!in_array($bagian, $daftarBagian, true)) {
    header("Location: materi.php");
    exit;
}

$namaBagian = [
    "A" => "Momen Gaya dan Momen Kopel",
    "B" => "Momen Inersia",
    "C" => "Momentum Sudut",
    "D" => "Usaha, Energi, dan Daya pada Gerak Rotasi",
    "E" => "Hukum Newton pada Gerak Rotasi",
    "ESAI" => "Evaluasi Esai"
];

// Ambil materi Dinamika Rotasi
$stmtMateri = mysqli_prepare(
    $conn,
    "SELECT id, judul, kelas
     FROM materi
     WHERE judul = 'Dinamika Rotasi'
     AND status_publikasi = 'publik'
     LIMIT 1"
);

mysqli_stmt_execute($stmtMateri);
$resultMateri = mysqli_stmt_get_result($stmtMateri);
$materi = mysqli_fetch_assoc($resultMateri);
mysqli_stmt_close($stmtMateri);

if (!$materi) {
    exit("Materi Dinamika Rotasi tidak ditemukan.");
}

$materiId = (int) $materi["id"];
$daftarSoal = [];

// Ambil soal dari tabel khusus Dinamika Rotasi
// Tabel saat ini berisi soal pilihan ganda.
if ($bagian !== "ESAI") {

    $stmtSoal = mysqli_prepare(
        $conn,
        "SELECT
            id,
            subbab,
            tingkat_kognitif,
            jenis_soal,
            pertanyaan,
            opsi_a,
            opsi_b,
            opsi_c,
            opsi_d,
            kunci_jawaban
         FROM soal_dinamika_rotasi
         WHERE subbab = ?
         AND jenis_soal = 'pilihan_ganda'
         ORDER BY id ASC"
    );

    mysqli_stmt_bind_param($stmtSoal, "s", $bagian);
    mysqli_stmt_execute($stmtSoal);

    $resultSoal = mysqli_stmt_get_result($stmtSoal);

    while ($row = mysqli_fetch_assoc($resultSoal)) {
        $daftarSoal[] = $row;
    }

    mysqli_stmt_close($stmtSoal);
}

$totalSoal = count($daftarSoal);

$_SESSION["csrf_latihan_rotasi"] = bin2hex(random_bytes(32));
$csrfToken = $_SESSION["csrf_latihan_rotasi"];

require_once "includes/head.php";
?>

<body class="bg-warmwhite text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main class="max-w-5xl mx-auto px-4 py-10">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">

        <div class="mb-8">

            <a href="materi-detail.php?id=<?= $materiId ?>"
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                ← Kembali ke Materi
            </a>

            <div class="mt-5">

                <p class="text-sm font-semibold text-blue-600 mb-2">
                    LATIHAN SOAL DINAMIKA ROTASI
                </p>

                <h1 class="text-2xl md:text-3xl font-bold text-darktext">
                    <?= e($namaBagian[$bagian]) ?>
                </h1>

                <p class="text-gray-500 mt-2">
                    Kerjakan soal berikut dengan teliti dan pilih jawaban
                    yang paling tepat.
                </p>

            </div>

        </div>

        <?php if ($totalSoal === 0): ?>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">

                <h2 class="font-bold text-yellow-800 mb-2">
                    Soal Belum Tersedia
                </h2>

                <p class="text-sm text-yellow-800">
                    Belum ada soal yang tersedia untuk
                    <?= e($namaBagian[$bagian]) ?>.
                    Silakan kembali ke halaman materi.
                </p>

            </div>

        <?php else: ?>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-8">

                <p class="font-semibold text-blue-800">
                    Petunjuk Pengerjaan
                </p>

                <p class="text-sm text-blue-700 mt-1">
                    Pilih satu jawaban yang paling tepat pada setiap soal.
                </p>

                <p class="text-sm font-semibold text-blue-700 mt-3">
                    Jumlah soal: <?= $totalSoal ?>
                </p>

            </div>

            <form action="proses-latihan-rotasi.php" method="POST">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= e($csrfToken) ?>">

                <input type="hidden"
                       name="materi_id"
                       value="<?= $materiId ?>">

                <input type="hidden"
                       name="bagian"
                       value="<?= e($bagian) ?>">

                <div class="space-y-8">

                    <?php foreach ($daftarSoal as $index => $soal): ?>

                        <div class="border border-gray-200 rounded-xl p-5">

                            <div class="flex items-start gap-3 mb-5">

                                <span class="flex-shrink-0 w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                    <?= $index + 1 ?>
                                </span>

                                <div class="font-semibold text-darktext leading-relaxed pt-1">
                                    <?= nl2br(e($soal["pertanyaan"])) ?>
                                </div>

                            </div>

                            <?php
                            $pilihan = [
                                "a" => $soal["opsi_a"],
                                "b" => $soal["opsi_b"],
                                "c" => $soal["opsi_c"],
                                "d" => $soal["opsi_d"]
                            ];
                            ?>

                            <div class="space-y-3 ml-12">

                                <?php foreach ($pilihan as $huruf => $isi): ?>

                                    <label class="flex items-start gap-3 border border-gray-200 rounded-lg p-3 cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">

                                        <input
                                            type="radio"
                                            name="jawaban[<?= (int) $soal["id"] ?>]"
                                            value="<?= e($huruf) ?>"
                                            required
                                            class="mt-1 accent-blue-600"
                                        >

                                        <span class="text-sm text-gray-700 leading-relaxed">
                                            <strong class="uppercase">
                                                <?= e($huruf) ?>.
                                            </strong>
                                            <?= e($isi) ?>
                                        </span>

                                    </label>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-5">

                        <p class="text-sm text-yellow-800">
                            Pastikan seluruh soal sudah dijawab sebelum
                            mengirimkan hasil latihan.
                        </p>

                    </div>

                    <button
                        type="submit"
                        onclick="return confirm('Apakah kamu yakin ingin mengirimkan jawaban latihan?')"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-5 rounded-xl transition"
                    >
                        Kirim Jawaban
                    </button>

                </div>

            </form>

        <?php endif; ?>

    </div>

</main>

<?php require_once "includes/footer.php"; ?>

</body>