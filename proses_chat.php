
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

// Ambil konfigurasi Gemini
$configAI = require "config/ai.php";

// Pastikan request menggunakan POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Metode request tidak diizinkan."
    ]);
    exit;
}

// Ambil pesan siswa
$pesan = trim($_POST["pesan"] ?? "");

if ($pesan === "") {
    echo json_encode([
        "success" => false,
        "message" => "Pertanyaan tidak boleh kosong."
    ]);
    exit;
}

if (mb_strlen($pesan) > 3000) {
    echo json_encode([
        "success" => false,
        "message" => "Pertanyaan terlalu panjang. Maksimal 3000 karakter."
    ]);
    exit;
}

// Pastikan API key tersedia
if (empty($configAI["api_key"])) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Konfigurasi API Gemini belum lengkap."
    ]);
    exit;
}

// Instruksi khusus PhyBot
$systemInstruction = "
Kamu adalah PhyBot, asisten pembelajaran Fisika
pada website PhyZone untuk siswa SMA.

Fokus utama kamu adalah materi Vektor kelas 10 SMA.

Tugas kamu:
1. Menjelaskan konsep Fisika dengan bahasa sederhana.
2. Membantu siswa memahami lambang dan notasi vektor.
3. Menjelaskan cara menggambar dan sifat-sifat vektor.
4. Menjelaskan komponen dan representasi vektor.
5. Membantu operasi penjumlahan dan pengurangan vektor.
6. Membantu menyelesaikan soal secara bertahap.
7. Menjelaskan rumus beserta arti setiap simbol.
8. Memberikan contoh sederhana jika diperlukan.

Gunakan bahasa Indonesia yang ramah, sopan,
dan mudah dipahami siswa SMA.

Jika siswa bertanya soal hitungan:
- Tuliskan informasi yang diketahui.
- Tuliskan apa yang ditanyakan.
- Jelaskan rumus yang digunakan.
- Tunjukkan langkah penyelesaian.
- Berikan hasil akhir beserta satuannya jika ada.

Jangan hanya memberikan jawaban akhir.

Jika pertanyaan di luar pembelajaran Fisika,
jawab dengan sopan bahwa kamu adalah pendamping
belajar Fisika PhyZone.

Jangan mengarang informasi atau rumus.
";

// Riwayat percakapan
if (!isset($_SESSION["riwayat_phybot"])) {
    $_SESSION["riwayat_phybot"] = [];
}

// Susun isi percakapan sebelumnya
$contents = array_slice(
    $_SESSION["riwayat_phybot"],
    -10
);

// Tambahkan pertanyaan terbaru
$contents[] = [
    "role" => "user",
    "parts" => [
        ["text" => $pesan]
    ]
];

// Payload Gemini
$payload = [
    "systemInstruction" => [
        "parts" => [
            ["text" => $systemInstruction]
        ]
    ],
    "contents" => $contents,
    "generationConfig" => [
        "temperature" => 0.7,
        "maxOutputTokens" => 2048
    ]
];

// Kirim request ke Gemini
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
        JSON_UNESCAPED_UNICODE
    ),
    CURLOPT_TIMEOUT => 60
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// Periksa koneksi
if ($response === false) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Gagal menghubungi Gemini: " . $curlError
    ]);

    exit;
}

// Ubah respons menjadi array
$result = json_decode($response, true);

// Periksa error dari Gemini
if ($statusCode !== 200) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $result["error"]["message"]
            ?? "Terjadi kesalahan saat menghubungi Gemini."
    ]);

    exit;
}

// Ambil jawaban AI
$jawaban = "";

foreach (
    ($result["candidates"][0]["content"]["parts"] ?? [])
    as $part
) {
    if (isset($part["text"])) {
        $jawaban .= $part["text"];
    }
}

if ($jawaban === "") {
    echo json_encode([
        "success" => false,
        "message" => "PhyBot belum dapat memberikan jawaban. Silakan coba lagi."
    ]);

    exit;
}

// Simpan percakapan ke session
$_SESSION["riwayat_phybot"] = array_slice(
    $contents,
    -10
);

$_SESSION["riwayat_phybot"][] = [
    "role" => "model",
    "parts" => [
        ["text" => $jawaban]
    ]
];

// Kirim jawaban ke halaman chat
echo json_encode([
    "success" => true,
    "jawaban" => $jawaban
], JSON_UNESCAPED_UNICODE);

exit;