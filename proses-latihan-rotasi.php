
<?php
$pageTitle = "Hasil Latihan Dinamika Rotasi";

require_once "config/database.php";
require_once "includes/auth.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireLogin();

// ==========================================
// FUNGSI BANTU
// ==========================================
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function berhenti($pesan) {
    echo "<div style='font-family:Arial;padding:30px;text-align:center'>";
    echo "<h2>Terjadi Kesalahan</h2>";
    echo "<p>" . e($pesan) . "</p>";
    echo "<a href='materi-detail.php?id=2'>Kembali ke Materi</a>";
    echo "</div>";
    exit;
}

// ==========================================
// VALIDASI REQUEST DAN CSRF
// ==========================================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: materi-detail.php?id=2");
    exit;
}

$token = $_POST["csrf_token"] ?? "";
$tokenSession = $_SESSION["csrf_latihan_rotasi"] ?? "";

if (
    empty($token) ||
    empty($tokenSession) ||
    !hash_equals($tokenSession, $token)
) {
    berhenti("Sesi latihan tidak valid. Silakan ulangi latihan.");
}

$materiId = filter_input(INPUT_POST, "materi_id", FILTER_VALIDATE_INT);
$bagian = strtoupper(trim($_POST["bagian"] ?? ""));
$jawaban = $_POST["jawaban"] ?? [];

if ($materiId != 2) {
    berhenti("Materi tidak valid.");
}

$bagianValid = ["A", "B", "C", "D", "E", "ESAI"];

if (!in_array($bagian, $bagianValid, true)) {
    berhenti("Bagian latihan tidak valid.");
}

if (!is_array($jawaban)) {
    berhenti("Format jawaban tidak valid.");
}

$siswaId = $_SESSION["siswa_id"] ?? 0;

if (!$siswaId) {
    berhenti("Data siswa tidak ditemukan. Silakan login kembali.");
}

$siswaId = (int) $siswaId;

// ==========================================
// VALIDASI MATERI
// ==========================================
$stmtMateri = mysqli_prepare(
    $conn,
    "SELECT id, judul FROM materi
     WHERE id = ?
       AND judul = 'Dinamika Rotasi'
       AND status_publikasi = 'publik'
     LIMIT 1"
);

if (!$stmtMateri) {
    berhenti("Gagal memeriksa data materi.");
}

mysqli_stmt_bind_param($stmtMateri, "i", $materiId);
mysqli_stmt_execute($stmtMateri);

$resultMateri = mysqli_stmt_get_result($stmtMateri);
$materi = mysqli_fetch_assoc($resultMateri);

mysqli_stmt_close($stmtMateri);

if (!$materi) {
    berhenti("Materi Dinamika Rotasi tidak ditemukan.");
}

// ==========================================
// AMBIL SOAL DINAMIKA ROTASI
// ==========================================
if ($bagian === "ESAI") {
    $sqlSoal = "SELECT *
                FROM soal_dinamika_rotasi
                WHERE jenis_soal = 'esai'
                ORDER BY id ASC";

    $stmtSoal = mysqli_prepare($conn, $sqlSoal);

} else {
    $sqlSoal = "SELECT *
                FROM soal_dinamika_rotasi
                WHERE subbab = ?
                  AND jenis_soal = 'pilihan_ganda'
                ORDER BY id ASC";

    $stmtSoal = mysqli_prepare($conn, $sqlSoal);

    if ($stmtSoal) {
        mysqli_stmt_bind_param($stmtSoal, "s", $bagian);
    }
}

if (!$stmtSoal) {
    berhenti("Gagal mengambil soal dari database.");
}

mysqli_stmt_execute($stmtSoal);
$resultSoal = mysqli_stmt_get_result($stmtSoal);

$daftarSoal = [];

while ($row = mysqli_fetch_assoc($resultSoal)) {
    $daftarSoal[] = $row;
}

mysqli_stmt_close($stmtSoal);

if (count($daftarSoal) === 0) {
    berhenti("Soal untuk bagian ini belum tersedia.");
}

// ==========================================
// HITUNG NILAI DAN SIAPKAN JAWABAN
// ==========================================
$totalSoal = count($daftarSoal);
$jumlahBenar = 0;
$jumlahSalah = 0;
$jumlahTerjawab = 0;
$hasilJawaban = [];

foreach ($daftarSoal as $soal) {
    $soalId = (int) $soal["id"];

    $jawabanSiswa = $jawaban[$soalId] ?? "";

    if (!is_scalar($jawabanSiswa)) {
        $jawabanSiswa = "";
    }

    $jawabanSiswa = trim((string) $jawabanSiswa);

    $kunci = strtolower(trim((string) ($soal["kunci_jawaban"] ?? "")));
    $jenis = strtolower(trim((string) ($soal["jenis_soal"] ?? "pilihan_ganda")));

    $statusBenar = null;

    if ($jenis === "esai" || $bagian === "ESAI") {

        // Esai belum dinilai otomatis.
        if ($jawabanSiswa !== "") {
            $jumlahTerjawab++;
        }

        $statusBenar = null;

    } else {

        if ($jawabanSiswa !== "") {
            $jumlahTerjawab++;
        }

        if (
            $jawabanSiswa !== "" &&
            strtolower($jawabanSiswa) === $kunci
        ) {
            $jumlahBenar++;
            $statusBenar = 1;
        } else {
            $jumlahSalah++;
            $statusBenar = 0;
        }
    }

    $hasilJawaban[] = [
        "soal" => $soal,
        "jawaban" => $jawabanSiswa,
        "status" => $statusBenar
    ];
}

// ==========================================
// HITUNG NILAI AKHIR
// ==========================================
if ($bagian === "ESAI") {
    $nilai = 0;
} else {
    $nilai = round(($jumlahBenar / $totalSoal) * 100, 2);
}

// ==========================================
// SIMPAN HASIL DAN JAWABAN
// ==========================================
mysqli_begin_transaction($conn);

try {

    // SIMPAN HASIL LATIHAN
    $stmtHasil = mysqli_prepare(
        $conn,
        "INSERT INTO hasil_latihan_rotasi
        (
            siswa_id,
            materi_id,
            bagian,
            jumlah_benar,
            jumlah_salah,
            nilai
        )
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    if (!$stmtHasil) {
        throw new Exception(
            "Gagal menyiapkan hasil: " . mysqli_error($conn)
        );
    }

    // i = integer
    // s = string
    // d = double
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
        throw new Exception(
            "Gagal menyimpan hasil: " . mysqli_stmt_error($stmtHasil)
        );
    }

    $hasilId = mysqli_insert_id($conn);

    mysqli_stmt_close($stmtHasil);

    if (!$hasilId) {
        throw new Exception("ID hasil latihan tidak berhasil dibuat.");
    }

    // SIMPAN JAWABAN SISWA
    $stmtJawaban = mysqli_prepare(
        $conn,
        "INSERT INTO 
        jawaban_siswa_rotasi
        (hasil_id, soal_rotasi_id, jawaban, status_benar)

        VALUES (?, ?, ?, ?)"
    );

    if (!$stmtJawaban) {
        throw new Exception(
            "Gagal menyiapkan jawaban: " . mysqli_error($conn)
        );
    }

    foreach ($hasilJawaban as $item) {

        $soalId = (int) $item["soal"]["id"];
        $jawabanSiswa = $item["jawaban"];
        $statusBenar = $item["status"];

        mysqli_stmt_bind_param(
            $stmtJawaban,
            "iisi",
            $hasilId,
            $soalId,
            $jawabanSiswa,
            $statusBenar
        );

        if (!mysqli_stmt_execute($stmtJawaban)) {
            throw new Exception(
                "Gagal menyimpan jawaban soal ID "
                . $soalId
                . ": "
                . mysqli_stmt_error($stmtJawaban)
            );
        }
    }

    mysqli_stmt_close($stmtJawaban);

    // SEMUA BERHASIL, SIMPAN PERMANEN
    mysqli_commit($conn);

} catch (Throwable $error) {
    mysqli_rollback($conn);

    die(
        "<h3>ERROR LATIHAN ROTASI</h3>" .
        "<pre>" . htmlspecialchars($error->getMessage(), ENT_QUOTES, "UTF-8") . "</pre>"
    );
}

// ==========================================
// TAMPILAN HASIL LATIHAN
// ==========================================
unset($_SESSION["csrf_latihan_rotasi"]);

$namaBagian = [
    "A" => "Momen Gaya dan Momen Kopel",
    "B" => "Momen Inersia",
    "C" => "Momentum Sudut",
    "D" => "Usaha, Energi, dan Daya pada Gerak Rotasi",
    "E" => "Hukum Newton pada Gerak Rotasi",
    "ESAI" => "Latihan Esai"
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($pageTitle) ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen">

<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">

        <!-- HEADER HASIL -->
        <div class="text-center mb-8">

            <div class="text-4xl mb-3">🎉</div>

            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                Latihan Selesai!
            </h1>

            <p class="text-slate-500 mt-2">
                <?= e($namaBagian[$bagian]) ?>
            </p>

        </div>

        <?php if ($bagian !== "ESAI"): ?>

            <!-- NILAI -->
            <div class="bg-blue-50 rounded-xl p-6 text-center mb-6">

                <p class="text-sm text-blue-700 font-medium">
                    Nilai Latihan Kamu
                </p>

                <div class="text-5xl font-bold text-blue-700 my-3">
                    <?= number_format($nilai, 2, ",", ".") ?>
                </div>

                <p class="text-sm text-slate-600">
                    <?= $jumlahBenar ?> benar dari <?= $totalSoal ?> soal
                </p>

            </div>

            <!-- RINGKASAN -->
            <div class="grid grid-cols-2 gap-4 mb-8">

                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-green-700">
                        Jawaban Benar
                    </p>

                    <p class="text-2xl font-bold text-green-700">
                        <?= $jumlahBenar ?>
                    </p>
                </div>

                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-red-700">
                        Jawaban Salah
                    </p>

                    <p class="text-2xl font-bold text-red-700">
                        <?= $jumlahSalah ?>
                    </p>
                </div>

            </div>

        <?php else: ?>

            <!-- HASIL ESAI -->
            <div class="bg-amber-50 rounded-xl p-5 mb-8">

                <h2 class="font-bold text-amber-800 mb-2">
                    Jawaban Esai Tersimpan
                </h2>

                <p class="text-sm text-amber-700">
                    Jawaban esai belum dinilai secara otomatis.
                    Cocokkan jawabanmu dengan pedoman jawaban yang tersedia di bawah.
                </p>

                <p class="text-sm text-amber-700 mt-2">
                    Jumlah jawaban terisi:
                    <?= $jumlahTerjawab ?> dari <?= $totalSoal ?>.
                </p>

            </div>

        <?php endif; ?>

        <!-- PEMBAHASAN -->
        <div class="border-t border-slate-200 pt-6">

            <h2 class="text-xl font-bold text-slate-800 mb-5">
                Pembahasan dan Evaluasi
            </h2>

            <div class="space-y-6">

                <?php foreach ($hasilJawaban as $index => $item): ?>

                    <?php
                    $soal = $item["soal"];
                    $jawabanSiswa = $item["jawaban"];
                    $status = $item["status"];

                    $jenis = strtolower(
                        $soal["jenis_soal"] ?? "pilihan_ganda"
                    );

                    $kunci = strtolower(
                        $soal["kunci_jawaban"] ?? ""
                    );

                    $opsi = [
                        "a" => $soal["opsi_a"] ?? "",
                        "b" => $soal["opsi_b"] ?? "",
                        "c" => $soal["opsi_c"] ?? "",
                        "d" => $soal["opsi_d"] ?? ""
                    ];
                    ?>

                    <div class="border border-slate-200 rounded-xl p-5">

                        <div class="flex flex-wrap justify-between gap-2 mb-3">

                            <span class="font-semibold text-slate-700">
                                Soal <?= $index + 1 ?>
                            </span>

                            <?php if ($jenis === "esai" || $bagian === "ESAI"): ?>

                                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-amber-100 text-amber-700">
                                    Esai
                                </span>

                            <?php elseif ($status === 1): ?>

                                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-green-100 text-green-700">
                                    Benar
                                </span>

                            <?php else: ?>

                                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-red-100 text-red-700">
                                    Salah
                                </span>

                            <?php endif; ?>

                        </div>

                        <p class="text-slate-800 font-medium leading-relaxed mb-4">
                            <?= nl2br(e($soal["pertanyaan"])) ?>
                        </p>

                        <?php if ($jenis === "esai" || $bagian === "ESAI"): ?>

                            <!-- JAWABAN ESAI -->
                            <div class="bg-slate-50 rounded-lg p-4 mb-3">

                                <p class="text-sm font-semibold text-slate-600 mb-2">
                                    Jawaban Kamu
                                </p>

                                <p class="text-sm text-slate-700 whitespace-pre-line">
                                    <?= $jawabanSiswa !== ""
                                        ? e($jawabanSiswa)
                                        : "Tidak dijawab." ?>
                                </p>

                            </div>

                            <!-- KUNCI ESAI -->
                            <div class="bg-blue-50 rounded-lg p-4">

                                <p class="text-sm font-semibold text-blue-800 mb-2">
                                    Pedoman Jawaban
                                </p>

                                <p class="text-sm text-blue-900 whitespace-pre-line">
                                    <?= nl2br(
                                        e(
                                            $soal["kunci_esai"]
                                            ?? "Pedoman jawaban belum tersedia."
                                        )
                                    ) ?>
                                </p>

                            </div>

                        <?php else: ?>

                            <!-- PILIHAN GANDA -->
                            <div class="space-y-2 mb-4">

                                <?php foreach ($opsi as $huruf => $isi): ?>

                                    <?php
                                    $kelasOpsi = "bg-slate-50 text-slate-700";

                                    if ($huruf === $kunci) {
                                        $kelasOpsi = "bg-green-50 border border-green-200 text-green-800";
                                    } elseif (
                                        $huruf === strtolower($jawabanSiswa)
                                        && $status === 0
                                    ) {
                                        $kelasOpsi = "bg-red-50 border border-red-200 text-red-800";
                                    }
                                    ?>

                                    <div class="text-sm rounded-lg p-3 <?= $kelasOpsi ?>">

                                        <span class="font-semibold uppercase">
                                            <?= e($huruf) ?>.
                                        </span>

                                        <?= e($isi) ?>

                                        <?php if ($huruf === $kunci): ?>
                                            <span class="font-semibold">
                                                (Kunci Jawaban)
                                            </span>
                                        <?php endif; ?>

                                        <?php if (
                                            $huruf === strtolower($jawabanSiswa)
                                            && $status === 0
                                        ): ?>
                                            <span class="font-semibold">
                                                (Jawaban Kamu)
                                            </span>
                                        <?php endif; ?>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                            <!-- PEMBAHASAN -->
                            <div class="bg-blue-50 rounded-lg p-4">

                                <p class="text-sm font-semibold text-blue-800 mb-2">
                                    Pembahasan
                                </p>

                                <p class="text-sm text-blue-900 whitespace-pre-line">
                                    <?= nl2br(
                                        e(
                                            $soal["pembahasan"]
                                            ?? "Pembahasan belum tersedia."
                                        )
                                    ) ?>
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <!-- TOMBOL -->
        <div class="flex flex-col sm:flex-row gap-3 mt-8">

            <a
                href="latihan-rotasi.php?bagian=<?= urlencode($bagian) ?>"
                class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-5 rounded-xl"
            >
                Ulangi Latihan
            </a>

            <a
                href="materi-detail.php?id=2"
                class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-5 rounded-xl"
            >
                Kembali ke Materi
            </a>

        </div>

    </div>

</div>

</body>
</html>
