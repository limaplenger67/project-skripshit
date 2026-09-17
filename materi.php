<?php
$pageTitle = "Materi";
require_once "config/database.php";
require_once "includes/auth.php";
requireLogin();

// Ambil semua materi yang sudah dipublikasikan beserta jumlah submaterinya
$query = "SELECT m.*, 
                 (SELECT COUNT(*) FROM submateri s 
                  WHERE s.materi_id = m.id AND s.status_publikasi = 'publik') AS jumlah_submateri,
                 (SELECT COUNT(*) FROM soal so 
                  WHERE so.materi_id = m.id) AS jumlah_soal
          FROM materi m
          WHERE m.status_publikasi = 'publik'
          ORDER BY m.id ASC";
$materiList = mysqli_query($conn, $query);

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main>
    <!-- Header halaman -->
    <section class="border-b border-darktext/10 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:py-20">
            <span class="inline-flex items-center gap-2 rounded-full bg-lightblue px-4 py-1.5 text-xs font-semibold text-navy">
                Katalog Materi
            </span>
            <h1 class="mt-4 font-heading text-3xl font-bold text-navy sm:text-4xl">
                Pilih Materi yang Mau Kamu Pelajari
            </h1>
            <p class="mt-3 max-w-xl text-darktext/80">
                Setiap materi terdiri dari submateri bertahap, visualisasi, dan latihan soal. Pilih satu dan mulai belajar.
            </p>
        </div>
    </section>

    <!-- Daftar materi -->
    <section class="py-14 lg:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="grid gap-6 md:grid-cols-2">

                <?php if (mysqli_num_rows($materiList) > 0): ?>
                    <?php while ($materi = mysqli_fetch_assoc($materiList)): ?>
                        <?php $isVektor = ($materi['judul'] === 'Vektor'); ?>
                        <article class="group flex flex-col rounded-2xl border border-darktext/10 bg-white p-6 transition-shadow hover:shadow-lg hover:shadow-navy/10">

                            <?php if ($isVektor): ?>
                                <!-- Ilustrasi Vektor -->
                                <svg viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-auto w-full" aria-hidden="true">
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
                                <!-- Ilustrasi Dinamika Rotasi -->
                                <svg viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-auto w-full" aria-hidden="true">
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

                            <div class="mt-5 flex items-center gap-2">
                                <span class="inline-flex rounded-full bg-lavender px-3 py-1 text-xs font-semibold text-navy">
                                    Kelas <?php echo $materi['kelas']; ?>
                                </span>
                                <span class="inline-flex rounded-full bg-lightblue px-3 py-1 text-xs font-semibold text-navy">
                                    <?php echo (int) $materi['jumlah_submateri']; ?> Submateri
                                </span>
                                <span class="inline-flex rounded-full bg-softyellow/40 px-3 py-1 text-xs font-semibold text-darktext">
                                    <?php echo (int) $materi['jumlah_soal']; ?> Soal
                                </span>
                            </div>

                            <h2 class="mt-3 font-heading text-xl font-semibold text-navy">
                                <?php echo $materi['judul']; ?>
                            </h2>
                            <p class="mt-2 flex-1 text-sm leading-relaxed text-darktext/75">
                                <?php echo $materi['deskripsi']; ?>
                            </p>

                            <a href="materi-detail.php?id=<?php echo $materi['id']; ?>"
                                class="mt-5 inline-flex w-fit items-center gap-2 rounded-full bg-navy px-5 py-2.5 text-sm font-semibold text-warmwhite transition-colors group-hover:bg-blue">
                                Masuk Materi
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </a>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full rounded-2xl border border-darktext/10 bg-white p-10 text-center text-darktext/60">
                        Belum ada materi yang dipublikasikan.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
</main>

<?php require_once "includes/footer.php"; ?>