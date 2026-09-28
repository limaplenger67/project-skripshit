// Simulasi: Jalan Berbelok, Jarak vs Perpindahan
document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById("canvasJarak");
    const ctx = canvas.getContext("2d");
    const W = canvas.width;
    const H = canvas.height;

    // Konversi grid logika (satuan blok, 1 blok = 10 m) ke piksel
    const CELL = 56;
    const OX = 88;
    const OY = 372;

    function px(p) {
        return { x: OX + p[0] * CELL, y: OY - p[1] * CELL };
    }

    // Titik penting (grid x ke kanan, y ke atas)
    const RUMAH = [0, 0];
    const TOKO = [4, 0];
    const TAMAN = [4, 3];
    const SEKOLAH = [8, 5];

    // Dua jalur, masing-masing daftar titik belok
    const JALUR = {
        A: [RUMAH, TOKO, [4, 3], SEKOLAH],           // rumah → toko → belok → sekolah (14 blok)
        B: [RUMAH, [0, 3], TAMAN, [8, 3], SEKOLAH]   // memutar lewat taman (22 blok)
    };

    const SKALA_M = 10; // 1 blok = 10 meter

    // State
    let jalurAktif = "A";
    let animasi = null;   // { segmen, t, jarakTempuh }
    let selesai = false;

    // Elemen UI
    const btnJalurA = document.getElementById("btnJalurA");
    const btnJalurB = document.getElementById("btnJalurB");
    const btnJalan = document.getElementById("btnJalan");
    const btnUlangi = document.getElementById("btnUlangi");
    const statusJalan = document.getElementById("statusJalan");
    const jarakVal = document.getElementById("jarakVal");
    const perpindahanVal = document.getElementById("perpindahanVal");
    const arahVal = document.getElementById("arahVal");

    // Hitung panjang total jalur (dalam blok)
    function panjangJalur(titik) {
        let total = 0;
        for (let i = 0; i < titik.length - 1; i++) {
            total += Math.hypot(titik[i + 1][0] - titik[i][0], titik[i + 1][1] - titik[i][1]);
        }
        return total;
    }

    // Perpindahan: garis lurus posisi awal ke akhir (selalu sama berapa pun jalurnya)
    function hitungPerpindahan() {
        const dx = SEKOLAH[0] - RUMAH[0];
        const dy = SEKOLAH[1] - RUMAH[1];
        const besar = Math.hypot(dx, dy) * SKALA_M;
        const arah = Math.atan2(dy, dx) * 180 / Math.PI; // terhadap sumbu x positif
        return { besar, arah };
    }

    // Gambar peta: jalan, gedung, jalur, dan pejalan
    function gambar() {
        ctx.clearRect(0, 0, W, H);

        // Latar
        ctx.fillStyle = "#DCEEFF";
        ctx.fillRect(0, 0, W, H);

        // Grid jalan (garis putus-putus halus)
        ctx.strokeStyle = "rgba(23, 58, 112, 0.10)";
        ctx.lineWidth = 1;
        for (let gx = 0; gx <= 10; gx++) {
            const a = px([gx, 0]);
            const b = px([gx, 6]);
            ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
        }
        for (let gy = 0; gy <= 6; gy++) {
            const a = px([0, gy]);
            const b = px([10, gy]);
            ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
        }

        // Jalur aktif (garis tebal navy)
        const titik = JALUR[jalurAktif].map(px);
        ctx.strokeStyle = "#173A70";
        ctx.lineWidth = 5;
        ctx.lineJoin = "round";
        ctx.lineCap = "round";
        ctx.beginPath();
        titik.forEach((p, i) => (i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y)));
        ctx.stroke();

        // Garis perpindahan (putus-putus merah, selalu sama)
        const awal = px(RUMAH);
        const akhir = px(SEKOLAH);
        ctx.strokeStyle = "#D9534F";
        ctx.lineWidth = 3;
        ctx.setLineDash([8, 6]);
        ctx.beginPath();
        ctx.moveTo(awal.x, awal.y);
        ctx.lineTo(akhir.x, akhir.y);
        ctx.stroke();
        ctx.setLineDash([]);

        // Label perpindahan
        ctx.fillStyle = "#D9534F";
        ctx.font = "bold 12px Inter, sans-serif";
        ctx.textAlign = "center";
        ctx.fillText("perpindahan", (awal.x + akhir.x) / 2, (awal.y + akhir.y) / 2 - 10);

        // Tempat
        gambarTempat("Rumah", RUMAH, "#3F7FD9");
        gambarTempat("Toko", TOKO, "#F8C95B");
        gambarTempat("Taman", TAMAN, "#9BC98B");
        gambarTempat("Sekolah", SEKOLAH, "#173A70");

        // Pejalan kaki
        let posisi = awal;
        if (animasi) {
            posisi = posisiDiJalur(JALUR[jalurAktif], animasi.jarakBlok);
        }
        ctx.fillStyle = "#243247";
        ctx.beginPath();
        ctx.arc(posisi.x, posisi.y, 9, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = "#FAF9F6";
        ctx.beginPath();
        ctx.arc(posisi.x, posisi.y, 4, 0, Math.PI * 2);
        ctx.fill();
    }

    function gambarTempat(nama, grid, warna) {
        const p = px(grid);
        ctx.fillStyle = warna;
        ctx.beginPath();
        ctx.roundRect(p.x - 34, p.y - 20, 68, 40, 8);
        ctx.fill();
        ctx.fillStyle = "#FAF9F6";
        ctx.font = "bold 12px Poppins, sans-serif";
        ctx.textAlign = "center";
        ctx.fillText(nama, p.x, p.y + 4);
    }

    // Posisi pejalan pada jarak tertentu (dalam blok) sepanjang jalur
    function posisiDiJalur(titikGrid, jarakBlok) {
        let sisa = jarakBlok;
        for (let i = 0; i < titikGrid.length - 1; i++) {
            const a = titikGrid[i];
            const b = titikGrid[i + 1];
            const seg = Math.hypot(b[0] - a[0], b[1] - a[1]);
            if (sisa <= seg) {
                const t = seg === 0 ? 0 : sisa / seg;
                return px([a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t]);
            }
            sisa -= seg;
        }
        return px(titikGrid[titikGrid.length - 1]);
    }

    // Update panel angka
    function updatePanel(jarakMeter) {
        const perp = hitungPerpindahan();
        jarakVal.textContent = (jarakMeter !== null ? jarakMeter : 0) + " m";
        perpindahanVal.textContent = perp.besar.toFixed(1) + " m";
        arahVal.textContent = perp.arah.toFixed(0) + "° dari timur";
    }

    // Animasi jalan (kecepatan 2 blok per detik, 1 blok = 10 m)
    const KECEPATAN_BLOK = 2;

    function mulaiJalan() {
        if (animasi) return;
        const totalBlok = panjangJalur(JALUR[jalurAktif]);
        animasi = { jarakBlok: 0, totalBlok };
        selesai = false;
        btnJalan.disabled = true;
        btnJalan.classList.add("opacity-50");
        statusJalan.textContent = "Sedang berjalan...";
        updatePanel(0);

        let terakhir = performance.now();
        function langkah(now) {
            const dt = (now - terakhir) / 1000;
            terakhir = now;
            animasi.jarakBlok = Math.min(animasi.jarakBlok + KECEPATAN_BLOK * dt, animasi.totalBlok);
            updatePanel(Math.round(animasi.jarakBlok * SKALA_M));
            gambar();
            if (animasi.jarakBlok < animasi.totalBlok) {
                requestAnimationFrame(langkah);
            } else {
                selesai = true;
                animasi = null;
                btnJalan.disabled = false;
                btnJalan.classList.remove("opacity-50");
                statusJalan.textContent = "Sampai! Ganti jalur lalu bandingkan hasilnya.";
            }
        }
        requestAnimationFrame(langkah);
    }

    function ulangi() {
        animasi = null;
        selesai = false;
        btnJalan.disabled = false;
        btnJalan.classList.remove("opacity-50");
        statusJalan.textContent = "";
        updatePanel(null);
        gambar();
    }

    function pilihJalur(jalur) {
        jalurAktif = jalur;
        ulangi();
        const aktif = "rounded-full border-2 border-navy bg-navy px-5 py-2 text-sm font-semibold text-warmwhite transition-colors";
        const nonaktif = "rounded-full border-2 border-navy/20 px-5 py-2 text-sm font-semibold text-navy transition-colors hover:border-navy";
        btnJalurA.className = jalur === "A" ? aktif : nonaktif;
        btnJalurB.className = jalur === "B" ? aktif : nonaktif;
        gambar();
    }

    btnJalurA.addEventListener("click", () => pilihJalur("A"));
    btnJalurB.addEventListener("click", () => pilihJalur("B"));
    btnJalan.addEventListener("click", mulaiJalan);
    btnUlangi.addEventListener("click", ulangi);

    updatePanel(null);
    gambar();
});