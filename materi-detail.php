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
                    <svg viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-auto w-full max-w-sm" aria-hidden="true">
                        <rect x="8" y="8" width="184" height="134" rx="16" fill="#DCEEFF"/>
                        <line x1="40" y1="115" x2="175" y2="115" stroke="#173A70" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M175 115l-9-5v10l9-5Z" fill="#173A70"/>
                        <line x1="40" y1="115" x2="40" y2="25" stroke="#173A70" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M40 25l-5 9h10l-5-9Z" fill="#173A70"/>
                        <text x="180" y="128" fill="#173A70" font-size="13" font-style="italic" font-family="Georgia, serif">x</text>
                        <text x="30" y="22" fill="#173A70" font-size="13" font-style="italic" font-family="Georgia, serif">y</text>
                        <line x1="40" y1="115" x2="120" y2="55" stroke="#3F7FD9" stroke-width="4" stroke-linecap="round"/>
                        <path d="M120 55l-13 2 7-11 6 9Z" fill="#3F7FD9"/>
                        <text x="126" y="50" fill="#3F7FD9" font-size="16" font-style="italic" font-family="Georgia, serif">A</text>
                        <line x1="40" y1="115" x2="90" y2="95" stroke="#F8C95B" stroke-width="4" stroke-linecap="round"/>
                        <path d="M90 95l-13 4 9-10 4 6Z" fill="#F8C95B"/>
                        <text x="97" y="92" fill="#C79A2E" font-size="16" font-style="italic" font-family="Georgia, serif">B</text>
                        <line x1="40" y1="115" x2="155" y2="85" stroke="#243247" stroke-width="3" stroke-dasharray="6 5" stroke-linecap="round"/>
                        <text x="148" y="75" fill="#243247" font-size="14" font-style="italic" font-family="Georgia, serif">R</text>
                    </svg>
                <?php else: ?>
                    <svg viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-auto w-full max-w-sm" aria-hidden="true">
                        <rect x="8" y="8" width="184" height="134" rx="16" fill="#E9E4FF"/>
                        <ellipse cx="95" cy="80" rx="48" ry="38" fill="#3F7FD9"/>
                        <ellipse cx="95" cy="80" rx="48" ry="38" stroke="#173A70" stroke-width="2.5" fill="none"/>
                        <ellipse cx="95" cy="80" rx="14" ry="11" fill="#173A70"/>
                        <ellipse cx="95" cy="80" rx="6" ry="5" fill="#F8C95B"/>
                        <path d="M40 55a55 42 0 0 1 30-24" stroke="#F8C95B" stroke-width="4" stroke-linecap="round" fill="none"/>
                        <path d="M70 31l-12 1 7-11 5 10Z" fill="#F8C95B"/>
                        <line x1="95" y1="80" x2="160" y2="40" stroke="#173A70" stroke-width="3.5" stroke-linecap="round"/>
                        <path d="M160 40l-13 1 8-11 5 10Z" fill="#173A70"/>
                        <text x="166" y="36" fill="#173A70" font-size="16" font-style="italic" font-family="Georgia, serif">F</text>
                        <text x="125" y="120" fill="#173A70" font-size="15" font-style="italic" font-family="Georgia, serif">τ = r × F</text>
                    </svg>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Daftar submateri -->
    <section class="py-12 lg:py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6">
            <h2 class="font-heading text-xl font-bold text-navy">Daftar Submateri</h2>

            <div class="mt-6 flex flex-col gap-3">
                <?php if (mysqli_num_rows($submateriList) > 0): ?>
                    <?php $urutan = 1; ?>
                    <?php while ($sub = mysqli_fetch_assoc($submateriList)): ?>
                        <a href="submateri-detail.php?id=<?php echo $sub["id"]; ?>"
                            class="group flex items-center gap-4 rounded-2xl border border-darktext/10 bg-white p-4 transition-shadow hover:shadow-lg hover:shadow-navy/10">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-lightblue font-heading text-sm font-bold text-navy">
                                <?php echo $urutan++; ?>
                            </span>
                            <span class="flex-1 font-heading text-sm font-semibold text-darktext sm:text-base">
                                <?php echo $sub["judul"]; ?>
                            </span>
                            <svg class="shrink-0 text-darktext/40 transition-colors group-hover:text-navy" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="rounded-2xl border border-darktext/10 bg-white p-8 text-center text-darktext/60">
                        Submateri untuk materi ini belum tersedia.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Aksi lanjutan -->
            <div class="mt-10 grid gap-4 sm:grid-cols-2">
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
</main>

<?php require_once "includes/footer.php"; ?>