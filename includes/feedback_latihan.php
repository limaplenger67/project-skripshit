
<?php

function getFeedbackLatihan($nilai) {

    $nilai = (float) $nilai;

    if ($nilai >= 80) {
        return [
            "kategori" => "Sangat Baik!",
            "pesan" => "Hebat! Kamu sudah menunjukkan pemahaman yang sangat baik terhadap materi Vektor.",
            "saran" => "Pertahankan hasil belajarmu dan cobalah soal dengan tingkat kesulitan yang lebih tinggi.",
            "warna" => "green"
        ];

    } elseif ($nilai >= 60) {
        return [
            "kategori" => "Cukup Baik!",
            "pesan" => "Kamu sudah memahami sebagian besar materi, tetapi masih ada beberapa konsep yang perlu diperkuat.",
            "saran" => "Pelajari kembali soal yang salah dan pahami pembahasannya sebelum mencoba latihan berikutnya.",
            "warna" => "yellow"
        ];

    } else {
        return [
            "kategori" => "Perlu Belajar Lagi",
            "pesan" => "Jangan menyerah! Kamu masih memiliki kesempatan untuk meningkatkan pemahaman materi Vektor.",
            "saran" => "Baca kembali materi pembelajaran, perhatikan contoh soal, lalu kerjakan latihan sekali lagi.",
            "warna" => "red"
        ];
    }
}
