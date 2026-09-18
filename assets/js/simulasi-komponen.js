document.addEventListener("DOMContentLoaded", function () {
    var canvas = document.getElementById("simCanvas");
    if (!canvas) return;
    var ctx = canvas.getContext("2d");
    var W = canvas.width;
    var H = canvas.height;

    var elDayung = document.getElementById("dayung");
    var elArus = document.getElementById("arus");
    var dayungVal = document.getElementById("dayungVal");
    var arusVal = document.getElementById("arusVal");
    var vyVal = document.getElementById("vyVal");
    var vxVal = document.getElementById("vxVal");
    var vVal = document.getElementById("vVal");
    var thetaVal = document.getElementById("thetaVal");

    // Geometri sungai
    var tepiAtas = 60;      // garis tepi jauh
    var tepiBawah = H - 60; // garis tepi dekat
    var startX = W / 2;

    // Kecepatan piksel per detik (biar animasinya enak dilihat)
    var SKALA_JALAN = 55;

    var perahu = { x: startX, y: tepiBawah };
    var jejak = [];      // titik-titik jalur perahu
    var waktuJeda = 0;   // jeda sebelum perahu menyeberang lagi

    function gambarPanah(x1, y1, x2, y2, warna, tebal) {
        var sudut = Math.atan2(y2 - y1, x2 - x1);
        ctx.strokeStyle = warna;
        ctx.fillStyle = warna;
        ctx.lineWidth = tebal || 3;
        ctx.lineCap = "round";

        ctx.beginPath();
        ctx.moveTo(x1, y1);
        ctx.lineTo(x2, y2);
        ctx.stroke();

        var ukuran = 10;
        ctx.beginPath();
        ctx.moveTo(x2, y2);
        ctx.lineTo(x2 - ukuran * Math.cos(sudut - Math.PI / 6), y2 - ukuran * Math.sin(sudut - Math.PI / 6));
        ctx.lineTo(x2 - ukuran * Math.cos(sudut + Math.PI / 6), y2 - ukuran * Math.sin(sudut + Math.PI / 6));
        ctx.closePath();
        ctx.fill();
    }

    function gambarPerahu(x, y) {
        // Badan perahu
        ctx.fillStyle = "#3F7FD9";
        ctx.strokeStyle = "#173A70";
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        ctx.moveTo(x - 28, y);
        ctx.lineTo(x - 18, y + 14);
        ctx.lineTo(x + 18, y + 14);
        ctx.lineTo(x + 28, y);
        ctx.closePath();
        ctx.fill();
        ctx.stroke();

        // Orang yang mendayung
        ctx.fillStyle = "#173A70";
        ctx.beginPath();
        ctx.arc(x, y - 10, 7, 0, Math.PI * 2);
        ctx.fill();

        // Dayung
        ctx.strokeStyle = "#F8C95B";
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.moveTo(x - 16, y - 14);
        ctx.lineTo(x + 16, y + 6);
        ctx.stroke();
    }

    function resetPerahu() {
        perahu.x = startX;
        perahu.y = tepiBawah;
        jejak = [];
        waktuJeda = 0;
    }

    var waktuSebelumnya = performance.now();

    function gambar(sekarang) {
        var dt = (sekarang - waktuSebelumnya) / 1000;
        if (dt > 0.05) dt = 0.05; // biar stabil kalau tab tidak aktif
        waktuSebelumnya = sekarang;

        var vDayung = parseFloat(elDayung.value);
        var vArus = parseFloat(elArus.value);

        // Update label slider
        dayungVal.textContent = vDayung.toFixed(1) + " m/s";
        arusVal.textContent = vArus.toFixed(1) + " m/s";

        // Panel hasil: komponen vektor
        vyVal.textContent = vDayung.toFixed(1) + " m/s";
        vxVal.textContent = vArus.toFixed(1) + " m/s";
        var vTotal = Math.sqrt(vDayung * vDayung + vArus * vArus);
        vVal.textContent = vTotal.toFixed(1) + " m/s";
        var sudut = Math.atan2(vArus, vDayung) * 180 / Math.PI;
        thetaVal.textContent = sudut.toFixed(0) + "°";

        // Gerakkan perahu (atas = menyeberang, kanan = terseret arus)
        if (waktuJeda > 0) {
            waktuJeda -= dt;
        } else {
            perahu.y -= vDayung * SKALA_JALAN * dt;
            perahu.x += vArus * SKALA_JALAN * dt;
            jejak.push({ x: perahu.x, y: perahu.y });

            // Sampai tepi seberang: tandai mendarat, jeda, ulangi
            if (perahu.y <= tepiAtas) {
                perahu.y = tepiAtas;
                jejak.push({ x: perahu.x, y: perahu.y });
                waktuJeda = 1.5;
                setTimeout(resetPerahu, 1500);
            }
        }

        // Bersihkan kanvas
        ctx.clearRect(0, 0, W, H);

        // Daratan atas dan bawah
        ctx.fillStyle = "#E9E4FF";
        ctx.fillRect(0, 0, W, tepiAtas);
        ctx.fillRect(0, tepiBawah + 20, W, H - tepiBawah - 20);

        // Air sungai
        ctx.fillStyle = "#DCEEFF";
        ctx.fillRect(0, tepiAtas, W, tepiBawah - tepiAtas + 20);

        // Garis arus kecil (visual arus mengalir)
        ctx.strokeStyle = "#3F7FD9";
        ctx.lineWidth = 2;
        var t = sekarang / 1000;
        for (var i = 0; i < 5; i++) {
            var garisY = tepiAtas + 50 + i * 60;
            var geser = (t * 40 * (vArus + 0.5)) % 60;
            for (var gx = -60 + geser; gx < W; gx += 90) {
                ctx.beginPath();
                ctx.moveTo(gx, garisY);
                ctx.lineTo(gx + 30, garisY);
                ctx.lineTo(gx + 24, garisY - 5);
                ctx.moveTo(gx + 30, garisY);
                ctx.lineTo(gx + 24, garisY + 5);
                ctx.stroke();
            }
        }

        // Tepi sungai
        ctx.strokeStyle = "#243247";
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(0, tepiAtas);
        ctx.lineTo(W, tepiAtas);
        ctx.moveTo(0, tepiBawah + 20);
        ctx.lineTo(W, tepiBawah + 20);
        ctx.stroke();

        // Titik berangkat (penanda)
        ctx.fillStyle = "#243247";
        ctx.font = "12px Inter, sans-serif";
        ctx.fillText("Berangkat", startX - 26, tepiBawah + 40);

        // Jalur perahu (jejak putus-putus)
        if (jejak.length > 1) {
            ctx.strokeStyle = "#243247";
            ctx.lineWidth = 2;
            ctx.setLineDash([5, 5]);
            ctx.beginPath();
            ctx.moveTo(startX, tepiBawah);
            for (var j = 0; j < jejak.length; j++) {
                ctx.lineTo(jejak[j].x, jejak[j].y);
            }
            ctx.stroke();
            ctx.setLineDash([]);

            // Titik mendarat (titik terakhir jejak kalau sudah sampai)
            var akhir = jejak[jejak.length - 1];
            if (akhir.y <= tepiAtas + 1) {
                ctx.fillStyle = "#173A70";
                ctx.beginPath();
                ctx.arc(akhir.x, tepiAtas, 6, 0, Math.PI * 2);
                ctx.fill();
                ctx.fillStyle = "#243247";
                ctx.fillText("Mendarat di sini", akhir.x - 40, tepiAtas - 12);
            }
        }

        // Titik awal di tepi dekat
        ctx.fillStyle = "#3F7FD9";
        ctx.beginPath();
        ctx.arc(startX, tepiBawah, 6, 0, Math.PI * 2);
        ctx.fill();

        // Perahu
        gambarPerahu(perahu.x, perahu.y);

        // Vektor kecepatan dari perahu
        var P = { x: perahu.x, y: perahu.y - 10 };
        var SKALA_V = 30; // piksel per m/s untuk panah vektor
        var ujungY = { x: P.x, y: P.y - vDayung * SKALA_V };
        var ujungX = { x: P.x + vArus * SKALA_V, y: P.y };
        var ujungR = { x: P.x + vArus * SKALA_V, y: P.y - vDayung * SKALA_V };

        // Segitiga komponen putus-putus
        ctx.strokeStyle = "#243247";
        ctx.lineWidth = 1.5;
        ctx.setLineDash([5, 4]);
        ctx.beginPath();
        ctx.moveTo(ujungY.x, ujungY.y);
        ctx.lineTo(ujungR.x, ujungR.y);
        ctx.moveTo(ujungX.x, ujungX.y);
        ctx.lineTo(ujungR.x, ujungR.y);
        ctx.stroke();
        ctx.setLineDash([]);

        gambarPanah(P.x, P.y, ujungY.x, ujungY.y, "#2E9E5B", 4);
        gambarPanah(P.x, P.y, ujungX.x, ujungX.y, "#3F7FD9", 4);
        gambarPanah(P.x, P.y, ujungR.x, ujungR.y, "#173A70", 4);

        // Label
        ctx.font = "italic bold 15px Georgia";
        ctx.fillStyle = "#2E9E5B";
        ctx.fillText("v dayung", ujungY.x - 70, ujungY.y + 4);
        ctx.fillStyle = "#3F7FD9";
        ctx.fillText("v arus", ujungX.x + 12, ujungX.y + 4);
        ctx.fillStyle = "#173A70";
        ctx.fillText("v total", ujungR.x + 12, ujungR.y - 6);

        requestAnimationFrame(gambar);
    }

    requestAnimationFrame(gambar);
});