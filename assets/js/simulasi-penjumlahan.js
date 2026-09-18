document.addEventListener("DOMContentLoaded", function () {
    var canvas = document.getElementById("simCanvas");
    if (!canvas) return;
    var ctx = canvas.getContext("2d");
    var W = canvas.width;
    var H = canvas.height;

    var elF1 = document.getElementById("f1");
    var elF2 = document.getElementById("f2");
    var elT1 = document.getElementById("t1");
    var elT2 = document.getElementById("t2");

    var f1Val = document.getElementById("f1Val");
    var f2Val = document.getElementById("f2Val");
    var t1Val = document.getElementById("t1Val");
    var t2Val = document.getElementById("t2Val");
    var rxVal = document.getElementById("rxVal");
    var ryVal = document.getElementById("ryVal");
    var rVal = document.getElementById("rVal");
    var thetaVal = document.getElementById("thetaVal");

    var SKALA = 22; // piksel per newton
    var base = { x: 190, y: 270 }; // posisi dasar gerobak

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

    function gambarGerobak(x, y) {
        // Bak gerobak
        ctx.fillStyle = "#3F7FD9";
        ctx.strokeStyle = "#173A70";
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.rect(x - 55, y - 45, 110, 45);
        ctx.fill();
        ctx.stroke();

        // Dua roda
        ctx.fillStyle = "#173A70";
        ctx.beginPath();
        ctx.arc(x - 32, y + 8, 14, 0, Math.PI * 2);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(x + 32, y + 8, 14, 0, Math.PI * 2);
        ctx.fill();

        // Pusat roda kuning
        ctx.fillStyle = "#F8C95B";
        ctx.beginPath();
        ctx.arc(x - 32, y + 8, 5, 0, Math.PI * 2);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(x + 32, y + 8, 5, 0, Math.PI * 2);
        ctx.fill();
    }

    function gambar() {
        var F1 = parseFloat(elF1.value);
        var F2 = parseFloat(elF2.value);
        var t1 = parseFloat(elT1.value) * Math.PI / 180;
        var t2 = parseFloat(elT2.value) * Math.PI / 180;

        // Vektor dalam koordinat kanvas (y positif ke bawah)
        var v1 = { x: F1 * Math.cos(t1), y: -F1 * Math.sin(t1) };
        var v2 = { x: F2 * Math.cos(t2), y: F2 * Math.sin(t2) };
        var R = { x: v1.x + v2.x, y: v1.y + v2.y };

        // Update label slider
        f1Val.textContent = F1 + " N";
        f2Val.textContent = F2 + " N";
        t1Val.textContent = Math.round(parseFloat(elT1.value)) + "°";
        t2Val.textContent = Math.round(parseFloat(elT2.value)) + "°";

        // Update panel hasil
        rxVal.textContent = R.x.toFixed(1) + " N";
        ryVal.textContent = R.y.toFixed(1) + " N";
        var besarR = Math.sqrt(R.x * R.x + R.y * R.y);
        rVal.textContent = besarR.toFixed(1) + " N";
        var sudutR = Math.atan2(-R.y, R.x) * 180 / Math.PI;
        thetaVal.textContent = sudutR.toFixed(0) + "°";

        // Gerobak bergoyang searah resultan
        var t = performance.now() / 1000;
        var offset = { x: 0, y: 0 };
        if (besarR > 0.01) {
            var arah = { x: R.x / besarR, y: R.y / besarR };
            var goyang = 15 + 12 * Math.sin(t * 1.5);
            offset.x = arah.x * goyang;
            offset.y = arah.y * goyang;
        }
        var px = base.x + offset.x;
        var py = base.y + offset.y;

        // Bersihkan kanvas
        ctx.clearRect(0, 0, W, H);

        // Latar langit dan tanah
        ctx.fillStyle = "#DCEEFF";
        ctx.fillRect(0, 0, W, H - 70);
        ctx.fillStyle = "#E9E4FF";
        ctx.fillRect(0, H - 70, W, 70);

        // Garis tanah
        ctx.strokeStyle = "#243247";
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(0, H - 70);
        ctx.lineTo(W, H - 70);
        ctx.stroke();

        // Titik tangkap vektor (depan gerobak)
        var P = { x: px + 55, y: py - 22 };

        var ujung1 = { x: P.x + v1.x * SKALA, y: P.y + v1.y * SKALA };
        var ujung2 = { x: P.x + v2.x * SKALA, y: P.y + v2.y * SKALA };
        var ujungR = { x: P.x + R.x * SKALA, y: P.y + R.y * SKALA };

        // Jajargenjang (garis putus-putus)
        ctx.strokeStyle = "#243247";
        ctx.lineWidth = 1.5;
        ctx.setLineDash([6, 5]);
        ctx.beginPath();
        ctx.moveTo(ujung1.x, ujung1.y);
        ctx.lineTo(ujungR.x, ujungR.y);
        ctx.moveTo(ujung2.x, ujung2.y);
        ctx.lineTo(ujungR.x, ujungR.y);
        ctx.stroke();
        ctx.setLineDash([]);

        // Gambar gerobak (di atas garis putus-putus)
        gambarGerobak(px, py);

        // Vektor gaya
        gambarPanah(P.x, P.y, ujung1.x, ujung1.y, "#3F7FD9", 4);
        gambarPanah(P.x, P.y, ujung2.x, ujung2.y, "#E8A825", 4);
        gambarPanah(P.x, P.y, ujungR.x, ujungR.y, "#173A70", 4);

        // Label vektor
        ctx.fillStyle = "#3F7FD9";
        ctx.font = "italic bold 16px Georgia";
        ctx.fillText("F1", ujung1.x + 10, ujung1.y - 6);
        ctx.fillStyle = "#C79A2E";
        ctx.fillText("F2", ujung2.x + 10, ujung2.y + 16);
        ctx.fillStyle = "#173A70";
        ctx.fillText("R", ujungR.x + 10, ujungR.y + 4);

        requestAnimationFrame(gambar);
    }

    gambar();
});