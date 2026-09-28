<!-- Simulasi: Jalan Berbelok, Jarak vs Perpindahan -->
<section class="mt-8 rounded-2xl border border-darktext/10 bg-white p-6 sm:p-8">
    <h2 class="font-heading text-lg font-bold text-navy">Simulasi Interaktif</h2>
    <p class="mt-1 text-sm text-darktext/70">
        Seseorang berjalan dari rumah ke sekolah melewati jalur yang berbeda. Pilih jalurnya, tekan tombol
        jalan, lalu bandingkan jarak tempuh dengan perpindahannya. Perhatikan: walau jalurnya beda-beda,
        perpindahannya selalu sama.
    </p>

    <!-- Kanvas -->
    <div class="mt-5 overflow-hidden rounded-xl border border-darktext/10 bg-lightblue/40">
        <canvas id="canvasJarak" width="720" height="420" class="h-auto w-full"></canvas>
    </div>

    <!-- Kontrol -->
    <div class="mt-6 flex flex-wrap items-center gap-3">
        <button id="btnJalurA" class="rounded-full border-2 border-navy bg-navy px-5 py-2 text-sm font-semibold text-warmwhite transition-colors">
            Jalur A, lewat toko
        </button>
        <button id="btnJalurB" class="rounded-full border-2 border-navy/20 px-5 py-2 text-sm font-semibold text-navy transition-colors hover:border-navy">
            Jalur B, memutar lewat taman
        </button>
        <button id="btnJalan" class="inline-flex items-center gap-2 rounded-full bg-softyellow px-5 py-2 text-sm font-semibold text-darktext transition-colors hover:opacity-90">
            Mulai Jalan
        </button>
        <button id="btnUlangi" class="rounded-full border-2 border-darktext/15 px-5 py-2 text-sm font-semibold text-darktext/70 transition-colors hover:border-darktext/40">
            Ulangi
        </button>
        <span id="statusJalan" class="text-sm font-medium text-darktext/60"></span>
    </div>

    <!-- Panel hasil -->
    <div class="mt-6 grid gap-3 rounded-xl bg-warmwhite p-4 text-center sm:grid-cols-3">
        <div>
            <p class="text-xs font-medium text-darktext/60">Jarak Tempuh (besaran skalar)</p>
            <p id="jarakVal" class="mt-1 font-heading text-lg font-bold text-navy">0 m</p>
        </div>
        <div>
            <p class="text-xs font-medium text-darktext/60">Perpindahan (besaran vektor)</p>
            <p id="perpindahanVal" class="mt-1 font-heading text-lg font-bold text-navy">0 m</p>
        </div>
        <div>
            <p class="text-xs font-medium text-darktext/60">Arah Perpindahan</p>
            <p id="arahVal" class="mt-1 font-heading text-lg font-bold text-navy">0°</p>
        </div>
    </div>

    <p class="mt-4 text-xs text-darktext/60">
        Keterangan: jarak tempuh adalah total panjang jalur yang dilalui, sedangkan perpindahan adalah garis
        lurus dari posisi awal ke posisi akhir beserta arahnya. Coba ganti jalur: jarak tempuh berubah,
        tetapi perpindahan dari rumah ke sekolah tidak pernah berubah.
    </p>
</section>

<script src="assets/js/simulasi-jarak-perpindahan.js"></script>