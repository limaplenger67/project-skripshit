<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

function kirimJson($data, $status = 200)
{
    http_response_code($status);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}


// ==========================================
// VALIDASI REQUEST
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    kirimJson([
        "success" => false,
        "message" => "Metode request tidak diizinkan."
    ], 405);

}

$pesan = trim($_POST["pesan"] ?? "");

$materiId = isset($_POST["materi_id"]) &&
    is_numeric($_POST["materi_id"])
        ? (int) $_POST["materi_id"]
        : 0;


if ($pesan === "") {

    kirimJson([
        "success" => false,
        "message" => "Pertanyaan tidak boleh kosong."
    ], 400);

}

if (mb_strlen($pesan) > 3000) {

    kirimJson([
        "success" => false,
        "message" => "Pertanyaan terlalu panjang. Maksimal 3000 karakter."
    ], 400);

}


// ==========================================
// AMBIL KONTEKS MATERI
// ==========================================

$materi = null;

if ($materiId > 0) {

    $stmtMateri = mysqli_prepare(
        $conn,
        "SELECT id, judul, kelas
         FROM materi
         WHERE id = ?
         AND status_publikasi = 'publik'
         LIMIT 1"
    );

    if ($stmtMateri) {

        mysqli_stmt_bind_param(
            $stmtMateri,
            "i",
            $materiId
        );

        mysqli_stmt_execute($stmtMateri);

        $materi = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmtMateri)
        );

        mysqli_stmt_close($stmtMateri);

    }

}


if (!$materi) {

    $materiId = 0;
    $materiJudul = "Fisika umum";
    $materiKelas = "SMA";

} else {

    $materiJudul = trim($materi["judul"]);
    $materiKelas = trim((string) $materi["kelas"]);

}


// ==========================================
// INSTRUKSI KHUSUS BERDASARKAN MATERI
// ==========================================

if (stripos($materiJudul, "vektor") !== false) {

    $fokusMateri = <<<TEXT
KONTEKS UTAMA: VEKTOR KELAS 10 SMA.

Prioritaskan pembahasan konsep vektor, besaran skalar dan vektor, notasi, penggambaran vektor, sifat vektor, komponen vektor, penguraian dengan trigonometri, penjumlahan dan pengurangan vektor, resultan, aturan sinus dan kosinus, serta perkalian titik dan silang.

Gunakan tingkat penjelasan yang sesuai untuk siswa kelas 10 SMA.
TEXT;

} elseif (
    stripos($materiJudul, "dinamika rotasi") !== false ||
    stripos($materiJudul, "rotasi") !== false
) {

    $fokusMateri = <<<TEXT
KONTEKS UTAMA: DINAMIKA ROTASI KELAS 11 SMA.

Prioritaskan pembahasan gerak rotasi, momen gaya atau torsi, momen kopel, momen inersia, hukum Newton pada gerak rotasi, momentum sudut, usaha, energi, dan daya pada gerak rotasi.

Gunakan tingkat penjelasan yang sesuai untuk siswa kelas 11 SMA.

Jangan mencampuradukkan rumus dinamika rotasi dengan rumus vektor. Jika konsep vektor diperlukan untuk menjelaskan torsi atau besaran lain, jelaskan keterkaitannya secara tepat.
TEXT;

} else {

    $fokusMateri = <<<TEXT
KONTEKS UTAMA: PENDAMPING BELAJAR FISIKA SMA SECARA UMUM.

Jawab pertanyaan Fisika dari berbagai topik SMA, misalnya mekanika, gerak, gaya, energi, momentum, fluida, suhu dan kalor, gelombang, bunyi, cahaya, listrik, dan magnet.

Sesuaikan tingkat penjelasan dengan kelas siswa jika diketahui.
TEXT;

}


// ==========================================
// SYSTEM INSTRUCTION PHYBOT
// ==========================================

$systemInstruction = <<<TEXT
Kamu adalah PhyBot, asisten pembelajaran Fisika pada website PhyZone untuk siswa SMA.

$fokusMateri

ATURAN PERAN:

1. Kamu boleh menjawab pertanyaan Fisika SMA dari berbagai topik, bukan hanya materi utama halaman ini.
2. Jika siswa bertanya tentang materi utama yang sedang dipelajari, berikan penjelasan yang lebih terarah dan mendalam.
3. Jika siswa bertanya tentang topik Fisika lain, tetap bantu menjawab selama masih berkaitan dengan pembelajaran Fisika.
4. Jika pertanyaan tidak berkaitan dengan Fisika atau pembelajaran, jelaskan dengan sopan bahwa kamu adalah pendamping belajar Fisika PhyZone.
5. Jangan mengarang fakta, data, satuan, atau rumus. Jika informasi kurang, sampaikan asumsi yang digunakan.
6. Gunakan bahasa Indonesia yang ramah, jelas, dan sesuai tingkat siswa SMA.

ATURAN MENYELESAIKAN SOAL:

1. Tuliskan informasi yang diketahui.
2. Tuliskan hal yang ditanyakan.
3. Tuliskan rumus yang sesuai.
4. Jelaskan arti simbol yang digunakan jika diperlukan.
5. Tunjukkan substitusi angka dan langkah perhitungan secara runtut.
6. Tuliskan hasil akhir beserta satuan dan arah jika diperlukan.
7. Jangan hanya memberikan jawaban akhir.
8. Periksa konsistensi satuan dan tanda positif-negatif.

ATURAN PENULISAN RUMUS:

1. Semua rumus matematika harus ditulis menggunakan LaTeX.
2. Rumus di dalam kalimat gunakan delimiter \( ... \).
3. Rumus yang berdiri sendiri gunakan delimiter \[ ... \] dan tulis pada baris tersendiri.
4. Contoh penulisan komponen vektor: \( A_x = A \cos \theta \) dan \( A_y = A \sin \theta \).
5. Contoh rumus torsi: \( \tau = r F \sin \theta \).
6. Gunakan \cdot untuk perkalian jika diperlukan, \frac untuk pecahan, \sqrt untuk akar, dan \mathrm{} untuk satuan atau keterangan teks di dalam rumus.
7. Jangan menulis rumus dalam code block atau membungkusnya dengan tanda backtick.
8. Pastikan setiap delimiter rumus dibuka dan ditutup dengan benar.

ATURAN FORMAT JAWABAN:

1. Gunakan paragraf dan baris baru agar jawaban mudah dibaca.
2. Gunakan judul sederhana jika diperlukan.
3. Boleh menggunakan daftar bernomor untuk langkah penyelesaian.
4. Hindari tabel yang terlalu lebar.
5. Jangan gunakan simbol dekoratif secara berlebihan.
6. Jangan menuliskan jawaban dalam format JSON atau HTML.
TEXT;


// ==========================================
// RIWAYAT CHAT TERPISAH PER MATERI
// ==========================================

$sessionHistoryKey = "riwayat_phybot_materi_" .
    ($materiId > 0 ? $materiId : "umum");

if (
    !isset($_SESSION[$sessionHistoryKey]) ||
    !is_array($_SESSION[$sessionHistoryKey])
) {

    $_SESSION[$sessionHistoryKey] = [];

}

$contents = array_slice(
    $_SESSION[$sessionHistoryKey],
    -10
);

$contents[] = [
    "role" => "user",
    "parts" => [
        ["text" => $pesan]
    ]
];


// ==========================================
// KONFIGURASI GEMINI
// ==========================================

$configAI = require "config/ai.php";

if (
    empty($configAI["api_key"]) ||
    empty($configAI["api_url"])
) {

    kirimJson([
        "success" => false,
        "message" => "Konfigurasi API Gemini belum lengkap. Periksa config/ai.php."
    ], 500);

}


// ==========================================
// PAYLOAD GEMINI
// ==========================================

$payload = [

    "systemInstruction" => [
        "parts" => [
            ["text" => $systemInstruction]
        ]
    ],

    "contents" => $contents,

    "generationConfig" => [
        "temperature" => 0.6,
        "maxOutputTokens" => 3000
    ]

];


// ==========================================
// KIRIM REQUEST KE GEMINI
// ==========================================

$ch = curl_init($configAI["api_url"]);

curl_setopt_array($ch, [

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_POST => true,

    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "x-goog-api-key: " . $configAI["api_key"]
    ],

    CURLOPT_POSTFIELDS => json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    ),

    CURLOPT_CONNECTTIMEOUT => 15,

    CURLOPT_TIMEOUT => 60

]);


$response = curl_exec($ch);

$curlError = curl_error($ch);

$statusCode = (int) curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);

curl_close($ch);


// ==========================================
// PERIKSA KONEKSI
// ==========================================

if ($response === false) {

    kirimJson([
        "success" => false,
        "message" => "Gagal menghubungi layanan AI. " . $curlError
    ], 500);

}


$result = json_decode($response, true);


// ==========================================
// PERIKSA ERROR GEMINI
// ==========================================

if ($statusCode < 200 || $statusCode >= 300) {

    $pesanError = $result["error"]["message"]
        ?? "Terjadi kesalahan saat menghubungi Gemini.";

    kirimJson([
        "success" => false,
        "message" => $pesanError
    ], 500);

}


// ==========================================
// AMBIL JAWABAN
// ==========================================

$jawaban = "";

foreach (
    ($result["candidates"][0]["content"]["parts"] ?? [])
    as $part
) {

    if (isset($part["text"])) {
        $jawaban .= $part["text"];
    }

}

$jawaban = trim($jawaban);


if ($jawaban === "") {

    kirimJson([
        "success" => false,
        "message" => "PhyBot belum dapat memberikan jawaban. Silakan coba lagi."
    ], 502);

}


// ==========================================
// SIMPAN RIWAYAT PER MATERI
// ==========================================

$riwayatBaru = $contents;

$riwayatBaru[] = [
    "role" => "model",
    "parts" => [
        ["text" => $jawaban]
    ]
];

$_SESSION[$sessionHistoryKey] = array_slice(
    $riwayatBaru,
    -12
);


// ==========================================
// KIRIM JAWABAN KE CHAT
// ==========================================

kirimJson([
    "success" => true,
    "jawaban" => $jawaban,
    "materi" => $materiJudul
]);