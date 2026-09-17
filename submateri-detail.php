<?php
$pageTitle = "Submateri";
require_once "config/database.php";
require_once "includes/auth.php";
requireLogin();

// Validasi parameter id
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: materi.php");
    exit;
}

$id = (int) $_GET["id"];

// Ambil data submateri beserta data materi induknya
$stmt = mysqli_prepare($conn, "SELECT s.*, m.judul AS materi_judul, m.kelas, m.id AS materi_id
                               FROM submateri s
                               JOIN materi m ON s.materi_id = m.id
                               WHERE s.id = ? AND s.status_publikasi = 'publik'");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$submateri = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$submateri) {
    header("Location: materi.php");
    exit;
}

$materiId = (int) $submateri["materi_id"];

// Ambil submateri sebelumnya (untuk tombol navigasi)
$stmtPrev = mysqli_prepare($conn, "SELECT id, judul FROM submateri
                                   WHERE materi_id = ? AND status_publikasi = 'publik' AND urutan < ?
                                   ORDER BY urutan DESC LIMIT 1");
mysqli_stmt_bind_param($stmtPrev, "ii", $materiId, $submateri["urutan"]);
mysqli_stmt_execute($stmtPrev);
$prevSub = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtPrev));
mysqli_stmt_close($stmtPrev);

// Ambil submateri berikutnya
$stmtNext = mysqli_prepare($conn, "SELECT id, judul FROM submateri
                                   WHERE materi_id = ? AND status_publikasi = 'publik' AND urutan > ?
                                   ORDER BY urutan ASC LIMIT 1");
mysqli_stmt_bind_param($stmtNext, "ii", $materiId, $submateri["urutan"]);
mysqli_stmt_execute($stmtNext);
$nextSub = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtNext));
mysqli_stmt_close($stmtNext);

$pageTitle = $submateri["judul"];

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:py-14">
    <!-- Breadcrumb -->
    <nav class="text-sm text-darktext/60" aria-label="Breadcrumb">
        <a href="materi.php" class="hover:text-navy">Materi</a>
        <span class="mx-1">/</span>
        <a href="materi-detail.php?id=<?php echo $materiId; ?>" class="hover:text-navy">
            <?php echo $submateri["materi_judul"]; ?>
        </a>
    </nav>

    <!-- Judul -->
    <div class="mt-5">
        <span class="inline-flex rounded-full bg-lavender px-3 py-1 text-xs font-semibold text-navy">
            Kelas <?php echo $submateri["kelas"]; ?> · <?php echo $submateri["materi_judul"]; ?>
        </span>
        <h1 class="mt-3 font-heading text-2xl font-bold text-navy sm:text-3xl">
            <?php echo $submateri["judul"]; ?>
        </h1>
    </div>

    <!-- Isi submateri -->
    <article class="mt-8 rounded-2xl border border-darktext/10 bg-white p-6 sm:p-8">
        <?php if ($submateri["isi"]): ?>
            <div class="space-y-4 leading-relaxed text-darktext/90">
                <?php foreach (explode("\n", trim($submateri["isi"])) as $paragraf): ?>
                    <?php if (trim($paragraf) !== ""): ?>
                        <p><?php echo nl2br(htmlspecialchars(trim($paragraf))); ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-darktext/60">Isi submateri ini belum tersedia.</p>
        <?php endif; ?>
    </article>

    <!-- Navigasi sebelumnya / berikutnya -->
    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-between">
        <?php if ($prevSub): ?>
            <a href="submateri-detail.php?id=<?php echo $prevSub["id"]; ?>"
                class="group flex items-center gap-2 rounded-full border-2 border-darktext/15 px-5 py-2.5 text-sm font-semibold text-darktext transition-colors hover:border-navy hover:text-navy">
                <svg class="transition-transform group-hover:-translate-x-0.5" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                <span class="max-w-[200px] truncate"><?php echo $prevSub["judul"]; ?></span>
            </a>
        <?php else: ?>
            <span class="invisible"></span>
        <?php endif; ?>

        <?php if ($nextSub): ?>
            <a href="submateri-detail.php?id=<?php echo $nextSub["id"]; ?>"
                class="group flex items-center justify-end gap-2 rounded-full bg-navy px-5 py-2.5 text-sm font-semibold text-warmwhite transition-colors hover:bg-blue">
                <span class="max-w-[200px] truncate"><?php echo $nextSub["judul"]; ?></span>
                <svg class="transition-transform group-hover:translate-x-0.5" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        <?php endif; ?>
    </div>
</main>

<?php require_once "includes/footer.php"; ?>