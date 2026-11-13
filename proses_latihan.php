
<?php
require_once "config/database.php";
require_once "includes/auth.php";
require_once "includes/feedback_latihan.php";

requireLogin();

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

// Pastikan halaman hanya menerima pengiriman formulir
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: materi.php");
    exit;
}

// Validasi token keamanan
$tokenForm = $_POST["csrf_token"] ?? "";
$tokenSession = $_SESSION["csrf_latihan"] ?? "";

if (
    empty($tokenForm) ||
    empty($tokenSession) ||
    !hash_equals($tokenSession, $tokenForm)
) {
    exit("Sesi latihan tidak valid. Silakan kembali dan kerjakan latihan lagi.");
}

unset($_SESSION["csrf_latihan"]);

// Ambil data dari formulir
$materiId = (int) ($_POST["materi_id"] ?? 0);
$bagian = strtoupper(trim($_POST["bagian"] ?? ""));
$jawabanSiswa = $_POST["jawaban"] ?? [];

if (
    $materiId <= 0 ||
    !in_array($bagian, ["A", "B", "C", "ESAI"], true) ||
    !is_array($jawabanSiswa)
) {
    exit("Data latihan tidak valid.");
}

// Pastikan materi adalah Vektor yang sudah dipublikasikan
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
    exit("Materi tidak ditemukan atau belum dipublikasikan.");
}

// Tentukan jenis soal
$jenisSoal = ($bagian === "ESAI") ? "esai" : "pilihan_ganda";

// Ambil soal yang sesuai dengan materi dan bagian
$stmtSoal = mysqli_prepare(
    $conn,
    "SELECT *
     FROM soal
     WHERE materi_id = ?
     AND bagian = ?
     AND jenis_soal = ?
     ORDER BY id ASC"
);

mysqli_stmt_bind_param(
    $stmtSoal,
    "iss",
    $materiId,
    $bagian,
    $jenisSoal
);

mysqli_stmt_execute($stmtSoal);
$resultSoal = mysqli_stmt_get_result($stmtSoal);

$daftarSoal = [];

while ($row = mysqli_fetch_assoc($resultSoal)) {
    $daftarSoal[] = $row;
}

mysqli_stmt_close($stmtSoal);

$totalSoal = count($daftarSoal);

if ($totalSoal === 0) {
    exit("Soal untuk bagian ini belum tersedia.");
}

// Pastikan jumlah jawaban sesuai dengan jumlah soal
if (count($jawabanSiswa) !== $totalSoal) {
    exit("Jumlah jawaban tidak sesuai. Silakan ulangi latihan.");
}

// Periksa jawaban dan hitung nilai
$jumlahBenar = 0;
$jumlahSalah = 0;
$nilai = 0;

$hasilJawaban = [];

foreach ($daftarSoal as $soal) {
    $soalId = (int) $soal["id"];

    if (!array_key_exists($soalId, $jawabanSiswa)) {
        exit("Masih ada soal yang belum dijawab.");
    }

    $jawaban = trim((string) $jawabanSiswa[$soalId]);

    if ($jawaban === "") {
        exit("Semua soal wajib dijawab.");
    }

    if ($jenisSoal === "pilihan_ganda") {
        $jawaban = strtolower($jawaban);

        if (!in_array($jawaban, ["a", "b", "c", "d"], true)) {
            exit("Pilihan jawaban tidak valid.");
        }

        $jawabanBenar = strtolower((string) $soal["jawaban_benar"]);
        $statusBenar = ($jawaban === $jawabanBenar) ? 1 : 0;

        if ($statusBenar === 1) {
            $jumlahBenar++;
        } else {
            $jumlahSalah++;
        }

        $hasilJawaban[] = [
            "soal_id" => $soalId,
            "jawaban" => $jawaban,
            "status_benar" => $statusBenar
        ];

    } else {
        // Esai disimpan untuk diperiksa siswa secara mandiri
        $hasilJawaban[] = [
            "soal_id" => $soalId,
            "jawaban" => $jawaban,
            "status_benar" => null
        ];
    }
}

// Hitung nilai hanya untuk pilihan ganda
if ($jenisSoal === "pilihan_ganda") {
    $nilai = round(($jumlahBenar / $totalSoal) * 100, 2);
}

// Simpan hasil latihan dan jawaban siswa
try {
    mysqli_begin_transaction($conn);

    $stmtHasil = mysqli_prepare(
        $conn,
        "INSERT INTO hasil_latihan
        (siswa_id, materi_id, bagian, jumlah_benar, jumlah_salah, nilai)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $siswaId = (int) $_SESSION["siswa_id"];

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

    mysqli_stmt_execute($stmtHasil);
    $hasilId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmtHasil);

    // Simpan jawaban setiap soal
    $stmtJawaban = mysqli_prepare(
        $conn,
        "INSERT INTO jawaban_siswa
        (hasil_id, soal_id, jawaban, status_benar)
        VALUES (?, ?, ?, ?)"
    );

    foreach ($hasilJawaban as $item) {
        $soalId = $item["soal_id"];
        $jawabanTersimpan = $item["jawaban"];
        $statusBenar = $item["status_benar"];

        mysqli_stmt_bind_param(
            $stmtJawaban,
            "iisi",
            $hasilId,
            $soalId,
            $jawabanTersimpan,
            $statusBenar
        );

        mysqli_stmt_execute($stmtJawaban);
    }

    mysqli_stmt_close($stmtJawaban);

    mysqli_commit($conn);

} catch (Throwable $error) {
    mysqli_rollback($conn);
    exit("Terjadi kesalahan saat menyimpan hasil latihan. Silakan coba kembali.");
}

// Siapkan umpan balik untuk pilihan ganda
if ($jenisSoal === "pilihan_ganda") {
    $feedback = getFeedbackLatihan($nilai);
}

// Mulai menampilkan halaman hasil
$pageTitle = "Hasil Latihan";
require_once "includes/head.php";
?>

<body class="bg-warmwhite text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main class="max-w-5xl mx-auto px-4 py-10">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">

        <div class="text-center mb-8">
            <div class="text-5xl mb-3">
                <?= $jenisSoal === "esai" ? "📝" : "🎉" ?>
            </div>

            <h1 class="text-2xl md:text-3xl font-bold text-darktext">
                <?= $jenisSoal === "esai"
                    ? "Jawaban Esai Berhasil Disimpan!"
                    : "Latihan Selesai!" ?>
            </h1>

            <p class="text-gray-500 mt-2">
                Materi Vektor - Bagian <?= e($bagian) ?>
            </p>
        </div>

        <?php if ($jenisSoal === "pilihan_ganda"): ?>

            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-8">

                <p class="text-center text-sm text-gray-600 mb-2">
                    Nilai Latihan Kamu
                </p>

                <div class="text-center text-5xl font-bold text-blue-700 mb-6">
                    <?= number_format($nilai, 0) ?>
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
                            <?= $jumlahBenar ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-4 col-span-2 md:col-span-1">
                        <p class="text-sm text-gray-500">Jawaban Salah</p>
                        <p class="text-2xl font-bold text-red-500">
                            <?= $jumlahSalah ?>
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
                    Jawaban berhasil disimpan
                </h2>

                <p class="text-sm text-yellow-800 leading-relaxed">
                    Jawaban esai tidak dinilai secara otomatis. Kamu dapat
                    membandingkan jawabanmu dengan pedoman jawaban yang
                    tersedia di bawah ini.
                </p>
            </div>

        <?php endif; ?>

        <div class="space-y-6">

            <h2 class="text-xl font-bold text-darktext border-b pb-3">
                <?= $jenisSoal === "esai"
                    ? "Jawaban dan Pedoman Jawaban"
                    : "Pembahasan Latihan" ?>
            </h2>

            <?php foreach ($daftarSoal as $index => $soal): ?>

                <?php
                $jawabanItem = $hasilJawaban[$index];
                $jawabanPengguna = $jawabanItem["jawaban"];
                $jawabanBenar = strtolower((string) ($soal["jawaban_benar"] ?? ""));
                ?>

                <div class="border border-gray-200 rounded-xl p-5">

                    <div class="flex items-start gap-3 mb-4">

                        <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                            <?= $index + 1 ?>
                        </span>

                        <div class="font-semibold text-darktext leading-relaxed">
                            <?= nl2br(e($soal["pertanyaan"])) ?>
                        </div>

                    </div>

                    <?php if ($jenisSoal === "pilihan_ganda"): ?>

                        <?php
                        $pilihan = [
                            "a" => $soal["pilihan_a"],
                            "b" => $soal["pilihan_b"],
                            "c" => $soal["pilihan_c"],
                            "d" => $soal["pilihan_d"]
                        ];
                        ?>

                        <div class="space-y-2 mb-4">

                            <?php foreach ($pilihan as $huruf => $isi): ?>

                                <?php
                                $terpilih = ($jawabanPengguna === $huruf);
                                $benar = ($jawabanBenar === $huruf);

                                $kelasPilihan = "border-gray-200 bg-white";

                                if ($benar) {
                                    $kelasPilihan = "border-green-300 bg-green-50";
                                } elseif ($terpilih && !$benar) {
                                    $kelasPilihan = "border-red-300 bg-red-50";
                                }
                                ?>

                                <div class="border rounded-lg p-3 text-sm <?= $kelasPilihan ?>">

                                    <span class="font-semibold uppercase">
                                        <?= e($huruf) ?>.
                                    </span>

                                    <?= e($isi) ?>

                                    <?php if ($benar): ?>
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
                            <span class="<?= $jawabanPengguna === $jawabanBenar ? "text-green-700" : "text-red-600" ?>">
                                <?= strtoupper(e($jawabanPengguna)) ?>
                            </span>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-4 text-sm">
                            <p class="font-bold text-blue-800 mb-1">
                                Pembahasan:
                            </p>

                            <p class="text-gray-700 leading-relaxed">
                                <?= nl2br(e($soal["pembahasan"] ?: "Belum ada pembahasan untuk soal ini.")) ?>
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
                                <?= nl2br(e($soal["kunci_esai"] ?: "Pedoman jawaban belum tersedia.")) ?>
                            </p>
                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="flex flex-col sm:flex-row gap-3 mt-8">

            <a href="latihan.php?materi_id=<?= $materiId ?>&bagian=<?= e($bagian) ?>"
               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-5 rounded-xl transition">
                Kerjakan Lagi
            </a>

            <a href="riwayat_latihan.php"
               class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-5 rounded-xl transition">
                Lihat Riwayat Latihan
            </a>

        </div>

    </div>

</main>

<?php require_once "includes/footer.php"; ?>