
<?php
$pageTitle = "Latihan Soal";

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

// Validasi parameter
if (
    !isset($_GET["materi_id"], $_GET["bagian"]) ||
    !is_numeric($_GET["materi_id"])
) {
    header("Location: materi.php");
    exit;
}

$materiId = (int) $_GET["materi_id"];
$bagian = strtoupper(trim($_GET["bagian"]));

$daftarBagian = ["A", "B", "C", "ESAI"];

if (!in_array($bagian, $daftarBagian, true)) {
    header("Location: materi-detail.php?id=" . $materiId);
    exit;
}

// Ambil data materi
$stmt = mysqli_prepare(
    $conn,
    "SELECT id, judul, kelas
     FROM materi
     WHERE id = ? AND status_publikasi = 'publik'"
);

mysqli_stmt_bind_param($stmt, "i", $materiId);
mysqli_stmt_execute($stmt);

$materi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$materi || $materi["judul"] !== "Vektor") {
    header("Location: materi.php");
    exit;
}

// Tentukan jenis soal
$jenisSoal = ($bagian === "ESAI") ? "esai" : "pilihan_ganda";

// Ambil soal sesuai bagian
$stmtSoal = mysqli_prepare(
    $conn,
    "SELECT id, pertanyaan, pilihan_a, pilihan_b,
            pilihan_c, pilihan_d, tingkat_kesulitan
     FROM soal
     WHERE materi_id = ?
       AND bagian = ?
       AND jenis_soal = ?
     ORDER BY RAND()"
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

// Informasi bagian
$namaBagian = [
    "A" => "Konsep Vektor",
    "B" => "Representasi Vektor",
    "C" => "Operasi Vektor",
    "ESAI" => "Evaluasi Esai"
];

$targetSoal = ($bagian === "ESAI") ? 5 : 15;
$totalSoal = count($daftarSoal);

$pageTitle = "Latihan " . $namaBagian[$bagian];

// Token keamanan formulir
if (empty($_SESSION["csrf_latihan"])) {
    $_SESSION["csrf_latihan"] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION["csrf_latihan"];

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main class="pb-12">

    <!-- HEADER LATIHAN -->
    <section class="border-b border-darktext/10 bg-white">
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6">

            <a
                href="materi-detail.php?id=<?php echo $materiId; ?>"
                class="inline-flex items-center gap-2 text-sm font-medium text-darktext/60 transition hover:text-navy"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="18" height="18"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6"/>
                    <path d="M20 12H9"/>
                </svg>
                Kembali ke Detail Materi
            </a>

            <div class="mt-5 flex flex-wrap items-center gap-2">

                <span class="rounded-full bg-lightblue px-3 py-1 text-xs font-semibold text-navy">
                    Materi Vektor
                </span>

                <span class="rounded-full bg-lavender px-3 py-1 text-xs font-semibold text-navy">
                    <?php echo e($materi["kelas"]); ?>
                </span>

                <span class="rounded-full bg-softyellow/50 px-3 py-1 text-xs font-semibold text-darktext">
                    <?php echo $totalSoal; ?> Soal
                </span>

            </div>

            <h1 class="mt-3 font-heading text-2xl font-bold text-navy sm:text-3xl">
                <?php echo e($namaBagian[$bagian]); ?>
            </h1>

            <p class="mt-2 text-sm leading-relaxed text-darktext/70">
                <?php if ($bagian === "ESAI"): ?>
                    Jawablah pertanyaan berikut dengan uraian yang jelas dan lengkap.
                    Setelah dikirim, kamu dapat membandingkan jawabanmu dengan kunci jawaban.
                <?php else: ?>
                    Pilih satu jawaban yang paling tepat pada setiap pertanyaan.
                    Hasil dan pembahasan akan ditampilkan setelah seluruh soal dikumpulkan.
                <?php endif; ?>
            </p>

        </div>
    </section>


    <!-- KONTEN LATIHAN -->
    <section class="mx-auto max-w-4xl px-4 pt-7 sm:px-6">

        <?php if ($totalSoal === 0): ?>

            <div class="rounded-2xl border border-blue-100 bg-white p-8 text-center shadow-sm">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-lightblue text-navy">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="28" height="28"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M8 6h13"/>
                        <path d="M8 12h13"/>
                        <path d="M8 18h13"/>
                        <path d="M3 6h.01"/>
                        <path d="M3 12h.01"/>
                        <path d="M3 18h.01"/>
                    </svg>
                </div>

                <h2 class="mt-4 font-heading text-lg font-bold text-navy">
                    Soal Belum Tersedia
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-darktext/60">
                    Belum ada soal yang ditambahkan untuk
                    <?php echo e($namaBagian[$bagian]); ?>.
                    Silakan kembali lagi setelah soal tersedia.
                </p>

                <a
                    href="materi-detail.php?id=<?php echo $materiId; ?>"
                    class="mt-5 inline-flex items-center justify-center rounded-full bg-navy px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue"
                >
                    Kembali ke Materi
                </a>

            </div>

        <?php else: ?>

            <!-- INFORMASI JUMLAH SOAL -->
            <div class="mb-5 rounded-2xl border border-blue-100 bg-lightblue/60 p-4">

                <div class="flex items-center justify-between gap-3">

                    <div>
                        <p class="text-sm font-semibold text-navy">
                            Progres Pengerjaan
                        </p>

                        <p class="mt-1 text-xs text-darktext/65">
                            Total <?php echo $totalSoal; ?> soal tersedia
                        </p>
                    </div>

                    <span class="rounded-full bg-white px-3 py-1.5 text-xs font-bold text-navy">
                        <?php echo $bagian === "ESAI" ? "Esai" : "Pilihan Ganda"; ?>
                    </span>

                </div>

                <?php if ($totalSoal < $targetSoal): ?>
                    <p class="mt-3 text-xs leading-relaxed text-darktext/70">
                        Perhatian: target bagian ini adalah
                        <?php echo $targetSoal; ?> soal, tetapi baru
                        <?php echo $totalSoal; ?> soal yang tersedia.
                    </p>
                <?php endif; ?>

            </div>


            <!-- FORM PENGERJAAN -->
            <form
                action="proses_latihan.php"
                method="POST"
                id="form-latihan"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php echo e($csrfToken); ?>"
                >

                <input
                    type="hidden"
                    name="materi_id"
                    value="<?php echo $materiId; ?>"
                >

                <input
                    type="hidden"
                    name="bagian"
                    value="<?php echo e($bagian); ?>"
                >


                <div class="flex flex-col gap-4">

                    <?php foreach ($daftarSoal as $index => $soal): ?>

                        <article class="rounded-2xl border border-darktext/10 bg-white p-4 shadow-sm sm:p-5">

                            <!-- NOMOR DAN KESULITAN -->
                            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">

                                <span class="rounded-full bg-lightblue px-3 py-1 text-xs font-bold text-navy">
                                    Soal <?php echo $index + 1; ?>
                                </span>

                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-darktext/60">
                                    <?php echo e(ucfirst($soal["tingkat_kesulitan"] ?? "Mudah")); ?>
                                </span>

                            </div>


                            <!-- PERTANYAAN -->
                            <div class="text-sm leading-relaxed text-darktext sm:text-base">
                                <?php echo nl2br(e($soal["pertanyaan"])); ?>
                            </div>


                            <?php if ($bagian === "ESAI"): ?>

                                <!-- JAWABAN ESAI -->
                                <div class="mt-5">

                                    <label
                                        for="jawaban_<?php echo (int) $soal["id"]; ?>"
                                        class="mb-2 block text-sm font-semibold text-navy"
                                    >
                                        Jawabanmu
                                    </label>

                                    <textarea
                                        id="jawaban_<?php echo (int) $soal["id"]; ?>"
                                        name="jawaban[<?php echo (int) $soal["id"]; ?>]"
                                        rows="5"
                                        required
                                        placeholder="Tuliskan jawabanmu di sini..."
                                        class="w-full rounded-xl border border-darktext/15 bg-warmwhite px-4 py-3 text-sm leading-relaxed outline-none transition focus:border-blue focus:ring-2 focus:ring-blue/15"
                                    ></textarea>

                                </div>

                            <?php else: ?>

                                <!-- PILIHAN GANDA -->
                                <div class="mt-5 flex flex-col gap-2">

                                    <?php
                                    $pilihan = [
                                        "a" => $soal["pilihan_a"],
                                        "b" => $soal["pilihan_b"],
                                        "c" => $soal["pilihan_c"],
                                        "d" => $soal["pilihan_d"]
                                    ];
                                    ?>

                                    <?php foreach ($pilihan as $huruf => $isi): ?>

                                        <?php if ($isi !== null && $isi !== ""): ?>

                                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-darktext/10 p-3 transition hover:border-blue/40 hover:bg-lightblue/30">

                                                <input
                                                    type="radio"
                                                    name="jawaban[<?php echo (int) $soal["id"]; ?>]"
                                                    value="<?php echo $huruf; ?>"
                                                    required
                                                    class="mt-1 h-4 w-4 shrink-0 accent-[#173A70]"
                                                >

                                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-lightblue text-xs font-bold uppercase text-navy">
                                                    <?php echo $huruf; ?>
                                                </span>

                                                <span class="text-sm leading-relaxed text-darktext">
                                                    <?php echo e($isi); ?>
                                                </span>

                                            </label>

                                        <?php endif; ?>

                                    <?php endforeach; ?>

                                </div>

                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>


                <!-- TOMBOL KIRIM -->
                <div class="mt-6 rounded-2xl border border-darktext/10 bg-white p-4 shadow-sm sm:p-5">

                    <p class="text-sm leading-relaxed text-darktext/65">
                        <?php if ($bagian === "ESAI"): ?>
                            Pastikan seluruh jawaban sudah terisi sebelum mengirimkan latihan.
                            Kamu dapat melihat kunci jawaban setelah pengiriman.
                        <?php else: ?>
                            Pastikan seluruh pertanyaan sudah dijawab.
                            Nilai dan pembahasan akan muncul setelah latihan dikumpulkan.
                        <?php endif; ?>
                    </p>

                    <button
                        type="submit"
                        class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-full bg-navy px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue sm:w-auto"
                        onclick="return confirm('Apakah kamu yakin ingin mengumpulkan jawaban?');"
                    >
                        Kumpulkan Jawaban

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="18" height="18"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
                    </button>

                </div>

            </form>

        <?php endif; ?>

    </section>

</main>

<?php require_once "includes/footer.php"; ?>