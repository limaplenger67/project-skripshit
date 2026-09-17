<?php
$pageTitle = "Belajar Fisika Jadi Lebih Mudah";
require_once "config/database.php";
require_once "includes/auth.php";

// Ambil data materi dari database (nanti dipakai lagi di halaman materi)
$materiList = mysqli_query($conn, "SELECT * FROM materi WHERE status_publikasi = 'publik' ORDER BY id ASC");

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main>
    <!-- HERO SECTION -->
    <section id="beranda" class="bg-warmwhite">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 pb-16 pt-12 sm:px-6 lg:grid-cols-2 lg:pb-24 lg:pt-20">
            <div class="animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full bg-lightblue px-4 py-1.5 text-xs font-semibold text-navy">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3Z"/></svg>
                    Platform Pembelajaran Fisika SMA
                </span>

                <h1 class="mt-5 font-heading text-4xl font-bold leading-tight text-navy sm:text-5xl">
                    Belajar Fisika Jadi Lebih Mudah
                </h1>

                <p class="mt-5 max-w-lg text-base leading-relaxed text-darktext/80 sm:text-lg">
                    PhyZone membantu kamu memahami konsep fisika melalui materi yang terstruktur, visualisasi interaktif, latihan soal, dan pendamping AI yang siap menjawab pertanyaanmu.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="materi.php" class="inline-flex items-center gap-2 rounded-full bg-navy px-7 py-3 text-sm font-semibold text-warmwhite transition-colors hover:bg-blue">
                        Mulai Belajar
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                    <a href="#chat.php" class="inline-flex items-center gap-2 rounded-full border-2 border-navy/20 px-7 py-3 text-sm font-semibold text-navy transition-colors hover:border-navy">
                        Jelajahi PhyZone
                    </a>
                </div>
            </div>

            <div class="animate-fade-up-delay flex justify-center lg:justify-end">
                <svg viewBox="0 0 420 360" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-auto w-full max-w-md" aria-hidden="true">
                    <path d="M52 210C30 130 90 45 180 40c80-4 150 40 170 115 20 78-25 155-105 175-75 19-172-20-193-120Z" fill="#173A70"/>
                    <path d="M120 70l4 10 10 4-10 4-4 10-4-10-10-4 10-4 4-10Z" fill="#F8C95B"/>
                    <path d="M300 55l3 7 7 3-7 3-3 7-3-7-7-3 7-3 3-7Z" fill="#DCEEFF"/>
                    <circle cx="250" cy="300" r="3" fill="#F8C95B"/>
                    <circle cx="90" cy="160" r="2.5" fill="#E9E4FF"/>
                    <circle cx="330" cy="180" r="2.5" fill="#F8C95B"/>
                    <circle cx="110" cy="105" r="22" fill="#3F7FD9"/>
                    <ellipse cx="110" cy="105" rx="40" ry="11" stroke="#F8C95B" stroke-width="4" fill="none" transform="rotate(-20 110 105)"/>
                    <ellipse cx="245" cy="180" rx="80" ry="62" fill="#3F7FD9"/>
                    <ellipse cx="245" cy="180" rx="80" ry="62" stroke="#DCEEFF" stroke-width="3" fill="none"/>
                    <ellipse cx="245" cy="180" rx="56" ry="42" fill="#DCEEFF" opacity="0.55"/>
                    <ellipse cx="245" cy="180" rx="24" ry="18" fill="#173A70"/>
                    <ellipse cx="245" cy="180" rx="10" ry="7" fill="#F8C95B"/>
                    <path d="M150 120a90 70 0 0 1 60-32" stroke="#F8C95B" stroke-width="4" stroke-linecap="round" fill="none"/>
                    <path d="M335 240a90 70 0 0 1-60 32" stroke="#F8C95B" stroke-width="4" stroke-linecap="round" fill="none"/>
                    <path d="M352 150l10 4-4 10" stroke="#F8C95B" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <line x1="245" y1="180" x2="330" y2="95" stroke="#F8C95B" stroke-width="4" stroke-linecap="round"/>
                    <path d="M330 95l-14 2 8-12 6 10Z" fill="#F8C95B"/>
                    <line x1="245" y1="180" x2="150" y2="255" stroke="#E9E4FF" stroke-width="4" stroke-linecap="round"/>
                    <path d="M150 255l14-2-8 12-6-10Z" fill="#E9E4FF"/>
                    <text x="340" y="85" fill="#DCEEFF" font-size="20" font-style="italic" font-family="Georgia, serif">F</text>
                    <text x="130" y="282" fill="#DCEEFF" font-size="20" font-style="italic" font-family="Georgia, serif">r</text>
                    <g transform="rotate(-8 150 300)">
                        <rect x="100" y="285" width="100" height="34" rx="8" fill="#FAF9F6"/>
                        <text x="150" y="308" text-anchor="middle" fill="#173A70" font-size="17" font-style="italic" font-family="Georgia, serif">τ = r × F</text>
                    </g>
                    <g transform="translate(150 315)">
                        <path d="M0 10 Q30 -2 60 10 L60 34 Q30 22 0 34 Z" fill="#DCEEFF"/>
                        <path d="M60 10 Q90 -2 120 10 L120 34 Q90 22 60 34 Z" fill="#F8C95B"/>
                        <path d="M0 10 Q30 -2 60 10 Q90 -2 120 10" stroke="#173A70" stroke-width="3" fill="none"/>
                    </g>
                </svg>
            </div>
        </div>
    </section>

    <!-- PILIH MATERI -->
    <section id="materi" class="bg-white py-16 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="max-w-xl">
                <h2 class="font-heading text-2xl font-bold text-navy sm:text-3xl">Pilih Materimu</h2>
                <p class="mt-3 text-darktext/80">
                    Setiap materi disusun bertahap, mulai dari konsep dasar hingga latihan soal, supaya kamu bisa belajar dengan alur yang jelas.
                </p>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2">
                <?php while ($materi = mysqli_fetch_assoc($materiList)): ?>
                    <?php $isVektor = ($materi['judul'] === 'Vektor'); ?>
                    <article class="group flex flex-col rounded-2xl border border-darktext/10 bg-warmwhite p-6 transition-shadow hover:shadow-lg hover:shadow-navy/10">
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

                        <span class="mt-5 inline-flex w-fit rounded-full bg-lavender px-3 py-1 text-xs font-semibold text-navy">
                            Kelas <?php echo $materi['kelas']; ?>
                        </span>
                        <h3 class="mt-3 font-heading text-xl font-semibold text-navy">
                            <?php echo $materi['judul']; ?>
                        </h3>
                        <p class="mt-2 flex-1 text-sm leading-relaxed text-darktext/75">
                            <?php echo $materi['deskripsi']; ?>
                        </p>
                        <a href="materi-detail.php?id=<?php echo $materi['id']; ?>" class="mt-5 inline-flex w-fit items-center gap-2 rounded-full bg-navy px-5 py-2.5 text-sm font-semibold text-warmwhite transition-colors group-hover:bg-blue">
                            Masuk Materi
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- KEUNGGULAN PHYZONE -->
    <section class="bg-warmwhite py-16 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-xl text-center">
                <h2 class="font-heading text-2xl font-bold text-navy sm:text-3xl">Kenapa PhyZone?</h2>
                <p class="mt-3 text-darktext/80">Semua yang kamu butuhkan untuk belajar fisika ada di satu tempat.</p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <article class="rounded-2xl border border-darktext/10 bg-white p-6 text-center transition-shadow hover:shadow-lg hover:shadow-navy/10">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-lightblue text-navy">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1h3a5 5 0 0 1 5 5 5 5 0 0 1 5-5h3a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-4a3 3 0 0 0-6 0v4a1 1 0 0 1-1 1Z"/></svg>
                    </span>
                    <h3 class="mt-4 font-heading text-base font-semibold text-navy">Materi Terstruktur</h3>
                    <p class="mt-2 text-sm leading-relaxed text-darktext/75">Materi disusun secara bertahap agar kamu lebih mudah mengikuti pembelajaran.</p>
                </article>

                <article class="rounded-2xl border border-darktext/10 bg-white p-6 text-center transition-shadow hover:shadow-lg hover:shadow-navy/10">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-lightblue text-navy">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m10 7 5 5-5 5"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                    </span>
                    <h3 class="mt-4 font-heading text-base font-semibold text-navy">Visualisasi Fisika</h3>
                    <p class="mt-2 text-sm leading-relaxed text-darktext/75">Konsep fisika diperkuat melalui ilustrasi dan visualisasi yang mudah dipahami.</p>
                </article>

                <article class="rounded-2xl border border-darktext/10 bg-white p-6 text-center transition-shadow hover:shadow-lg hover:shadow-navy/10">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-lightblue text-navy">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>
                    </span>
                    <h3 class="mt-4 font-heading text-base font-semibold text-navy">Chatbot AI</h3>
                    <p class="mt-2 text-sm leading-relaxed text-darktext/75">Bertanyalah mengenai materi yang sedang kamu pelajari, PhyBot siap membantu.</p>
                </article>

                <article class="rounded-2xl border border-darktext/10 bg-white p-6 text-center transition-shadow hover:shadow-lg hover:shadow-navy/10">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-lightblue text-navy">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </span>
                    <h3 class="mt-4 font-heading text-base font-semibold text-navy">Latihan Soal</h3>
                    <p class="mt-2 text-sm leading-relaxed text-darktext/75">Uji pemahamanmu setelah mempelajari materi dengan soal latihan yang tersedia.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- PREVIEW CHATBOT -->
    <section id="chat-ai" class="bg-white py-16 lg:py-24">
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-lavender px-4 py-1.5 text-xs font-semibold text-navy">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>
                    PhyBot, Asisten Belajarmu
                </span>
                <h2 class="mt-5 font-heading text-2xl font-bold text-navy sm:text-3xl">Belajar Tidak Harus Sendiri</h2>
                <p class="mt-4 leading-relaxed text-darktext/80">
                    PhyBot membantu menjawab pertanyaan yang berkaitan dengan materi yang sedang kamu pelajari. Bingung dengan suatu konsep? Langsung tanyakan saja.
                </p>
                <p class="mt-4 flex items-start gap-2 rounded-xl bg-softyellow/25 p-4 text-sm leading-relaxed text-darktext/80">
                    <svg class="mt-0.5 shrink-0 text-navy" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
                    PhyBot berfokus pada materi pembelajaran yang tersedia di PhyZone, sehingga jawabannya selalu sesuai dengan materi yang kamu pelajari.
                </p>
            </div>

            <div class="rounded-2xl border border-darktext/10 bg-warmwhite p-5 shadow-lg shadow-navy/10">
                <div class="flex items-center gap-3 border-b border-darktext/10 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-navy text-warmwhite">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>
                    </span>
                    <div>
                        <p class="font-heading text-sm font-semibold text-navy">PhyBot</p>
                        <p class="text-xs text-darktext/60">Pendamping belajarmu</p>
                    </div>
                </div>

                <div class="flex flex-col gap-4 py-5">
                    <div class="flex justify-end">
                        <div class="max-w-[80%] rounded-2xl rounded-br-sm bg-navy px-4 py-3 text-sm leading-relaxed text-warmwhite">
                            Kenapa benda bisa mengalami torsi?
                        </div>
                    </div>
                    <div class="flex justify-start">
                        <div class="max-w-[85%] rounded-2xl rounded-bl-sm bg-lightblue px-4 py-3 text-sm leading-relaxed text-darktext">
                            Torsi muncul ketika gaya bekerja pada suatu benda pada jarak tertentu dari sumbu rotasi. Besarnya dipengaruhi oleh besar gaya, jarak terhadap sumbu, dan sudut antara gaya dengan lengan gayanya.
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 rounded-full border border-darktext/15 bg-white px-4 py-2.5">
                    <input type="text" placeholder="Tulis pertanyaanmu di sini..." disabled class="w-full bg-transparent text-sm text-darktext outline-none placeholder:text-darktext/40" />
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-navy text-warmwhite">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-center text-xs text-darktext/50">
                    Ini adalah tampilan preview. PhyBot akan aktif pada tahap pengembangan berikutnya.
                </p>
            </div>
        </div>
    </section>

    <!-- PENGEMBANG -->
    <section id="tentang" class="bg-warmwhite py-16 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-xl text-center">
                <h2 class="font-heading text-2xl font-bold text-navy sm:text-3xl">PhyZone Dikembangkan Oleh</h2>
                <p class="mt-3 text-darktext/80">Dua mahasiswa yang ingin membuat belajar fisika terasa lebih mudah dan menyenangkan.</p>
            </div>

            <div class="mx-auto mt-10 grid max-w-2xl gap-6 sm:grid-cols-2">
                <article class="flex items-center gap-4 rounded-2xl border border-darktext/10 bg-white p-5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-lavender text-navy">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21a8 8 0 0 1 13.3-6"/><circle cx="10" cy="8" r="5"/><path d="m17 17 4 4"/></svg>
                    </span>
                    <p class="font-heading text-base font-semibold text-navy">Melinda Amru Claudia</p>
                </article>
                <article class="flex items-center gap-4 rounded-2xl border border-darktext/10 bg-white p-5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-lavender text-navy">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21a8 8 0 0 1 13.3-6"/><circle cx="10" cy="8" r="5"/><path d="m17 17 4 4"/></svg>
                    </span>
                    <p class="font-heading text-base font-semibold text-navy">Putri Suci Ramadani</p>
                </article>
            </div>
        </div>
    </section>
</main>

<?php require_once "includes/footer.php"; ?>