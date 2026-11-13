<?php
$pageTitle = "Belajar Fisika Jadi Lebih Mudah";
require_once "config/database.php";
require_once "includes/auth.php";

$materiList = mysqli_query(
    $conn,
    "SELECT * FROM materi WHERE status_publikasi = 'publik' ORDER BY id ASC"
);

require_once "includes/head.php";
?>

<body class="min-h-screen bg-[#F7F9FD] font-body text-[#243247]">
<?php require_once "includes/navbar.php"; ?>

<main>
    <!-- HERO / PILIH MATERI -->
    <section id="beranda" class="relative isolate overflow-hidden bg-gradient-to-b from-white via-[#F8FAFF] to-[#F3F7FF]">
        <div class="pointer-events-none absolute -right-16 -top-12 -z-10 hidden w-[55%] max-w-[850px] opacity-90 lg:block">
            <img
                src="assets/images/phyzone-hero-decoration.png"
                alt=""
                class="h-auto w-full object-contain"
                aria-hidden="true"
            >
        </div>

        <div class="mx-auto max-w-[1460px] px-4 pb-12 pt-10 sm:px-6 sm:pb-16 sm:pt-14 lg:pb-16">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-[#D8E6FF] bg-[#EAF2FF] px-4 py-2 text-xs font-semibold text-[#173A70] sm:text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m12 3 1.9 5.8L20 11l-6.1 2.2L12 19l-1.9-5.8L4 11l6.1-2.2L12 3Z"/>
                    </svg>
                    Platform Pembelajaran Fisika SMA
                </span>

                <h1 class="mt-5 font-heading text-4xl font-bold leading-[1.12] tracking-tight text-[#173A70] sm:text-5xl lg:text-6xl">
                    Pilih Materimu
                </h1>
                <p class="mt-4 max-w-xl text-sm leading-7 text-[#536783] sm:text-base">
                    Setiap materi disusun bertahap, mulai dari konsep dasar hingga latihan soal,
                    supaya kamu bisa belajar dengan alur yang jelas.
                </p>
            </div>

            <!-- KARTU MATERI -->
            <div id="materi" class="relative z-10 mt-8 grid gap-5 md:grid-cols-2 lg:mt-10 lg:gap-6">
                <?php if ($materiList && mysqli_num_rows($materiList) > 0): ?>
                    <?php while ($materi = mysqli_fetch_assoc($materiList)): ?>
                        <?php
                            $judulMateri = trim($materi["judul"] ?? "");
                            $isVektor = mb_strtolower($judulMateri, "UTF-8") === "vektor";
                            $gambarMateri = $isVektor
                                ? "assets/images/vektor-illustration.png"
                                : "assets/images/rotasi-illustration.png";
                            $kelas = htmlspecialchars((string)($materi["kelas"] ?? ""), ENT_QUOTES, "UTF-8");
                            $judul = htmlspecialchars($judulMateri, ENT_QUOTES, "UTF-8");
                            $deskripsi = htmlspecialchars((string)($materi["deskripsi"] ?? ""), ENT_QUOTES, "UTF-8");
                            $materiId = (int)($materi["id"] ?? 0);
                        ?>

                        <article class="group flex h-full flex-col rounded-[22px] border border-[#DCE6F5] bg-white p-3.5 shadow-[0_8px_24px_rgba(23,58,112,0.06)] transition duration-300 hover:-translate-y-1 hover:border-[#BFD4F6] hover:shadow-[0_16px_34px_rgba(23,58,112,0.12)] sm:p-4">
                            <div class="aspect-[2.35/1] w-full overflow-hidden rounded-[17px] bg-[#EAF2FF]">
                                <img
                                    src="<?php echo $gambarMateri; ?>"
                                    alt="Ilustrasi materi <?php echo $judul; ?>"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.025]"
                                    loading="lazy"
                                >
                            </div>

                            <div class="flex flex-1 flex-col px-1.5 pb-1 pt-4 sm:px-1">
                                <span class="inline-flex w-fit rounded-full border border-[#D7E5FF] bg-[#EAF2FF] px-3 py-1 text-xs font-semibold text-[#173A70]">
                                    Kelas <?php echo $kelas; ?>
                                </span>

                                <h2 class="mt-3 font-heading text-xl font-bold tracking-tight text-[#173A70] sm:text-2xl">
                                    <?php echo $judul; ?>
                                </h2>

                                <p class="mt-2 min-h-[48px] text-sm leading-6 text-[#61738D]">
                                    <?php echo $deskripsi; ?>
                                </p>

                                <a
                                    href="materi-detail.php?id=<?php echo $materiId; ?>"
                                    class="mt-5 inline-flex w-fit items-center gap-3 rounded-full bg-[#173A70] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#28599A] focus:outline-none focus:ring-4 focus:ring-[#173A70]/15"
                                >
                                    Masuk Materi
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M5 12h14"/>
                                        <path d="m12 5 7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="rounded-2xl border border-[#DCE6F5] bg-white p-6 text-sm text-[#61738D] md:col-span-2">
                        Belum ada materi yang dipublikasikan.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- KEUNGGULAN PHYZONE -->
    <section class="bg-white py-14 sm:py-16">
        <div class="mx-auto max-w-[1460px] px-4 sm:px-6">
            <div class="mx-auto max-w-xl text-center">
                <h2 class="font-heading text-2xl font-bold text-[#173A70] sm:text-3xl">Kenapa PhyZone?</h2>
                <p class="mt-3 text-sm leading-6 text-[#61738D] sm:text-base">
                    Semua yang kamu butuhkan untuk belajar fisika ada di satu tempat.
                </p>
            </div>

            <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?php
                $fitur = [
                    ["Materi Terstruktur", "Materi disusun secara bertahap agar kamu lebih mudah mengikuti pembelajaran.", "book-open"],
                    ["Visualisasi Fisika", "Konsep fisika diperkuat melalui ilustrasi dan visualisasi yang mudah dipahami.", "chart-no-axes-combined"],
                    ["Chatbot AI", "PhyBot siap membantu menjawab pertanyaan seputar materi yang sedang kamu pelajari.", "bot"],
                    ["Latihan Soal", "Uji pemahamanmu setelah mempelajari materi dengan soal latihan yang tersedia.", "clipboard-check"]
                ];
                $ikon = [
                    "book-open" => '<path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1 1h0a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1h3a5 5 0 0 1 5 5 5 5 0 0 1 5-5h3a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-4a3 3 0 0 0-6 0v4a1 1 0 0 1-1 1Z"/>',
                    "chart-no-axes-combined" => '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-5 5"/>',
                    "bot" => '<rect x="4" y="8" width="16" height="12" rx="2"/><path d="M12 8V4H8"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 13v2"/><path d="M15 13v2"/>',
                    "clipboard-check" => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4.5h6"/><path d="m9 13 2 2 4-4"/>'
                ];
                foreach ($fitur as $item):
                ?>
                    <article class="rounded-2xl border border-[#E2EAF5] bg-[#FBFCFF] p-5 text-center transition hover:-translate-y-1 hover:shadow-lg hover:shadow-[#173A70]/5">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EAF2FF] text-[#173A70]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <?php echo $ikon[$item[2]]; ?>
                            </svg>
                        </span>
                        <h3 class="mt-4 font-heading text-base font-semibold text-[#173A70]"><?php echo $item[0]; ?></h3>
                        <p class="mt-2 text-sm leading-6 text-[#61738D]"><?php echo $item[1]; ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CHATBOT AI -->
    <section id="chat-ai" class="bg-[#F4F7FC] py-14 sm:py-16">
        <div class="mx-auto grid max-w-[1460px] items-center gap-8 px-4 sm:px-6 lg:grid-cols-2">
            <div>
                <span class="inline-flex rounded-full border border-[#D7E5FF] bg-[#EAF2FF] px-4 py-2 text-xs font-semibold text-[#173A70]">
                    PhyBot, Asisten Belajarmu
                </span>
                <h2 class="mt-5 font-heading text-2xl font-bold text-[#173A70] sm:text-3xl">Belajar Tidak Harus Sendiri</h2>
                <p class="mt-4 max-w-xl text-sm leading-7 text-[#61738D] sm:text-base">
                    PhyBot membantu menjawab pertanyaan yang berkaitan dengan materi yang sedang kamu pelajari.
                    Bingung dengan suatu konsep? Langsung tanyakan saja.
                </p>
                <p class="mt-4 rounded-xl border border-[#E5DDBF] bg-[#FFF9E9] p-4 text-sm leading-6 text-[#536783]">
                    PhyBot berfokus pada materi pembelajaran yang tersedia di PhyZone, sehingga jawabannya dapat mendukung proses belajarmu.
                </p>
            </div>

            <div class="rounded-2xl border border-[#DCE6F5] bg-white p-5 shadow-[0_10px_30px_rgba(23,58,112,0.07)]">
                <div class="flex items-center gap-3 border-b border-[#E8EEF7] pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#173A70] text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="8" width="16" height="12" rx="2"/><path d="M12 8V4H8"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 13v2"/><path d="M15 13v2"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-heading text-sm font-semibold text-[#173A70]">PhyBot</p>
                        <p class="text-xs text-[#8190A5]">Pendamping belajarmu</p>
                    </div>
                </div>

                <div class="flex flex-col gap-4 py-5">
                    <div class="flex justify-end">
                        <div class="max-w-[82%] rounded-2xl rounded-br-sm bg-[#173A70] px-4 py-3 text-sm leading-6 text-white">
                            Kenapa benda bisa mengalami torsi?
                        </div>
                    </div>
                    <div class="flex justify-start">
                        <div class="max-w-[88%] rounded-2xl rounded-bl-sm bg-[#EAF2FF] px-4 py-3 text-sm leading-6 text-[#34445B]">
                            Torsi muncul ketika gaya bekerja pada suatu benda pada jarak tertentu dari sumbu rotasi. Besarnya dipengaruhi oleh besar gaya, jarak terhadap sumbu, dan sudut antara gaya dengan lengan gayanya.
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 rounded-full border border-[#E1E8F2] bg-[#FBFCFF] px-4 py-2.5">
                    <input type="text" placeholder="Tulis pertanyaanmu di sini..." disabled class="w-full bg-transparent text-sm text-[#34445B] outline-none placeholder:text-[#9AA8BA]">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#173A70] text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-center text-xs text-[#8A98AB]">Tampilan ilustrasi PhyBot.</p>
            </div>
        </div>
    </section>

    <!-- PENGEMBANG -->
    <section id="tentang" class="bg-white py-14 sm:py-16">
        <div class="mx-auto max-w-[1460px] px-4 sm:px-6">
            <div class="mx-auto max-w-xl text-center">
                <h2 class="font-heading text-2xl font-bold text-[#173A70] sm:text-3xl">PhyZone Dikembangkan Oleh</h2>
                <p class="mt-3 text-sm leading-6 text-[#61738D] sm:text-base">
                    Dua mahasiswa yang ingin membuat belajar fisika terasa lebih mudah dan menyenangkan.
                </p>
            </div>

            <div class="mx-auto mt-8 grid max-w-2xl gap-4 sm:grid-cols-2">
                <?php foreach (["Melinda Amru Claudia", "Putri Suci Ramadani"] as $namaPengembang): ?>
                    <article class="flex items-center gap-4 rounded-2xl border border-[#E2EAF5] bg-[#FBFCFF] p-5">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#EAF2FF] text-[#173A70]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="5"/><path d="M3 21a9 9 0 0 1 18 0"/>
                            </svg>
                        </span>
                        <p class="font-heading text-sm font-semibold text-[#173A70]"><?php echo htmlspecialchars($namaPengembang, ENT_QUOTES, "UTF-8"); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once "includes/footer.php"; ?>
