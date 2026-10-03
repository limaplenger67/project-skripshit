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

// Ambil submateri sebelumnya
$stmtPrev = mysqli_prepare($conn, "SELECT id, judul FROM submateri
                                   WHERE materi_id = ?
                                   AND status_publikasi = 'publik'
                                   AND urutan < ?
                                   ORDER BY urutan DESC
                                   LIMIT 1");

mysqli_stmt_bind_param($stmtPrev, "ii", $materiId, $submateri["urutan"]);
mysqli_stmt_execute($stmtPrev);

$prevSub = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtPrev));

mysqli_stmt_close($stmtPrev);

// Ambil submateri berikutnya
$stmtNext = mysqli_prepare($conn, "SELECT id, judul FROM submateri
                                   WHERE materi_id = ?
                                   AND status_publikasi = 'publik'
                                   AND urutan > ?
                                   ORDER BY urutan ASC
                                   LIMIT 1");

mysqli_stmt_bind_param($stmtNext, "ii", $materiId, $submateri["urutan"]);
mysqli_stmt_execute($stmtNext);

$nextSub = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtNext));

mysqli_stmt_close($stmtNext);

$pageTitle = $submateri["judul"];

// ======================================================
// PERTANYAAN PEMANTIK
// ======================================================

$pertanyaanPemantikMap = [
    "Pengantar Dinamika Rotasi" => "Mengapa roda sepeda bisa bergerak maju sekaligus berputar ketika sepeda dikayuh?",
    "Torsi (Momen Gaya)" => "Mengapa pintu lebih mudah dibuka jika kita mendorongnya dari bagian yang jauh dari engsel?",
    "Torsi" => "Mengapa pintu lebih mudah dibuka jika kita mendorongnya dari bagian yang jauh dari engsel?",
    "Momen Inersia" => "Mengapa roda lebih mudah diputar jika sebagian besar massanya berada di dekat bagian tengah roda?",
    "Hukum Newton untuk Rotasi" => "Mengapa benda bisa berputar lebih cepat jika gaya yang diberikan lebih besar?",
    "Energi Kinetik Rotasi" => "Mengapa roda yang berputar memiliki energi gerak?",
    "Momentum Sudut" => "Mengapa penari bisa berputar lebih cepat ketika kedua tangannya dirapatkan ke tubuh?"
];

$judulCari = trim(
    preg_replace('/\s+/', ' ', $submateri["judul"])
);

$pertanyaanPemantik = $pertanyaanPemantikMap[$judulCari] ?? "";

// ======================================================
// INTISARI UNTUK FITUR SUARA
// ======================================================

$judulIntisari = trim($submateri["judul"]);

$intisariMateri = "";

if (stripos($judulIntisari, "Pengantar Dinamika Rotasi") !== false) {

    $intisariMateri =
        "Oke, di materi ini kita mulai dengan memahami apa itu dinamika rotasi. "
        . "Sederhananya, dinamika rotasi membahas gerak benda yang berputar dan bagaimana gaya dapat memengaruhi gerakan tersebut. "
        . "Beberapa konsep penting yang akan muncul adalah torsi dan momen inersia. "
        . "Jadi, kita akan melihat bagaimana suatu benda bisa mulai berputar, berubah putarannya, atau mempertahankan gerak rotasinya.";

} elseif (stripos($judulIntisari, "Torsi") !== false) {

    $intisariMateri =
        "Pernah kepikiran kenapa pintu lebih mudah dibuka kalau didorong dari bagian yang jauh dari engsel? "
        . "Nah, ini berkaitan dengan torsi. "
        . "Torsi menunjukkan kemampuan suatu gaya untuk memutar benda terhadap sumbu rotasi. "
        . "Besarnya dipengaruhi oleh gaya, jarak dari sumbu rotasi, dan sudut antara posisi dengan arah gaya.";

} elseif (stripos($judulIntisari, "Momen Inersia") !== false) {

    $intisariMateri =
        "Kalau torsi berkaitan dengan kemampuan gaya memutar benda, momen inersia berkaitan dengan seberapa sulit benda itu diputar. "
        . "Nilainya dipengaruhi oleh massa benda dan bagaimana massa tersebut tersebar terhadap sumbu rotasi. "
        . "Jadi, dua benda yang massanya sama belum tentu memiliki momen inersia yang sama.";

} elseif (stripos($judulIntisari, "Hukum Newton") !== false) {

    $intisariMateri =
        "Sekarang kita masuk ke hubungan antara torsi dan gerak rotasi. "
        . "Hukum Newton untuk rotasi menyatakan bahwa semakin besar resultan torsi yang bekerja pada benda, semakin besar pula kecenderungan benda mengalami percepatan sudut. "
        . "Hubungan ini melibatkan torsi, momen inersia, dan percepatan sudut, sama seperti hubungan gaya, massa, dan percepatan pada gerak translasi.";

} elseif (stripos($judulIntisari, "Energi Kinetik Rotasi") !== false) {

    $intisariMateri =
        "Benda yang berputar juga memiliki energi. "
        . "Energi tersebut disebut energi kinetik rotasi dan dipengaruhi oleh momen inersia serta kecepatan sudut benda. "
        . "Pada benda yang menggelinding, terdapat energi kinetik translasi karena benda bergerak maju dan energi kinetik rotasi karena benda juga berputar.";

} elseif (stripos($judulIntisari, "Momentum Sudut") !== false) {

    $intisariMateri =
        "Terakhir, kita bahas momentum sudut. "
        . "Momentum sudut merupakan ukuran kuantitas gerak rotasi pada benda. "
        . "Untuk benda tegar, momentum sudut berkaitan dengan momen inersia dan kecepatan sudut. "
        . "Ketika resultan torsi luar pada suatu sistem bernilai nol, momentum sudutnya tetap. "
        . "Contohnya bisa kita lihat pada penari atau atlet skater yang dapat mempercepat putarannya ketika kedua tangannya dirapatkan ke tubuh.";
}

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:py-14">

    <!-- Breadcrumb -->
    <nav class="text-sm text-darktext/60" aria-label="Breadcrumb">
        <a href="materi.php" class="hover:text-navy">
            Materi
        </a>

        <span class="mx-1">/</span>

        <a href="materi-detail.php?id=<?php echo $materiId; ?>" class="hover:text-navy">
            <?php echo htmlspecialchars($submateri["materi_judul"]); ?>
        </a>
    </nav>


    <!-- Judul -->
    <div class="mt-5">

        <span class="inline-flex rounded-full bg-lavender px-3 py-1 text-xs font-semibold text-navy">
            Kelas <?php echo htmlspecialchars($submateri["kelas"]); ?>
            ·
            <?php echo htmlspecialchars($submateri["materi_judul"]); ?>
        </span>

        <h1 class="mt-3 font-heading text-2xl font-bold text-navy sm:text-3xl">
            <?php echo htmlspecialchars($submateri["judul"]); ?>
        </h1>

    </div>


    <!-- Pertanyaan Pemantik -->
    <section
        id="card-pemantik"
        class="mt-8 rounded-2xl border border-darktext/10 bg-white p-6 sm:p-8"
    >

        <h2 class="font-heading text-lg font-bold text-navy">
            Pertanyaan Pemantik
        </h2>

        <p class="mt-3 leading-relaxed text-darktext/90">
            <?php echo htmlspecialchars($pertanyaanPemantik); ?>
        </p>


        <div id="area-jawab">

            <label
                for="jawaban-siswa"
                class="mt-5 block text-sm font-semibold text-darktext/80"
            >
                Tulis jawabanmu berdasarkan pemahaman awalmu:
            </label>


            <textarea
                id="jawaban-siswa"
                rows="5"
                placeholder="Tulis jawabanmu di sini..."
                class="mt-2 w-full rounded-xl border border-darktext/15 bg-warmwhite px-4 py-3 leading-relaxed text-darktext focus:border-navy focus:outline-none"
            ></textarea>


            <p
                id="error-pemantik"
                class="mt-2 hidden text-sm font-semibold text-red-600"
            >
                Silakan isi jawabanmu terlebih dahulu sebelum mengirim.
            </p>


            <button
                type="button"
                id="btn-kirim-jawaban"
                class="mt-4 rounded-full bg-navy px-6 py-2.5 text-sm font-semibold text-warmwhite transition-colors hover:bg-blue"
            >
                Kirim Jawaban
            </button>

        </div>


        <!-- Hasil Jawaban -->
        <div
            id="hasil-jawaban"
            class="mt-5 hidden rounded-xl border border-darktext/10 bg-lavender/40 p-4"
        >

            <p class="text-sm font-semibold text-navy">
                Jawabanmu:
            </p>

            <p
                id="isi-jawaban"
                class="mt-2 whitespace-pre-line leading-relaxed text-darktext/90"
            ></p>

            <p class="mt-3 text-xs italic text-darktext/60">
                Ini adalah pemahaman awalmu. Sekarang bandingkan dengan pembahasan materi di bawah ini!
            </p>

        </div>

    </section>


    <!-- Isi Submateri -->
    <article
        id="pembahasan-materi"
        class="mt-8 hidden rounded-2xl border border-darktext/10 bg-white p-6 sm:p-8"
    >

        <h2 class="font-heading text-lg font-bold text-navy">
            Pembahasan Materi
        </h2>


        <!-- Tombol Suara -->
        <div class="mt-4 flex flex-wrap gap-2">

            <button
                type="button"
                id="btn-baca"
                class="rounded-full bg-navy px-5 py-2.5 text-sm font-semibold text-warmwhite transition-colors hover:bg-blue"
            >
                🔊 Dengerin Intinya
            </button>


            <button
                type="button"
                id="btn-stop"
                class="rounded-full border-2 border-darktext/15 px-5 py-2.5 text-sm font-semibold text-darktext transition-colors hover:border-navy hover:text-navy"
            >
                ⏹ Stop
            </button>

        </div>

        <!-- Isi Materi -->
        <div class="mt-6">

            <?php if ($submateri["isi"]): ?>

                <div class="space-y-4 leading-relaxed text-darktext/90">

                    <?php
                    $isiMateri = trim($submateri["isi"]);

                    $blokMateri = preg_split(
                        "/\n\s*\n/",
                        $isiMateri
                    );

                    foreach ($blokMateri as $blok):

                        $blok = trim($blok);

                        if ($blok === "") {
                            continue;
                        }

                        // Jika blok merupakan rumus display MathJax
                        if (
                            str_starts_with($blok, "\[")
                            &&
                            str_ends_with($blok, "\]")
                        ) {

                            echo $blok;

                        } else {

                            echo "<p>"
                                . nl2br(htmlspecialchars($blok))
                                . "</p>";
                        }

                    endforeach;
                    ?>

                </div>

            <?php else: ?>

                <p class="text-darktext/60">
                    Isi submateri ini belum tersedia.
                </p>

            <?php endif; ?>

        </div>

    </article>


    <!-- Simulasi -->
    <?php if (!empty($submateri["simulasi"])): ?>

        <?php

        $simFile = "includes/simulasi-" . $submateri["simulasi"] . ".php";

        if (file_exists($simFile)) {
            include $simFile;
        }

        ?>

    <?php endif; ?>


    <!-- Materi Visual -->
    <?php if (!empty($submateri["media"])): ?>

        <section class="mt-8 rounded-2xl border border-darktext/10 bg-white p-6 sm:p-8">

            <h2 class="font-heading text-lg font-bold text-navy">
                Materi Visual
            </h2>


            <?php

            $ekstensi = strtolower(
                pathinfo(
                    $submateri["media"],
                    PATHINFO_EXTENSION
                )
            );

            ?>


            <?php if ($ekstensi === "pdf"): ?>

                <iframe
                    src="<?php echo htmlspecialchars($submateri["media"]); ?>"
                    class="mt-4 h-[480px] w-full rounded-xl border border-darktext/15"
                    title="Materi visual PDF"
                ></iframe>


            <?php elseif ($ekstensi === "mp4"): ?>

                <video
                    controls
                    class="mt-4 w-full rounded-xl border border-darktext/15"
                >

                    <source
                        src="<?php echo htmlspecialchars($submateri["media"]); ?>"
                        type="video/mp4"
                    >

                    Browser kamu tidak mendukung pemutar video.

                </video>


            <?php else: ?>

                <img
                    src="<?php echo htmlspecialchars($submateri["media"]); ?>"
                    alt="Materi visual"
                    class="mt-4 w-full rounded-xl border border-darktext/15"
                />

            <?php endif; ?>

        </section>

    <?php endif; ?>


    <!-- Navigasi Sebelumnya / Berikutnya -->
    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-between">

        <?php if ($prevSub): ?>

            <a
                href="submateri-detail.php?id=<?php echo $prevSub["id"]; ?>"
                class="group flex items-center gap-2 rounded-full border-2 border-darktext/15 px-5 py-2.5 text-sm font-semibold text-darktext transition-colors hover:border-navy hover:text-navy"
            >

                <svg
                    class="transition-transform group-hover:-translate-x-0.5"
                    xmlns="http://www.w3.org/2000/svg"
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M19 12H5"/>
                    <path d="m12 19-7-7 7-7"/>
                </svg>

                <span class="max-w-[200px] truncate">
                    <?php echo htmlspecialchars($prevSub["judul"]); ?>
                </span>

            </a>

        <?php else: ?>

            <span class="invisible"></span>

        <?php endif; ?>


        <?php if ($nextSub): ?>

            <a
                href="submateri-detail.php?id=<?php echo $nextSub["id"]; ?>"
                class="group flex items-center justify-end gap-2 rounded-full bg-navy px-5 py-2.5 text-sm font-semibold text-warmwhite transition-colors hover:bg-blue"
            >

                <span class="max-w-[200px] truncate">
                    <?php echo htmlspecialchars($nextSub["judul"]); ?>
                </span>

                <svg
                    class="transition-transform group-hover:translate-x-0.5"
                    xmlns="http://www.w3.org/2000/svg"
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                </svg>

            </a>

        <?php endif; ?>

    </div>

</main>


<?php require_once "includes/footer.php"; ?>


<script>
document.addEventListener("DOMContentLoaded", function () {

    // ==========================================
    // ELEMEN PERTANYAAN PEMANTIK
    // ==========================================

    var btn = document.getElementById("btn-kirim-jawaban");
    var textarea = document.getElementById("jawaban-siswa");
    var errorMsg = document.getElementById("error-pemantik");
    var hasil = document.getElementById("hasil-jawaban");
    var isiJawaban = document.getElementById("isi-jawaban");
    var areaJawab = document.getElementById("area-jawab");
    var pembahasan = document.getElementById("pembahasan-materi");


    // ==========================================
    // KIRIM JAWABAN
    // ==========================================

    function kirimJawaban() {

        var jawaban = textarea.value.trim();


        if (jawaban === "") {

            errorMsg.classList.remove("hidden");

            textarea.focus();

            return;
        }


        errorMsg.classList.add("hidden");

        isiJawaban.textContent = jawaban;

        hasil.classList.remove("hidden");

        areaJawab.classList.add("hidden");

        pembahasan.classList.remove("hidden");

        pembahasan.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });

    }


    // Klik tombol Kirim Jawaban
    btn.addEventListener("click", function () {

        kirimJawaban();

    });


    // ==========================================
    // ENTER UNTUK KIRIM
    // SHIFT + ENTER UNTUK BARIS BARU
    // ==========================================

    textarea.addEventListener("keydown", function (event) {

        if (event.key === "Enter" && !event.shiftKey) {

            event.preventDefault();

            kirimJawaban();

        }

    });


    // ==========================================
    // FITUR SUARA
    // ==========================================

    var btnBaca = document.getElementById("btn-baca");
    var btnStop = document.getElementById("btn-stop");


    // Intisari dari PHP
    var teksIntisari = <?php
        echo json_encode(
            $intisariMateri,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    ?>;


    // Tombol Dengerin Intinya
    btnBaca.addEventListener("click", function () {

        if (!("speechSynthesis" in window)) {

            alert("Browser kamu tidak mendukung fitur suara.");

            return;
        }


        if (teksIntisari === "") {

            alert("Intisari untuk submateri ini belum tersedia.");

            return;
        }


        // Hentikan suara sebelumnya jika masih berjalan
        window.speechSynthesis.cancel();


        var suara = new SpeechSynthesisUtterance(
            teksIntisari
        );


        suara.lang = "id-ID";

        suara.rate = 0.9;

        suara.pitch = 1;


        window.speechSynthesis.speak(suara);

    });


    // Tombol Stop
    btnStop.addEventListener("click", function () {

        if ("speechSynthesis" in window) {

            window.speechSynthesis.cancel();

        }

    });

});
</script>


<noscript>

    <style>
        #pembahasan-materi {
            display: block !important;
        }
    </style>

</noscript>
