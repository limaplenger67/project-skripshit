<!-- Simulasi: Gerobak Ditarik Dua Tali -->
<section class="mt-8 rounded-2xl border border-darktext/10 bg-white p-6 sm:p-8">
    <h2 class="font-heading text-lg font-bold text-navy">Simulasi Interaktif</h2>
    <p class="mt-1 text-sm text-darktext/70">
        Dua orang menarik gerobak dengan gaya dari arah berbeda. Atur besar gaya dan sudutnya, lalu perhatikan
        ke mana gerobak bergerak. Gerobak selalu bergerak searah resultan kedua gaya.
    </p>

    <!-- Kanvas -->
    <div class="mt-5 overflow-hidden rounded-xl border border-darktext/10 bg-lightblue/40">
        <canvas id="simCanvas" width="720" height="420" class="h-auto w-full"></canvas>
    </div>

    <!-- Kontrol -->
    <div class="mt-6 grid gap-5 sm:grid-cols-2">
        <div>
            <label class="mb-1 flex items-center justify-between text-sm font-medium text-darktext">
                <span>Gaya F1 (orang pertama)</span>
                <span id="f1Val" class="font-semibold text-navy">6 N</span>
            </label>
            <input id="f1" type="range" min="0" max="10" step="0.5" value="6"
                class="w-full accent-navy" />
            <label class="mb-1 mt-3 flex items-center justify-between text-sm font-medium text-darktext">
                <span>Sudut F1 terhadap horizontal</span>
                <span id="t1Val" class="font-semibold text-navy">30°</span>
            </label>
            <input id="t1" type="range" min="0" max="90" step="1" value="30"
                class="w-full accent-navy" />
        </div>
        <div>
            <label class="mb-1 flex items-center justify-between text-sm font-medium text-darktext">
                <span>Gaya F2 (orang kedua)</span>
                <span id="f2Val" class="font-semibold text-navy">4 N</span>
            </label>
            <input id="f2" type="range" min="0" max="10" step="0.5" value="4"
                class="w-full accent-navy" />
            <label class="mb-1 mt-3 flex items-center justify-between text-sm font-medium text-darktext">
                <span>Sudut F2 terhadap horizontal</span>
                <span id="t2Val" class="font-semibold text-navy">20°</span>
            </label>
            <input id="t2" type="range" min="0" max="90" step="1" value="20"
                class="w-full accent-navy" />
        </div>
    </div>

    <!-- Panel hasil -->
    <div class="mt-6 grid gap-3 rounded-xl bg-warmwhite p-4 text-center sm:grid-cols-4">
        <div>
            <p class="text-xs font-medium text-darktext/60">Komponen X</p>
            <p id="rxVal" class="mt-1 font-heading text-lg font-bold text-navy">0 N</p>
        </div>
        <div>
            <p class="text-xs font-medium text-darktext/60">Komponen Y</p>
            <p id="ryVal" class="mt-1 font-heading text-lg font-bold text-navy">0 N</p>
        </div>
        <div>
            <p class="text-xs font-medium text-darktext/60">Resultan (R)</p>
            <p id="rVal" class="mt-1 font-heading text-lg font-bold text-navy">0 N</p>
        </div>
        <div>
            <p class="text-xs font-medium text-darktext/60">Arah Resultan</p>
            <p id="thetaVal" class="mt-1 font-heading text-lg font-bold text-navy">0°</p>
        </div>
    </div>

    <p class="mt-4 text-xs text-darktext/60">
        Keterangan: F1 digambar biru ke atas, F2 digambar kuning ke bawah. Garis putus-putus menunjukkan
        jajargenjang, dan panah navy adalah resultannya (R = F1 + F2).
    </p>
</section>

<script src="assets/js/simulasi-penjumlahan.js"></script>