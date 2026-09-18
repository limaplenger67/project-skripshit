<!-- Simulasi: Perahu Menyeberang Sungai -->
<section class="mt-8 rounded-2xl border border-darktext/10 bg-white p-6 sm:p-8">
    <h2 class="font-heading text-lg font-bold text-navy">Simulasi Interaktif</h2>
    <p class="mt-1 text-sm text-darktext/70">
        Perahu mendayung lurus ke seberang sungai, tetapi arus mendorongnya ke kanan. Atur kecepatan dayung
        dan kecepatan arus, lalu perhatikan jalur resultan dan titik mendaratnya.
    </p>

    <!-- Kanvas -->
    <div class="mt-5 overflow-hidden rounded-xl border border-darktext/10 bg-lightblue/40">
        <canvas id="simCanvas" width="720" height="420" class="h-auto w-full"></canvas>
    </div>

    <!-- Kontrol -->
    <div class="mt-6 grid gap-5 sm:grid-cols-2">
        <div>
            <label class="mb-1 flex items-center justify-between text-sm font-medium text-darktext">
                <span>Kecepatan dayung (v perahu)</span>
                <span id="dayungVal" class="font-semibold text-navy">2 m/s</span>
            </label>
            <input id="dayung" type="range" min="0" max="5" step="0.1" value="2"
                class="w-full accent-navy" />
            <p class="mt-1 text-xs text-darktext/60">Kecepatan perahu terhadap air, arahnya selalu lurus menyeberang.</p>
        </div>
        <div>
            <label class="mb-1 flex items-center justify-between text-sm font-medium text-darktext">
                <span>Kecepatan arus (v arus)</span>
                <span id="arusVal" class="font-semibold text-navy">1 m/s</span>
            </label>
            <input id="arus" type="range" min="0" max="3" step="0.1" value="1"
                class="w-full accent-navy" />
            <p class="mt-1 text-xs text-darktext/60">Kecepatan air sungai, arahnya sepanjang sungai ke kanan.</p>
        </div>
    </div>

    <!-- Panel hasil -->
    <div class="mt-6 grid gap-3 rounded-xl bg-warmwhite p-4 text-center sm:grid-cols-4">
        <div>
            <p class="text-xs font-medium text-darktext/60">Komponen Y (menyeberang)</p>
            <p id="vyVal" class="mt-1 font-heading text-lg font-bold text-navy">0 m/s</p>
        </div>
        <div>
            <p class="text-xs font-medium text-darktext/60">Komponen X (terseret arus)</p>
            <p id="vxVal" class="mt-1 font-heading text-lg font-bold text-navy">0 m/s</p>
        </div>
        <div>
            <p class="text-xs font-medium text-darktext/60">Resultan (v total)</p>
            <p id="vVal" class="mt-1 font-heading text-lg font-bold text-navy">0 m/s</p>
        </div>
        <div>
            <p class="text-xs font-medium text-darktext/60">Arah Resultan</p>
            <p id="thetaVal" class="mt-1 font-heading text-lg font-bold text-navy">0°</p>
        </div>
    </div>

    <p class="mt-4 text-xs text-darktext/60">
        Keterangan: panah hijau adalah kecepatan dayung (sumbu Y), panah biru adalah arus (sumbu X),
        dan panah navy adalah kecepatan resultan yang benar-benar dialami perahu.
    </p>
</section>

<script src="assets/js/simulasi-komponen.js"></script>