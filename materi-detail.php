<?php
$pageTitle = "Detail Materi";
require_once "config/database.php";
require_once "includes/auth.php";
requireLogin();

// Validasi parameter id
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: materi.php");
    exit;
}

$id = (int) $_GET["id"];

// Ambil data materi
$stmt = mysqli_prepare($conn, "SELECT * FROM materi WHERE id = ? AND status_publikasi = 'publik'");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$materi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$materi) {
    header("Location: materi.php");
    exit;
}

// Ambil daftar submateri yang dipublikasikan
$stmtSub = mysqli_prepare($conn, "SELECT * FROM submateri WHERE materi_id = ? AND status_publikasi = 'publik' ORDER BY urutan ASC");
mysqli_stmt_bind_param($stmtSub, "i", $id);
mysqli_stmt_execute($stmtSub);
$submateriList = mysqli_stmt_get_result($stmtSub);

// Hitung jumlah soal untuk materi ini
$stmtSoal = mysqli_prepare($conn, "SELECT COUNT(*) AS jumlah FROM soal WHERE materi_id = ?");
mysqli_stmt_bind_param($stmtSoal, "i", $id);
mysqli_stmt_execute($stmtSoal);
$jumlahSoal = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtSoal))["jumlah"];
mysqli_stmt_close($stmtSoal);

$isVektor = ($materi["judul"] === "Vektor");
$pageTitle = $materi["judul"];

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main>
    <!-- Header materi -->
    <section class="border-b border-darktext/10 bg-white">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-12 sm:px-6 lg:grid-cols-2 lg:py-16">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-lavender px-3 py-1 text-xs font-semibold text-navy">
                        Kelas <?php echo $materi["kelas"]; ?>
                    </span>
                    <span class="inline-flex rounded-full bg-lightblue px-3 py-1 text-xs font-semibold text-navy">
                        <?php echo mysqli_num_rows($submateriList); ?> Submateri
                    </span>
                    <span class="inline-flex rounded-full bg-softyellow/40 px-3 py-1 text-xs font-semibold text-darktext">
                        <?php echo (int) $jumlahSoal; ?> Soal Latihan
                    </span>
                </div>

                <h1 class="mt-4 font-heading text-3xl font-bold text-navy sm:text-4xl">
                    <?php echo $materi["judul"]; ?>
                </h1>
                <p class="mt-3 max-w-lg leading-relaxed text-darktext/80">
                    <?php echo $materi["deskripsi"]; ?>
                </p>
                <p class="mt-4 text-sm text-darktext/60">
                    Pelajari submateri secara berurutan dari atas ke bawah supaya pemahamanmu bertahap.
                </p>
            </div>

            <div class="flex justify-center lg:justify-end">
                <?php if ($isVektor): ?>
                   <img src="assets/images/vektor-illustration.png">
                <?php else: ?>
                   <img src="assets/images/rotasi-illustration.png">
                <?php endif; ?>
            </div>
        </div>
    </section>

        <!-- Daftar submateri -->
    <section class="py-10 lg:py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6">
            <h2 class="font-heading text-xl font-bold text-navy">Daftar Submateri</h2>

            <div class="mt-5 flex flex-col gap-3">
                <?php if (mysqli_num_rows($submateriList) > 0): ?>
                    <?php
                    // Kumpulkan dulu semua baris supaya bisa dikelompokkan per bab
                    $submateriRows = [];
                    while ($row = mysqli_fetch_assoc($submateriList)) {
                        $submateriRows[] = $row;
                    }
                    $urutan = 1;
                    $babAktif = null;
                    $namaBab = [
                        "A" => "Konsep Vektor",
                        "B" => "Representasi Vektor",
                        "C" => "Operasi Vektor"
                    ];
                    ?>
                    <?php foreach ($submateriRows as $sub): ?>
                        <?php if (!empty($sub["bab"]) && $sub["bab"] !== $babAktif): ?>
                            <?php $babAktif = $sub["bab"]; ?>
                            <h3 class="mt-3 first:mt-0 font-heading text-xs font-bold uppercase tracking-wide text-blue sm:text-sm">
                                Bagian <?php echo $babAktif; ?> · <?php echo $namaBab[$babAktif] ?? "Bagian " . htmlspecialchars($babAktif); ?>
                            </h3>
                        <?php endif; ?>
                        <a href="submateri-detail.php?id=<?php echo $sub["id"]; ?>"
                            class="group flex items-center gap-3 rounded-2xl border border-darktext/10 bg-white p-3.5 transition-shadow hover:shadow-lg hover:shadow-navy/10 sm:gap-4 sm:p-4">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-lightblue font-heading text-sm font-bold text-navy sm:h-10 sm:w-10">
                                <?php echo $urutan++; ?>
                            </span>
                            <span class="flex-1 font-heading text-sm font-semibold text-darktext sm:text-base">
                                <?php echo $sub["judul"]; ?>
                            </span>
                            <svg class="shrink-0 text-darktext/40 transition-colors group-hover:text-navy" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="rounded-2xl border border-darktext/10 bg-white p-8 text-center text-darktext/60">
                        Submateri untuk materi ini belum tersedia.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Aksi lanjutan -->
            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-darktext/10 bg-lightblue p-5">
                    <h3 class="font-heading text-sm font-semibold text-navy">Latihan Soal</h3>
                    <p class="mt-1 text-sm text-darktext/70">
                        Uji pemahamanmu dengan <?php echo (int) $jumlahSoal; ?> soal latihan.
                    </p>
                    <span class="mt-3 inline-block rounded-full bg-navy/10 px-4 py-1.5 text-xs font-semibold text-navy/70">
                        Segera hadir
                    </span>
                </div>
                <div class="rounded-2xl border border-darktext/10 bg-lavender p-5">
                    <h3 class="font-heading text-sm font-semibold text-navy">Tanya PhyBot</h3>
                    <p class="mt-1 text-sm text-darktext/70">
                        Bingung dengan materi ini? Tanyakan langsung ke PhyBot.
                    </p>
                    <span class="mt-3 inline-block rounded-full bg-navy/10 px-4 py-1.5 text-xs font-semibold text-navy/70">
                        Segera hadir
                    </span>
                </div>
            </div>
        </div>
    </section>