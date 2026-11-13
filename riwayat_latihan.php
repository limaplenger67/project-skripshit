
<?php
$pageTitle = "Riwayat Latihan";

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$siswaId = (int) $_SESSION["siswa_id"];

// Ambil riwayat latihan siswa khusus materi Vektor
$query = "
    SELECT
        h.id,
        h.materi_id,
        h.bagian,
        h.jumlah_benar,
        h.jumlah_salah,
        h.nilai,
        h.created_at,
        m.judul,
        (
            SELECT COUNT(*)
            FROM jawaban_siswa js
            WHERE js.hasil_id = h.id
        ) AS total_soal
    FROM hasil_latihan h
    INNER JOIN materi m ON h.materi_id = m.id
    WHERE h.siswa_id = ?
      AND m.judul = 'Vektor'
    ORDER BY h.created_at DESC, h.id DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $siswaId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$riwayat = [];

while ($row = mysqli_fetch_assoc($result)) {
    $riwayat[] = $row;
}

mysqli_stmt_close($stmt);

// Hitung ringkasan riwayat
$totalLatihan = count($riwayat);
$totalPG = 0;
$totalEsai = 0;
$totalNilai = 0;
$jumlahNilai = 0;

foreach ($riwayat as $item) {
    if ($item["bagian"] === "ESAI") {
        $totalEsai++;
    } else {
        $totalPG++;
        $totalNilai += (float) $item["nilai"];
        $jumlahNilai++;
    }
}

$rataRata = $jumlahNilai > 0
    ? $totalNilai / $jumlahNilai
    : 0;

require_once "includes/head.php";
?>

<body class="bg-warmwhite min-h-screen">

<?php require_once "includes/navbar.php"; ?>

<main class="max-w-6xl mx-auto px-4 py-8">

    <!-- Header halaman -->
    <div class="mb-7">
        <a href="materi-detail.php?id=1"
           class="text-sm text-blue-700 hover:text-blue-900 font-medium">
            ← Kembali ke Materi
        </a>

        <div class="mt-4">
            <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full mb-3">
                Aktivitas Belajar
            </span>

            <h1 class="text-3xl font-bold text-darktext mb-2">
                Riwayat Latihan
            </h1>

            <p class="text-gray-600">
                Lihat perkembangan dan hasil latihan materi Vektor yang telah kamu kerjakan.
            </p>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">

        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm text-gray-500 mb-2">Total Latihan</p>
            <p class="text-2xl font-bold text-darktext">
                <?= $totalLatihan ?>
            </p>
            <p class="text-xs text-gray-400 mt-1">Kali pengerjaan</p>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm text-gray-500 mb-2">Pilihan Ganda</p>
            <p class="text-2xl font-bold text-blue-700">
                <?= $totalPG ?>
            </p>
            <p class="text-xs text-gray-400 mt-1">Latihan A, B, dan C</p>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm text-gray-500 mb-2">Latihan Esai</p>
            <p class="text-2xl font-bold text-purple-700">
                <?= $totalEsai ?>
            </p>
            <p class="text-xs text-gray-400 mt-1">Kali pengerjaan</p>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm text-gray-500 mb-2">Rata-rata Nilai</p>
            <p class="text-2xl font-bold text-green-700">
                <?= number_format($rataRata, 1) ?>
            </p>
            <p class="text-xs text-gray-400 mt-1">Pilihan ganda</p>
        </div>

    </div>

    <!-- Daftar riwayat -->
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">

        <div class="p-5 border-b border-gray-100">
            <h2 class="text-lg font-bold text-darktext">
                Daftar Pengerjaan
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Riwayat latihan ditampilkan dari pengerjaan terbaru.
            </p>
        </div>

        <?php if (empty($riwayat)): ?>

            <div class="text-center px-5 py-14">

                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">📝</span>
                </div>

                <h3 class="font-bold text-darktext text-lg mb-2">
                    Belum Ada Riwayat Latihan
                </h3>

                <p class="text-gray-500 text-sm mb-5">
                    Kamu belum mengerjakan latihan materi Vektor.
                    Yuk, mulai latihan pertamamu!
                </p>

                <a href="materi-detail.php?id=1"
                   class="inline-block bg-blue-700 hover:bg-blue-800 text-white font-semibold px-5 py-3 rounded-xl transition">
                    Mulai Latihan
                </a>

            </div>

        <?php else: ?>

            <div class="divide-y divide-gray-100">

                <?php foreach ($riwayat as $index => $item): ?>

                    <?php
                    $isEsai = ($item["bagian"] === "ESAI");

                    $namaBagian = $isEsai
                        ? "Latihan Esai"
                        : "Latihan Bagian " . $item["bagian"];

                    $tanggal = date(
                        "d M Y, H:i",
                        strtotime($item["created_at"])
                    );

                    $nilai = (float) $item["nilai"];
                    ?>

                    <div class="p-5 hover:bg-gray-50 transition">

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                            <!-- Informasi latihan -->
                            <div class="flex items-start gap-3">

                                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0
                                    <?= $isEsai ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">

                                    <span class="font-bold">
                                        <?= $isEsai ? 'E' : e($item["bagian"]) ?>
                                    </span>

                                </div>

                                <div>
                                    <h3 class="font-bold text-darktext">
                                        <?= e($namaBagian) ?>
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        <?= e($item["judul"]) ?>
                                    </p>

                                    <p class="text-xs text-gray-400 mt-2">
                                        <?= e($tanggal) ?>
                                    </p>
                                </div>

                            </div>

                            <!-- Informasi hasil -->
                            <div class="flex flex-wrap items-center gap-3 md:justify-end">

                                <?php if ($isEsai): ?>

                                    <div class="bg-purple-50 text-purple-700 px-3 py-2 rounded-lg text-sm">
                                        <span class="font-semibold">
                                            <?= (int) $item["total_soal"] ?> soal
                                        </span>
                                        <span class="text-xs block">
                                            Esai
                                        </span>
                                    </div>

                                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-2 rounded-full">
                                        Evaluasi Mandiri
                                    </span>

                                <?php else: ?>

                                    <div class="bg-green-50 text-green-700 px-3 py-2 rounded-lg text-sm">
                                        <span class="font-semibold">
                                            <?= (int) $item["jumlah_benar"] ?> benar
                                        </span>
                                    </div>

                                    <div class="bg-red-50 text-red-700 px-3 py-2 rounded-lg text-sm">
                                        <span class="font-semibold">
                                            <?= (int) $item["jumlah_salah"] ?> salah
                                        </span>
                                    </div>

                                    <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-center">
                                        <span class="text-xl font-bold">
                                            <?= number_format($nilai, 0) ?>
                                        </span>
                                        <span class="text-xs block">
                                            Nilai
                                        </span>
                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                        <div class="mt-4 flex justify-end gap-4">

    <a href="detail_riwayat.php?id=<?= (int) $item["id"] ?>"
       class="text-sm text-blue-700 hover:text-blue-900 font-semibold">
        Lihat Hasil →
    </a>

    <a href="latihan.php?materi_id=<?= (int) $item["materi_id"] ?>&bagian=<?= e($item["bagian"]) ?>"
       class="text-sm text-gray-600 hover:text-blue-700 font-semibold">
        Kerjakan Lagi
    </a>

</div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

    <!-- Keterangan -->
    <div class="mt-5 bg-blue-50 border border-blue-100 rounded-xl p-4">
        <p class="text-sm text-blue-800 leading-relaxed">
            <strong>Catatan:</strong>
            Nilai rata-rata dihitung dari seluruh pengerjaan pilihan ganda.
            Latihan esai tidak mendapatkan nilai otomatis karena penilaiannya
            dilakukan secara mandiri dengan membandingkan jawaban dan pedoman jawaban.
        </p>
    </div>

</main>

<?php require_once "includes/footer.php"; ?>