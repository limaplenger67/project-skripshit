
<?php
session_start();

$pageTitle = "Latihan Soal";
require_once "config/database.php";
require_once "includes/auth.php";
requireLogin();

// Ambil ID submateri
$submateriId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($submateriId <= 0) {
    header("Location: materi.php");
    exit;
}

// Ambil data submateri dan materi
$stmtMateri = mysqli_prepare($conn, "
    SELECT s.id, s.judul, s.kelas, m.judul AS nama_materi
    FROM submateri s
    JOIN materi m ON m.id = s.materi_id
    WHERE s.id = ? 
    AND s.status_publikasi = 'publik'
    LIMIT 1
");

mysqli_stmt_bind_param($stmtMateri, "i", $submateriId);
mysqli_stmt_execute($stmtMateri);
$resultMateri = mysqli_stmt_get_result($stmtMateri);
$submateri = mysqli_fetch_assoc($resultMateri);

if (!$submateri) {
    header("Location: materi.php");
    exit;
}

// Ambil soal
$stmtSoal = mysqli_prepare($conn, "
    SELECT id, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e,
           jawaban_benar, pembahasan
    FROM soal
    WHERE submateri_id = ?
    AND status_publikasi = 'publik'
    ORDER BY id ASC
");

mysqli_stmt_bind_param($stmtSoal, "i", $submateriId);
mysqli_stmt_execute($stmtSoal);
$resultSoal = mysqli_stmt_get_result($stmtSoal);

$daftarSoal = [];
while ($row = mysqli_fetch_assoc($resultSoal)) {
    $daftarSoal[] = $row;
}

$totalSoal = count($daftarSoal);

// Proses jawaban setelah seluruh soal dikirim
$hasil = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["selesai"])) {
    $jawabanSiswa = $_POST["jawaban"] ?? [];

    $jumlahBenar = 0;
    $jumlahSalah = 0;
    $detailHasil = [];

    foreach ($daftarSoal as $index => $soal) {
        $idSoal = (string) $soal["id"];
        $jawaban = strtoupper(trim($jawabanSiswa[$idSoal] ?? ""));
        $kunci = strtoupper(trim($soal["jawaban_benar"]));

        $benar = ($jawaban !== "" && $jawaban === $kunci);

        if ($benar) {
            $jumlahBenar++;
        } else {
            $jumlahSalah++;
        }

        $detailHasil[] = [
            "soal" => $soal,
            "jawaban_siswa" => $jawaban,
            "benar" => $benar
        ];
    }

    $nilai = $totalSoal > 0
        ? round(($jumlahBenar / $totalSoal) * 100)
        : 0;

    $hasil = [
        "benar" => $jumlahBenar,
        "salah" => $jumlahSalah,
        "nilai" => $nilai,
        "detail" => $detailHasil
    ];
}

require_once "includes/head.php";
require_once "includes/navbar.php";
?>

<main class="min-h-screen bg-warmwhite py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <!-- Breadcrumb -->
        <div class="text-sm text-slate-500 mb-5">
            <a href="materi.php" class="hover:text-blue-700">Materi</a>
            <span class="mx-2">/</span>
            <a href="materi-detail.php?id=<?= (int)$submateriId ?>"
               class="hover:text-blue-700">
                <?= htmlspecialchars($submateri["nama_materi"]) ?>
            </a>
            <span class="mx-2">/</span>
            <span class="text-slate-700">Latihan Soal</span>
        </div>

        <!-- Header -->
        <section class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                    Kelas <?= htmlspecialchars($submateri["kelas"]) ?>
                </span>

                <span class="text-sm text-slate-500">
                    <?= $totalSoal ?> Soal
                </span>
            </div>

            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                Latihan Soal
            </h1>

            <p class="text-slate-600 mt-2">
                <?= htmlspecialchars($submateri["judul"]) ?>
            </p>

            <p class="text-sm text-slate-500 mt-3">
                Kerjakan seluruh soal dengan teliti. Nilai dan pembahasan
                akan ditampilkan setelah kamu menyelesaikan semua soal.
            </p>
        </section>

        <?php if ($totalSoal === 0): ?>

            <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center">
                <div class="text-4xl mb-3">📝</div>
                <h2 class="text-lg font-bold text-slate-800">
                    Belum Ada Soal
                </h2>
                <p class="text-slate-500 mt-2">
                    Soal latihan untuk materi ini belum tersedia.
                </p>
                <a href="materi-detail.php?id=<?= (int)$submateriId ?>"
                   class="inline-block mt-5 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold">
                    Kembali ke Materi
                </a>
            </div>

        <?php elseif ($hasil !== null): ?>

            <!-- HASIL LATIHAN -->
            <section class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
                <div class="text-center">
                    <div class="text-4xl mb-3">
                        <?= $hasil["nilai"] >= 75 ? "🎉" : "💪" ?>
                    </div>

                    <h2 class="text-2xl font-bold text-slate-800">
                        Latihan Selesai!
                    </h2>

                    <p class="text-slate-500 mt-2">
                        Berikut hasil pengerjaan latihan soal kamu.
                    </p>

                    <div class="my-6">
                        <p class="text-sm text-slate-500 mb-1">Nilai Akhir</p>
                        <p class="text-5xl font-bold text-blue-700">
                            <?= $hasil["nilai"] ?>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 mt-5">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-blue-700">
                            <?= $totalSoal ?>
                        </p>
                        <p class="text-xs text-slate-600 mt-1">Total Soal</p>
                    </div>

                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-green-700">
                            <?= $hasil["benar"] ?>
                        </p>
                        <p class="text-xs text-slate-600 mt-1">Benar</p>
                    </div>

                    <div class="bg-red-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-red-700">
                            <?= $hasil["salah"] ?>
                        </p>
                        <p class="text-xs text-slate-600 mt-1">Salah</p>
                    </div>
                </div>

                <div class="mt-6 rounded-xl p-4
                    <?= $hasil["nilai"] >= 75
                        ? 'bg-green-50 border border-green-100'
                        : 'bg-yellow-50 border border-yellow-100' ?>">
                    <h3 class="font-bold text-slate-800 mb-1">
                        <?= $hasil["nilai"] >= 75
                            ? "Bagus sekali!"
                            : "Tetap semangat belajar!" ?>
                    </h3>

                    <p class="text-sm text-slate-600">
                        <?= $hasil["nilai"] >= 75
                            ? "Kamu sudah memahami materi dengan baik. Pertahankan hasil belajarmu!"
                            : "Jangan menyerah. Pelajari kembali materi dan pembahasan soal agar pemahamanmu semakin baik." ?>
                    </p>
                </div>
            </section>

            <!-- PEMBAHASAN -->
            <section class="mb-6">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-slate-800">
                        Pembahasan Jawaban
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Pelajari kembali setiap jawaban untuk memperkuat pemahamanmu.
                    </p>
                </div>

                <div class="space-y-4">
                    <?php foreach ($hasil["detail"] as $index => $detail):
                        $soal = $detail["soal"];
                        $jawabanSiswa = $detail["jawaban_siswa"];
                        $kunci = strtoupper(trim($soal["jawaban_benar"]));

                        $opsi = [
                            "A" => $soal["opsi_a"],
                            "B" => $soal["opsi_b"],
                            "C" => $soal["opsi_c"],
                            "D" => $soal["opsi_d"],
                            "E" => $soal["opsi_e"]
                        ];
                    ?>
                        <article class="bg-white border border-slate-200 rounded-2xl p-5">
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <span class="font-bold text-slate-700">
                                    Soal <?= $index + 1 ?>
                                </span>

                                <?php if ($detail["benar"]): ?>
                                    <span class="text-sm font-semibold text-green-700 bg-green-50 px-3 py-1 rounded-full">
                                        ✓ Benar
                                    </span>
                                <?php else: ?>
                                    <span class="text-sm font-semibold text-red-700 bg-red-50 px-3 py-1 rounded-full">
                                        ✕ Belum tepat
                                    </span>
                                <?php endif; ?>
                            </div>

                            <p class="text-slate-800 font-medium mb-4">
                                <?= nl2br(htmlspecialchars($soal["pertanyaan"])) ?>
                            </p>

                            <div class="space-y-2">
                                <?php foreach ($opsi as $huruf => $isi):
                                    if ($isi === null || trim($isi) === "") continue;

                                    $isKunci = ($huruf === $kunci);
                                    $isJawaban = ($huruf === $jawabanSiswa);

                                    $kelasOpsi = "border-slate-200 bg-white";

                                    if ($isKunci) {
                                        $kelasOpsi = "border-green-300 bg-green-50";
                                    } elseif ($isJawaban && !$detail["benar"]) {
                                        $kelasOpsi = "border-red-300 bg-red-50";
                                    }
                                ?>
                                    <div class="border rounded-xl p-3 text-sm <?= $kelasOpsi ?>">
                                        <span class="font-bold mr-2"><?= $huruf ?>.</span>
                                        <?= htmlspecialchars($isi) ?>

                                        <?php if ($isKunci): ?>
                                            <span class="text-green-700 font-semibold ml-2">
                                                (Jawaban benar)
                                            </span>
                                        <?php elseif ($isJawaban): ?>
                                            <span class="text-red-700 font-semibold ml-2">
                                                (Jawaban kamu)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-4 p-4 rounded-xl bg-blue-50 border border-blue-100">
                                <p class="font-bold text-blue-800 text-sm mb-1">
                                    Pembahasan
                                </p>
                                <p class="text-sm text-slate-700 leading-relaxed">
                                    <?= nl2br(htmlspecialchars(
                                        $soal["pembahasan"] ?: "Pembahasan untuk soal ini belum tersedia."
                                    )) ?>
                                </p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="flex flex-wrap gap-3 justify-center pb-8">
                <a href="latihan-soal.php?id=<?= (int)$submateriId ?>"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl">
                    Ulangi Latihan
                </a>

                <a href="materi-detail.php?id=<?= (int)$submateriId ?>"
                   class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-6 py-3 rounded-xl">
                    Kembali ke Materi
                </a>
            </div>

        <?php else: ?>

            <!-- FORM PENGERJAAN -->
            <form method="POST" id="formLatihan">

                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-slate-800">
                        Kerjakan Soal
                    </h2>

                    <span class="text-sm text-slate-500" id="progressText">
                        0 dari <?= $totalSoal ?> dijawab
                    </span>
                </div>

                <div class="w-full h-2 bg-slate-200 rounded-full mb-6 overflow-hidden">
                    <div id="progressBar"
                         class="h-full bg-blue-600 rounded-full transition-all"
                         style="width: 0%">
                    </div>
                </div>

                <div class="space-y-5">
                    <?php foreach ($daftarSoal as $index => $soal):
                        $opsi = [
                            "A" => $soal["opsi_a"],
                            "B" => $soal["opsi_b"],
                            "C" => $soal["opsi_c"],
                            "D" => $soal["opsi_d"],
                            "E" => $soal["opsi_e"]
                        ];
                    ?>
                        <article class="bg-white border border-slate-200 rounded-2xl p-5">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-bold text-blue-700 bg-blue-50 px-3 py-1.5 rounded-full">
                                    Soal <?= $index + 1 ?>
                                </span>

                                <span class="text-xs text-slate-400">
                                    Pilihan Ganda
                                </span>
                            </div>

                            <p class="text-slate-800 font-medium leading-relaxed mb-5">
                                <?= nl2br(htmlspecialchars($soal["pertanyaan"])) ?>
                            </p>

                            <div class="space-y-3">
                                <?php foreach ($opsi as $huruf => $isi):
                                    if ($isi === null || trim($isi) === "") continue;
                                ?>
                                    <label class="flex items-start gap-3 border border-slate-200 rounded-xl p-3 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                                        <input
                                            type="radio"
                                            name="jawaban[<?= (int)$soal["id"] ?>]"
                                            value="<?= $huruf ?>"
                                            class="mt-1 accent-blue-600"
                                        >

                                        <span class="text-sm text-slate-700">
                                            <strong class="mr-1"><?= $huruf ?>.</strong>
                                            <?= htmlspecialchars($isi) ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 mt-6">
                    <p class="text-sm text-slate-600 mb-4">
                        Pastikan seluruh soal sudah dijawab sebelum mengakhiri latihan.
                        Setelah dikirim, nilai dan pembahasan akan ditampilkan.
                    </p>

                    <button type="submit"
                            name="selesai"
                            value="1"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition">
                        Selesaikan Latihan
                    </button>
                </div>
            </form>

        <?php endif; ?>

    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formLatihan");

    if (!form) return;

    const radios = form.querySelectorAll('input[type="radio"]');
    const progressText = document.getElementById("progressText");
    const progressBar = document.getElementById("progressBar");

    const totalSoal = <?= (int)$totalSoal ?>;

    function updateProgress() {
        const terjawab = new Set();

        radios.forEach(function (radio) {
            if (radio.checked) {
                terjawab.add(radio.name);
            }
        });

        const jumlah = terjawab.size;
        const persen = totalSoal > 0
            ? (jumlah / totalSoal) * 100
            : 0;

        progressText.textContent = jumlah + " dari " + totalSoal + " dijawab";
        progressBar.style.width = persen + "%";
    }

    radios.forEach(function (radio) {
        radio.addEventListener("change", updateProgress);
    });

    form.addEventListener("submit", function (event) {
        const terjawab = new Set();

        radios.forEach(function (radio) {
            if (radio.checked) {
                terjawab.add(radio.name);
            }
        });

        if (terjawab.size < totalSoal) {
            event.preventDefault();

            alert(
                "Masih ada " + (totalSoal - terjawab.size) +
                " soal yang belum dijawab. Silakan periksa kembali."
            );

            return;
        }

        const yakin = confirm(
            "Apakah kamu yakin ingin menyelesaikan seluruh latihan soal?"
        );

        if (!yakin) {
            event.preventDefault();
        }
    });
});
</script>

<?php require_once "includes/footer.php"; ?>
