<?php
$pageTitle = "Detail Materi";

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

// Fungsi untuk mengamankan output
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

// Validasi parameter ID materi
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: materi.php");
    exit;
}

$id = (int) $_GET["id"];

// Ambil data materi
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM materi
     WHERE id = ? AND status_publikasi = 'publik'"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$materi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

mysqli_stmt_close($stmt);

if (!$materi) {
    header("Location: materi.php");
    exit;
}

// Ambil seluruh submateri yang sudah dipublikasikan
$stmtSub = mysqli_prepare(
    $conn,
    "SELECT * FROM submateri
     WHERE materi_id = ?
     AND status_publikasi = 'publik'
     ORDER BY urutan ASC"
);

mysqli_stmt_bind_param($stmtSub, "i", $id);
mysqli_stmt_execute($stmtSub);

$resultSub = mysqli_stmt_get_result($stmtSub);

$submateriRows = [];

while ($row = mysqli_fetch_assoc($resultSub)) {
    $submateriRows[] = $row;
}

mysqli_stmt_close($stmtSub);

// Identifikasi materi Vektor
$isVektor = ($materi["judul"] === "Vektor");

// Hitung jumlah soal berdasarkan bagian dan jenis soal
$jumlahSoal = 0;

$jumlahBagian = [
    "A" => 0,
    "B" => 0,
    "C" => 0,
    "ESAI" => 0
];

$stmtSoal = mysqli_prepare(
    $conn,
    "SELECT bagian, jenis_soal, COUNT(*) AS jumlah
     FROM soal
     WHERE materi_id = ?
     GROUP BY bagian, jenis_soal"
);

mysqli_stmt_bind_param($stmtSoal, "i", $id);
mysqli_stmt_execute($stmtSoal);

$resultSoal = mysqli_stmt_get_result($stmtSoal);

while ($soal = mysqli_fetch_assoc($resultSoal)) {
    $jumlah = (int) $soal["jumlah"];
    $jumlahSoal += $jumlah;

    if ($isVektor) {
        if (
            $soal["bagian"] === "A" &&
            $soal["jenis_soal"] === "pilihan_ganda"
        ) {
            $jumlahBagian["A"] += $jumlah;
        }

        if (
            $soal["bagian"] === "B" &&
            $soal["jenis_soal"] === "pilihan_ganda"
        ) {
            $jumlahBagian["B"] += $jumlah;
        }

        if (
            $soal["bagian"] === "C" &&
            $soal["jenis_soal"] === "pilihan_ganda"
        ) {
            $jumlahBagian["C"] += $jumlah;
        }

        if (
            $soal["bagian"] === "ESAI" &&
            $soal["jenis_soal"] === "esai"
        ) {
            $jumlahBagian["ESAI"] += $jumlah;
        }
    }
}

mysqli_stmt_close($stmtSoal);

// Judul halaman
$pageTitle = $materi["judul"];

$namaBab = [
    "A" => "Konsep Vektor",
    "B" => "Representasi Vektor",
    "C" => "Operasi Vektor"
];

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main>

    <!-- HEADER MATERI -->
    <section class="border-b border-darktext/10 bg-white">
        <div class="mx-auto grid max-w-6xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:py-14">

            <div>
                <div class="flex flex-wrap items-center gap-2">

                    <span class="rounded-full bg-lavender px-3 py-1 text-xs font-semibold text-navy">
                        Kelas <?php echo e($materi["kelas"]); ?>
                    </span>

                    <span class="rounded-full bg-lightblue px-3 py-1 text-xs font-semibold text-navy">
                        <?php echo count($submateriRows); ?> Submateri
                    </span>

                    <span class="rounded-full bg-softyellow/40 px-3 py-1 text-xs font-semibold text-darktext">
                        <?php echo $jumlahSoal; ?> Soal Latihan
                    </span>

                </div>

                <h1 class="mt-4 font-heading text-3xl font-bold text-navy sm:text-4xl">
                    <?php echo e($materi["judul"]); ?>
                </h1>

                <p class="mt-3 max-w-lg text-sm leading-relaxed text-darktext/80 sm:text-base">
                    <?php echo e($materi["deskripsi"]); ?>
                </p>

                <p class="mt-4 text-sm text-darktext/60">
                    Pelajari submateri secara berurutan dari atas ke bawah supaya pemahamanmu berkembang secara bertahap.
                </p>
            </div>

            <div class="flex justify-center lg:justify-end">
                <?php if ($isVektor): ?>
                    <img
                        src="assets/images/vektor-illustration.png"
                        alt="Ilustrasi materi Vektor"
                        class="h-52 w-full max-w-md rounded-2xl object-contain sm:h-64"
                    >
                <?php else: ?>
                    <img
                        src="assets/images/rotasi-illustration.png"
                        alt="Ilustrasi materi Dinamika Rotasi"
                        class="h-52 w-full max-w-md rounded-2xl object-contain sm:h-64"
                    >
                <?php endif; ?>
            </div>

        </div>
    </section>


    <!-- DAFTAR SUBMATERI -->
    <section class="py-9 sm:py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6">

            <div class="mb-5">
                <h2 class="font-heading text-xl font-bold text-navy">
                    Daftar Submateri
                </h2>

                <p class="mt-1 text-sm text-darktext/60">
                    Pilih materi yang ingin kamu pelajari.
                </p>
            </div>

            <div class="flex flex-col gap-3">

                <?php if (count($submateriRows) > 0): ?>

                    <?php
                    $urutan = 1;
                    $babAktif = null;
                    ?>

                    <?php foreach ($submateriRows as $sub): ?>

                        <?php if (!empty($sub["bab"]) && $sub["bab"] !== $babAktif): ?>

                            <?php $babAktif = $sub["bab"]; ?>

                            <h3 class="mt-3 font-heading text-xs font-bold uppercase tracking-wide text-blue sm:text-sm">
                                Bagian <?php echo e($babAktif); ?>
                                ·
                                <?php echo e($namaBab[$babAktif] ?? "Bagian " . $babAktif); ?>
                            </h3>

                        <?php endif; ?>

                        <a
                            href="submateri-detail.php?id=<?php echo (int) $sub["id"]; ?>"
                            class="group flex items-center gap-3 rounded-2xl border border-darktext/10 bg-white p-3.5 transition-all hover:border-blue/30 hover:shadow-md hover:shadow-navy/5 sm:gap-4 sm:p-4"
                        >

                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-lightblue font-heading text-sm font-bold text-navy sm:h-10 sm:w-10">
                                <?php echo $urutan++; ?>
                            </span>

                            <span class="flex-1 font-heading text-sm font-semibold text-darktext sm:text-base">
                                <?php echo e($sub["judul"]); ?>
                            </span>

                            <svg
                                class="shrink-0 text-darktext/40 transition-colors group-hover:text-navy"
                                xmlns="http://www.w3.org/2000/svg"
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="m9 18 6-6-6-6"/>
                            </svg>

                        </a>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="rounded-2xl border border-darktext/10 bg-white p-8 text-center text-sm text-darktext/60">
                        Submateri untuk materi ini belum tersedia.
                    </div>

                <?php endif; ?>

            </div>


            <!-- FITUR LATIHAN DAN CHATBOT -->
            <div class="mt-9">

                <?php if ($isVektor): ?>

                    <div class="mb-4">
                        <h2 class="font-heading text-xl font-bold text-navy">
                            Latihan Soal
                        </h2>

                        <p class="mt-1 text-sm leading-relaxed text-darktext/65">
                            Kerjakan latihan berdasarkan bagian materi untuk mengukur pemahamanmu.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">

                        <!-- LATIHAN BAGIAN A -->
                        <article class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-blue">
                                        Bagian A
                                    </span>

                                    <h3 class="mt-1 font-heading font-bold text-navy">
                                        Konsep Vektor
                                    </h3>
                                </div>

                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-lightblue font-heading font-bold text-navy">
                                    A
                                </span>
                            </div>

                            <p class="mt-2 text-sm text-darktext/65">
                                <?php echo $jumlahBagian["A"]; ?> dari 15 soal pilihan ganda
                            </p>

                            <?php if ($jumlahBagian["A"] > 0): ?>
                                <a
                                    href="latihan.php?materi_id=<?php echo $id; ?>&bagian=A"
                                    class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-navy px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue"
                                >
                                    Mulai Latihan
                                </a>
                            <?php else: ?>
                                <span class="mt-4 inline-flex w-full justify-center rounded-full bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-500">
                                    Soal belum tersedia
                                </span>
                            <?php endif; ?>
                        </article>


                        <!-- LATIHAN BAGIAN B -->
                        <article class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-blue">
                                        Bagian B
                                    </span>

                                    <h3 class="mt-1 font-heading font-bold text-navy">
                                        Representasi Vektor
                                    </h3>
                                </div>

                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-lightblue font-heading font-bold text-navy">
                                    B
                                </span>
                            </div>

                            <p class="mt-2 text-sm text-darktext/65">
                                <?php echo $jumlahBagian["B"]; ?> dari 15 soal pilihan ganda
                            </p>

                            <?php if ($jumlahBagian["B"] > 0): ?>
                                <a
                                    href="latihan.php?materi_id=<?php echo $id; ?>&bagian=B"
                                    class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-navy px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue"
                                >
                                    Mulai Latihan
                                </a>
                            <?php else: ?>
                                <span class="mt-4 inline-flex w-full justify-center rounded-full bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-500">
                                    Soal belum tersedia
                                </span>
                            <?php endif; ?>
                        </article>


                        <!-- LATIHAN BAGIAN C -->
                        <article class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-blue">
                                        Bagian C
                                    </span>

                                    <h3 class="mt-1 font-heading font-bold text-navy">
                                        Operasi Vektor
                                    </h3>
                                </div>

                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-lightblue font-heading font-bold text-navy">
                                    C
                                </span>
                            </div>

                            <p class="mt-2 text-sm text-darktext/65">
                                <?php echo $jumlahBagian["C"]; ?> dari 15 soal pilihan ganda
                            </p>

                            <?php if ($jumlahBagian["C"] > 0): ?>
                                <a
                                    href="latihan.php?materi_id=<?php echo $id; ?>&bagian=C"
                                    class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-navy px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue"
                                >
                                    Mulai Latihan
                                </a>
                            <?php else: ?>
                                <span class="mt-4 inline-flex w-full justify-center rounded-full bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-500">
                                    Soal belum tersedia
                                </span>
                            <?php endif; ?>
                        </article>


                        <!-- LATIHAN ESAI -->
                        <article class="rounded-2xl border border-purple-100 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-purple-600">
                                        Evaluasi
                                    </span>

                                    <h3 class="mt-1 font-heading font-bold text-navy">
                                        Latihan Esai
                                    </h3>
                                </div>

                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-lavender font-heading font-bold text-navy">
                                    E
                                </span>
                            </div>

                            <p class="mt-2 text-sm text-darktext/65">
                                <?php echo $jumlahBagian["ESAI"]; ?> dari 5 soal esai
                            </p>

                            <?php if ($jumlahBagian["ESAI"] > 0): ?>
                                <a
                                    href="latihan.php?materi_id=<?php echo $id; ?>&bagian=ESAI"
                                    class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-purple-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-purple-800"
                                >
                                    Mulai Latihan
                                </a>
                            <?php else: ?>
                                <span class="mt-4 inline-flex w-full justify-center rounded-full bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-500">
                                    Soal belum tersedia
                                </span>
                            <?php endif; ?>
                        </article>

                    </div>

                    <p class="mt-3 text-xs leading-relaxed text-darktext/50">
                        Target latihan terdiri dari 45 soal pilihan ganda dan 5 soal esai. Tombol akan aktif setelah soal dimasukkan ke database.
                    </p>

                <?php else: ?>

                    <!-- TAMPILAN MATERI SELAIN VEKTOR -->
                    <div class="rounded-2xl border border-darktext/10 bg-lightblue p-5">
                        <h3 class="font-heading text-sm font-semibold text-navy">
                            Latihan Soal
                        </h3>

                        <p class="mt-1 text-sm text-darktext/70">
                            Latihan untuk materi ini belum tersedia.
                        </p>
                    </div>

                <?php endif; ?>


                <!-- CHATBOT -->
                <div class="mt-4 rounded-2xl border border-darktext/10 bg-lavender p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-heading text-sm font-semibold text-navy">
                                Butuh Bantuan Belajar?
                            </h3>

                            <p class="mt-1 text-sm text-darktext/70">
                                Tanyakan konsep yang belum kamu pahami kepada PhyBot.
                            </p>
                        </div>

                        <a
                            href="chat.php"
                            class="inline-flex shrink-0 items-center justify-center rounded-full bg-navy px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue"
                        >
                            Tanya PhyBot
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </section>

</main>

<?php require_once "includes/footer.php"; ?>