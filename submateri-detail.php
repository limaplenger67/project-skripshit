
<?php
$pageTitle = "Submateri";

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

// ==========================================
// VALIDASI ID SUBMATERI
// ==========================================

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: materi.php");
    exit;
}

$id = (int) $_GET["id"];

// ==========================================
// AMBIL DATA SUBMATERI
// ==========================================
<a href="latihan-soal.php?id=<?= (int)$submateri['id'] ?>">
    Latihan Soal
</a>

$stmt = mysqli_prepare(
    $conn,
    "SELECT s.*, 
            m.judul AS materi_judul,
            m.kelas,
            m.id AS materi_id
     FROM submateri s
     JOIN materi m ON s.materi_id = m.id
     WHERE s.id = ? 
     AND s.status_publikasi = 'publik'"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$submateri = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmt)
);

mysqli_stmt_close($stmt);

if (!$submateri) {
    header("Location: materi.php");
    exit;
}

$materiId = (int) $submateri["materi_id"];
$judul = trim($submateri["judul"]);

$pageTitle = $judul;

// ==========================================
// NAVIGASI SUBMATERI SEBELUMNYA
// ==========================================

$stmtPrev = mysqli_prepare(
    $conn,
    "SELECT id, judul FROM submateri
     WHERE materi_id = ?
     AND status_publikasi = 'publik'
     AND urutan < ?
     ORDER BY urutan DESC
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmtPrev,
    "ii",
    $materiId,
    $submateri["urutan"]
);

mysqli_stmt_execute($stmtPrev);

$prevSub = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmtPrev)
);

mysqli_stmt_close($stmtPrev);

// ==========================================
// NAVIGASI SUBMATERI BERIKUTNYA
// ==========================================

$stmtNext = mysqli_prepare(
    $conn,
    "SELECT id, judul FROM submateri
     WHERE materi_id = ?
     AND status_publikasi = 'publik'
     AND urutan > ?
     ORDER BY urutan ASC
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmtNext,
    "ii",
    $materiId,
    $submateri["urutan"]
);

mysqli_stmt_execute($stmtNext);

$nextSub = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmtNext)
);

mysqli_stmt_close($stmtNext);

// ==========================================
// PERTANYAAN PEMANTIK
// HANYA AWAL BAGIAN A, B, DAN C
// ==========================================

$pertanyaanPemantikMap = [

    11 => "Pernahkah kamu melihat sebuah benda yang bergerak dari satu tempat ke tempat lain? Bagaimana cara kita menyatakan besar dan arah perpindahan benda tersebut menggunakan konsep vektor?",

    14 => "Jika sebuah gaya bekerja pada suatu benda dengan arah miring, apakah seluruh gaya tersebut bekerja pada arah horizontal dan vertikal secara bersamaan? Bagaimana cara menguraikannya?",

    16 => "Jika dua orang menarik sebuah benda menggunakan tali dari arah yang berbeda, bagaimana cara menentukan arah dan besar gaya gabungan yang bekerja pada benda tersebut?"

];

$pertanyaanPemantik = $pertanyaanPemantikMap[$id] ?? "";

$adaPemantik = !empty($pertanyaanPemantik);

// ==========================================
// INTISARI MATERI UNTUK FITUR SUARA
// ==========================================

$intisariVektorMap = [

    11 => "Pada materi ini kita akan mengenal konsep dasar vektor. Vektor merupakan besaran yang memiliki nilai atau besar serta arah. Berbeda dengan besaran skalar yang hanya memiliki nilai, vektor perlu dinyatakan menggunakan lambang dan arah tertentu. Contohnya adalah perpindahan, kecepatan, dan gaya.",

    12 => "Menggambar vektor dilakukan dengan menggunakan garis berarah atau anak panah. Panjang anak panah menunjukkan besar vektor, sedangkan arah anak panah menunjukkan arah vektor. Dalam menggambar vektor, kita perlu memperhatikan skala dan arah agar representasinya tepat.",

    13 => "Vektor memiliki beberapa sifat penting, seperti kesamaan vektor, vektor negatif, dan vektor nol. Dua vektor dikatakan sama apabila memiliki besar dan arah yang sama, meskipun titik pangkalnya berbeda.",

    14 => "Komponen vektor merupakan bagian vektor yang diuraikan ke dalam arah sumbu horizontal dan vertikal. Komponen horizontal biasanya dinyatakan sebagai Vx, sedangkan komponen vertikal dinyatakan sebagai Vy. Kedua komponen tersebut dapat digunakan untuk menentukan besar dan arah vektor.",

    15 => "Penguraian vektor berdasarkan aturan trigonometri menggunakan hubungan sinus, kosinus, dan tangen. Jika sudut diukur dari sumbu horizontal, komponen horizontal diperoleh dari besar vektor dikalikan cosinus sudut, sedangkan komponen vertikal diperoleh dari besar vektor dikalikan sinus sudut.",

    16 => "Penjumlahan dan pengurangan vektor dengan metode grafis dilakukan melalui gambar. Beberapa metode yang dapat digunakan adalah metode segitiga, metode jajargenjang, dan metode poligon. Resultan merupakan vektor yang menggambarkan hasil gabungan beberapa vektor.",

    17 => "Metode analitis menentukan resultan vektor dengan menggunakan komponen-komponennya. Komponen pada arah horizontal dijumlahkan secara terpisah dari komponen pada arah vertikal. Setelah itu, besar resultan dapat dihitung menggunakan teorema Pythagoras.",

    18 => "Rumus kosinus digunakan untuk menentukan besar resultan dua vektor yang membentuk sudut tertentu. Rumus ini membantu menghitung resultan tanpa harus menggambar vektor secara langsung.",

    19 => "Rumus sinus dapat digunakan untuk menentukan arah resultan vektor. Dengan mengetahui besar kedua vektor dan sudut di antara keduanya, arah resultan dapat ditentukan melalui perbandingan sinus.",

    20 => "Perkalian vektor terdiri atas perkalian titik atau dot product dan perkalian silang atau cross product. Perkalian titik menghasilkan besaran skalar, sedangkan perkalian silang menghasilkan besaran vektor."
];

$intisariMateri = $intisariVektorMap[$id] ?? "";

// ==========================================
// INTISARI DINAMIKA ROTASI
// UNTUK MENJAGA FITUR PADA PROJEK TEMAN
// ==========================================

if ($intisariMateri === "") {

    if (stripos($judul, "Pengantar Dinamika Rotasi") !== false) {

        $intisariMateri =
            "Dinamika rotasi membahas gerak benda yang berputar dan bagaimana gaya memengaruhi gerakan tersebut. Konsep pentingnya meliputi torsi dan momen inersia.";

    } elseif (stripos($judul, "Torsi") !== false) {

        $intisariMateri =
            "Torsi menunjukkan kemampuan suatu gaya untuk memutar benda terhadap sumbu rotasi. Besarnya dipengaruhi oleh gaya, jarak dari sumbu rotasi, dan sudut antara posisi dengan arah gaya.";

    } elseif (stripos($judul, "Momen Inersia") !== false) {

        $intisariMateri =
            "Momen inersia menunjukkan tingkat kesulitan suatu benda untuk mengalami perubahan gerak rotasi. Nilainya dipengaruhi oleh massa dan distribusi massa terhadap sumbu rotasi.";

    } elseif (stripos($judul, "Hukum Newton") !== false) {

        $intisariMateri =
            "Hukum Newton untuk rotasi menghubungkan resultan torsi, momen inersia, dan percepatan sudut. Semakin besar resultan torsi, semakin besar percepatan sudut jika momen inersia tetap.";

    } elseif (stripos($judul, "Energi Kinetik Rotasi") !== false) {

        $intisariMateri =
            "Energi kinetik rotasi merupakan energi yang dimiliki benda karena gerak berputarnya. Energi ini dipengaruhi oleh momen inersia dan kuadrat kecepatan sudut.";

    } elseif (stripos($judul, "Momentum Sudut") !== false) {

        $intisariMateri =
            "Momentum sudut merupakan ukuran gerak rotasi benda. Untuk benda tegar, momentum sudut berkaitan dengan momen inersia dan kecepatan sudut.";
    }
}

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:py-14">

    <!-- BREADCRUMB -->

    <nav class="text-sm text-darktext/60" aria-label="Breadcrumb">

        <a href="materi.php" class="hover:text-navy">
            Materi
        </a>

        <span class="mx-1">/</span>

        <a
            href="materi-detail.php?id=<?php echo $materiId; ?>"
            class="hover:text-navy"
        >
            <?php echo htmlspecialchars($submateri["materi_judul"]); ?>
        </a>

    </nav>


    <!-- JUDUL SUBMATERI -->

    <div class="mt-5">

        <span class="inline-flex rounded-full bg-lavender px-3 py-1 text-xs font-semibold text-navy">

            Kelas <?php echo htmlspecialchars($submateri["kelas"]); ?>
            ·
            <?php echo htmlspecialchars($submateri["materi_judul"]); ?>

        </span>

        <h1 class="mt-3 font-heading text-2xl font-bold text-navy sm:text-3xl">

            <?php echo htmlspecialchars($judul); ?>

        </h1>

    </div>


    <!-- PERTANYAAN PEMANTIK -->

    <?php if ($adaPemantik): ?>

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

        <!-- HASIL JAWABAN -->

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

    <?php endif; ?>


    <!-- PEMBAHASAN MATERI -->

    <article
        id="pembahasan-materi"
        class="mt-8 rounded-2xl border border-darktext/10 bg-white p-6 sm:p-8"
        <?php if ($adaPemantik): ?>
            style="display:none;"
        <?php endif; ?>
    >

        <h2 class="font-heading text-lg font-bold text-navy">
            Pembahasan Materi
        </h2>


        <!-- FITUR SUARA -->

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


        <!-- ISI MATERI -->

        <div class="mt-6">

            <?php if (!empty($submateri["isi"])): ?>

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

                        if (
                            str_starts_with($blok, '\[') &&
                            str_ends_with($blok, '\]')
                        ) {

                            echo $blok;

                        } else {

                            echo "<p>" .
                                nl2br(htmlspecialchars($blok)) .
                                "</p>";

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


    <!-- SIMULASI -->

    <?php if (!empty($submateri["simulasi"])): ?>

        <?php

        $simFile = "includes/simulasi-" .
            basename($submateri["simulasi"]) .
            ".php";

        if (file_exists($simFile)) {
            include $simFile;
        }

        ?>

    <?php endif; ?>


    <!-- MATERI VISUAL -->

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
                >

            <?php endif; ?>

        </section>

    <?php endif; ?>


    <!-- NAVIGASI SUBMATERI -->

    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-between">

        <?php if ($prevSub): ?>

            <a
                href="submateri-detail.php?id=<?php echo $prevSub["id"]; ?>"
                class="group flex items-center gap-2 rounded-full border-2 border-darktext/15 px-5 py-2.5 text-sm font-semibold text-darktext transition-colors hover:border-navy hover:text-navy"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="transition-transform group-hover:-translate-x-0.5"
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
                    xmlns="http://www.w3.org/2000/svg"
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="transition-transform group-hover:translate-x-0.5"
                >
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                </svg>

            </a>

        <?php endif; ?>

    </div>

</main>


<?php require_once "includes/footer.php"; ?>


<!-- ==========================================
     JAVASCRIPT
========================================== -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    // ======================================
    // PERTANYAAN PEMANTIK
    // ======================================

    var btn = document.getElementById("btn-kirim-jawaban");

    var textarea = document.getElementById("jawaban-siswa");

    var errorMsg = document.getElementById("error-pemantik");

    var hasil = document.getElementById("hasil-jawaban");

    var isiJawaban = document.getElementById("isi-jawaban");

    var areaJawab = document.getElementById("area-jawab");

    var pembahasan = document.getElementById("pembahasan-materi");


    function kirimJawaban() {

        if (!textarea) return;

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

        pembahasan.style.display = "block";

        pembahasan.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });

    }


    if (btn) {

        btn.addEventListener("click", kirimJawaban);

        textarea.addEventListener("keydown", function(event) {

            if (event.key === "Enter" && !event.shiftKey) {

                event.preventDefault();

                kirimJawaban();

            }

        });

    }


    // ======================================
    // FITUR SUARA
    // ======================================

    var btnBaca = document.getElementById("btn-baca");

    var btnStop = document.getElementById("btn-stop");


    var teksIntisari = <?php

        echo json_encode(
            $intisariMateri,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );

    ?>;


    // DENGERIN INTINYA

    if (btnBaca) {

        btnBaca.addEventListener("click", function() {

            if (!("speechSynthesis" in window)) {

                alert("Browser kamu tidak mendukung fitur suara.");

                return;
            }

            if (teksIntisari === "") {

                alert("Intisari untuk submateri ini belum tersedia.");

                return;
            }

            window.speechSynthesis.cancel();

            var suara = new SpeechSynthesisUtterance(
                teksIntisari
            );

            suara.lang = "id-ID";

            suara.rate = 0.9;

            suara.pitch = 1;

            window.speechSynthesis.speak(suara);

        });

    }


    // STOP SUARA

    if (btnStop) {

        btnStop.addEventListener("click", function() {

            if ("speechSynthesis" in window) {

                window.speechSynthesis.cancel();

            }

        });

    }

});

</script>


<noscript>

    <style>
        #pembahasan-materi {
            display: block !important;
        }
    </style>

</noscript>
