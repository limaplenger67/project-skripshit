
<?php
$pageTitle = "Materi";

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

// Ambil semua materi beserta jumlah submateri dan soal
$query = "SELECT m.*,
    (SELECT COUNT(*) FROM submateri s
     WHERE s.materi_id = m.id
     AND s.status_publikasi = 'publik') AS jumlah_submateri,
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

<main class="overflow-hidden">

    <!-- HERO HEADER -->
    <section class="relative overflow-hidden bg-white">

        <!-- Dekorasi background -->
        <div class="pointer-events-none absolute -right-20 -top-32 h-[430px] w-[650px] rounded-full bg-lightblue/60 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-8 px-4 py-12 sm:px-6 lg:grid-cols-2 lg:gap-12 lg:py-16">

            <!-- Teks -->
            <div class="relative z-10">

                <span class="inline-flex items-center gap-2 rounded-full bg-lightblue px-4 py-2 text-sm font-semibold text-navy">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="19" height="19"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">
                        <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/>
                    </svg>
                    Platform Pembelajaran Fisika SMA
                </span>

                <h1 class="mt-5 font-heading text-4xl font-bold leading-tight tracking-tight text-navy sm:text-5xl lg:text-6xl">
                    Pilih Materimu
                </h1>

                <p class="mt-4 max-w-2xl text-base leading-relaxed text-darktext/80 sm:text-lg">
                    Setiap materi disusun bertahap, mulai dari konsep dasar hingga latihan soal, supaya kamu bisa belajar dengan alur yang jelas.
                </p>

            </div>

            <!-- Ilustrasi Hero -->
            <div class="relative hidden h-60 items-center justify-center lg:flex">

                <div class="absolute right-0 top-0 h-64 w-64 rounded-full bg-blue-100/70 blur-2xl"></div>

                <svg viewBox="0 0 440 260"
                     class="relative z-10 h-full w-full"
                     xmlns="http://www.w3.org/2000/svg">

                    <!-- Atom -->
                    <g transform="translate(205 115)">
                        <ellipse rx="78" ry="30"
                                 fill="none"
                                 stroke="#76A9F5"
                                 stroke-width="5"
                                 transform="rotate(35)"/>

                        <ellipse rx="78" ry="30"
                                 fill="none"
                                 stroke="#76A9F5"
                                 stroke-width="5"
                                 transform="rotate(-35)"/>

                        <ellipse rx="78" ry="30"
                                 fill="none"
                                 stroke="#A4C8FF"
                                 stroke-width="5"/>

                        <circle r="15" fill="#3979D5"/>
                        <circle cx="75" cy="0" r="7" fill="#76A9F5"/>
                    </g>

                    <!-- Buku -->
                    <g transform="translate(305 160)">
                        <path d="M0 15 L105 0 L130 15 L25 32 Z"
                              fill="#8DB7F3"/>

                        <path d="M25 32 L130 15 L130 43 L25 60 Z"
                              fill="#24549A"/>

                        <path d="M0 15 L25 32 L25 60 L0 42 Z"
                              fill="#D7E8FF"/>

                        <path d="M10 50 L112 35 L138 50 L35 68 Z"
                              fill="#DCEBFC"/>

                        <path d="M35 68 L138 50 L138 66 L35 85 Z"
                              fill="#6B94CC"/>

                        <path d="M10 50 L35 68 L35 85 L10 67 Z"
                              fill="#FFFFFF"/>
                    </g>

                    <circle cx="70" cy="65" r="22" fill="#C5DCFF"/>
                    <circle cx="355" cy="45" r="8" fill="#B4D1FA"/>
                    <circle cx="110" cy="205" r="7" fill="#D2E4FF"/>

                </svg>

            </div>

        </div>
    </section>


    <!-- DAFTAR MATERI -->
    <section class="bg-gradient-to-b from-white to-[#F3F7FF] py-10 sm:py-14">

        <div class="mx-auto max-w-6xl px-4 sm:px-6">

            <div class="grid gap-6 md:grid-cols-2">

                <?php if ($materiList && mysqli_num_rows($materiList) > 0): ?>

                    <?php while ($materi = mysqli_fetch_assoc($materiList)): ?>

                        <?php
                        $judul = $materi["judul"];
                        $isVektor = (stripos($judul, "Vektor") !== false);
                        ?>

                        <article class="group flex flex-col overflow-hidden rounded-3xl border border-blue-100 bg-white p-3 shadow-[0_8px_25px_rgba(23,58,112,0.06)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_15px_35px_rgba(23,58,112,0.12)]">

                            <!-- GAMBAR MATERI -->
                            <div class="relative h-[230px] overflow-hidden rounded-2xl bg-[#E5EEFF] sm:h-[270px]">

                                <?php if ($isVektor): ?>

                                    <img
                                        src="assets/images/vektor.png"
                                        alt="Ilustrasi realistis materi vektor"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                    >

                                    <!-- Fallback jika gambar belum tersedia -->
                                    <div class="hidden h-full w-full items-center justify-center">
                                        <svg viewBox="0 0 500 280"
                                             class="h-full w-full"
                                             xmlns="http://www.w3.org/2000/svg">

                                            <rect width="500" height="280" fill="#E2EDFF"/>

                                            <path d="M80 220 L430 220 M80 220 L80 35"
                                                  stroke="#173A70"
                                                  stroke-width="5"
                                                  fill="none"/>

                                            <path d="M430 220 l-16 -9 v18 Z"
                                                  fill="#173A70"/>

                                            <path d="M80 35 l-9 16 h18 Z"
                                                  fill="#173A70"/>

                                            <path d="M80 220 L280 70"
                                                  stroke="#397FDE"
                                                  stroke-width="9"
                                                  stroke-linecap="round"/>

                                            <path d="M280 70 l-27 5 16 -24 Z"
                                                  fill="#397FDE"/>

                                            <path d="M80 220 L230 155"
                                                  stroke="#F5BD43"
                                                  stroke-width="9"
                                                  stroke-linecap="round"/>

                                            <path d="M230 155 l-27 -2 17 -18 Z"
                                                  fill="#F5BD43"/>

                                            <path d="M80 220 L355 125"
                                                  stroke="#173A70"
                                                  stroke-width="7"
                                                  stroke-dasharray="14 12"
                                                  fill="none"
                                                  stroke-linecap="round"/>

                                            <g font-family="Georgia" font-style="italic"
                                               font-size="30">
                                                <text x="292" y="60" fill="#397FDE">A</text>
                                                <text x="240" y="150" fill="#C18C23">B</text>
                                                <text x="370" y="115" fill="#173A70">R</text>
                                                <text x="445" y="235" fill="#173A70">x</text>
                                                <text x="60" y="28" fill="#173A70">y</text>
                                            </g>

                                        </svg>
                                    </div>

                                <?php else: ?>

                                    <img
                                        src="assets/images/dinamika-rotasi.png"
                                        alt="Ilustrasi realistis materi dinamika rotasi"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                    >

                                    <!-- Fallback -->
                                    <div class="hidden h-full w-full items-center justify-center">

                                        <svg viewBox="0 0 500 280"
                                             class="h-full w-full"
                                             xmlns="http://www.w3.org/2000/svg">

                                            <rect width="500" height="280" fill="#E9E9FF"/>

                                            <ellipse cx="245" cy="155"
                                                     rx="130" ry="75"
                                                     fill="#548DD6"/>

                                            <ellipse cx="245" cy="155"
                                                     rx="130" ry="75"
                                                     stroke="#173A70"
                                                     stroke-width="5"
                                                     fill="none"/>

                                            <ellipse cx="245" cy="155"
                                                     rx="35" ry="25"
                                                     fill="#173A70"/>

                                            <path d="M110 95 Q145 30 235 35"
                                                  stroke="#F5BD43"
                                                  stroke-width="9"
                                                  fill="none"
                                                  stroke-linecap="round"/>

                                            <path d="M235 35 l-20 -12 5 25 Z"
                                                  fill="#F5BD43"/>

                                            <path d="M245 155 L365 75"
                                                  stroke="#173A70"
                                                  stroke-width="7"/>

                                            <path d="M365 75 l-26 4 14 -23 Z"
                                                  fill="#173A70"/>

                                            <text x="380" y="70"
                                                  font-size="30"
                                                  fill="#173A70"
                                                  font-family="Georgia"
                                                  font-style="italic">F</text>

                                            <text x="315" y="235"
                                                  font-size="30"
                                                  fill="#173A70"
                                                  font-family="Georgia"
                                                  font-style="italic">τ = r × F</text>

                                        </svg>

                                    </div>

                                <?php endif; ?>

                            </div>


                            <!-- INFORMASI MATERI -->
                            <div class="flex flex-1 flex-col px-3 pb-3 pt-4 sm:px-4">

                                <div class="flex flex-wrap items-center gap-2">

                                    <span class="rounded-full bg-lavender px-3 py-1 text-xs font-semibold text-navy">
                                        Kelas <?php echo htmlspecialchars($materi["kelas"]); ?>
                                    </span>

                                    <span class="rounded-full bg-lightblue px-3 py-1 text-xs font-semibold text-navy">
                                        <?php echo (int)$materi["jumlah_submateri"]; ?> Submateri
                                    </span>

                                    <span class="rounded-full bg-softyellow/50 px-3 py-1 text-xs font-semibold text-darktext">
                                        <?php echo (int)$materi["jumlah_soal"]; ?> Soal
                                    </span>

                                </div>


                                <h2 class="mt-4 font-heading text-2xl font-bold text-navy">
                                    <?php echo htmlspecialchars($materi["judul"]); ?>
                                </h2>


                                <p class="mt-2 flex-1 text-base leading-relaxed text-darktext/75">
                                    <?php echo htmlspecialchars($materi["deskripsi"]); ?>
                                </p>


                                <a
                                    href="materi-detail.php?id=<?php echo (int)$materi["id"]; ?>"
                                    class="mt-5 inline-flex w-fit items-center gap-3 rounded-full bg-navy px-7 py-3 text-base font-semibold text-white transition-all duration-300 hover:bg-blue hover:shadow-lg hover:shadow-blue/20"
                                >

                                    Masuk Materi

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         width="18"
                                         height="18"
                                         viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="2"
                                         stroke-linecap="round"
                                         stroke-linejoin="round">

                                        <path d="M5 12h14"/>
                                        <path d="m12 5 7 7-7 7"/>

                                    </svg>

                                </a>

                            </div>

                        </article>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="col-span-full rounded-3xl border border-blue-100 bg-white p-12 text-center">

                        <h2 class="font-heading text-xl font-bold text-navy">
                            Belum Ada Materi
                        </h2>

                        <p class="mt-2 text-darktext/60">
                            Belum ada materi yang dipublikasikan.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>

</main>

<?php require_once "includes/footer.php"; ?>

</body>
</html>