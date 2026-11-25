<?php
$pageTitle = "PhyBot - Asisten Belajar AI";

require_once "config/database.php";
require_once "includes/auth.php";

requireLogin();

require_once "includes/head.php";
?>

<?php require_once "includes/navbar.php"; ?>

<main class="min-h-screen bg-[#F5F8FD] px-4 py-6 md:py-8">

    <div class="max-w-5xl mx-auto">

        <!-- HEADER PHYBOT -->
        <div class="bg-white rounded-2xl border border-blue-100 p-5 md:p-6 mb-5 shadow-sm">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-3xl">
                    🤖
                </div>

                <div class="flex-1">

                    <h1 class="text-xl md:text-2xl font-bold text-[#173A70]">
                        PhyBot
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Asisten Belajar Fisika PhyZone
                    </p>

                    <div class="flex items-center gap-2 mt-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="text-xs text-green-600 font-medium">
                            Siap membantu belajar
                        </span>
                    </div>

                </div>

            </div>

            <div class="mt-4 bg-blue-50 rounded-xl p-3">

                <p class="text-sm text-blue-800 leading-relaxed">
                    Hai! Aku PhyBot, teman belajarmu di PhyZone.
                    Yuk, tanyakan apa saja tentang materi Fisika.
                    Aku siap membantu menjelaskan konsep, rumus,
                    maupun latihan soal.
                </p>

            </div>

        </div>


        <!-- RUANG CHAT -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div
                id="chat-box"
                class="h-[430px] md:h-[500px] overflow-y-auto p-4 md:p-6 space-y-5"
            >

                <!-- PESAN PEMBUKA -->
                <div class="flex items-start gap-3">

                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                        🤖
                    </div>

                    <div class="max-w-[85%]">

                        <p class="text-xs text-gray-400 mb-1">
                            PhyBot
                        </p>

                        <div class="bg-blue-50 text-gray-700 rounded-2xl rounded-tl-sm px-4 py-3 text-sm leading-relaxed">

                            Halo, <?php echo htmlspecialchars($siswaNama, ENT_QUOTES, "UTF-8"); ?>! 👋

                            <br><br>

                            Senang bertemu denganmu. Ada materi Fisika yang ingin kamu pelajari hari ini?

                            Aku siap membantu menjelaskan konsep, rumus, maupun latihan soal.

                        </div>

                    </div>

                </div>

            </div>


            <!-- INDIKATOR MENGETIK -->
            <div
                id="typing-indicator"
                class="hidden px-5 pb-3"
            >

                <div class="flex items-center gap-3">

                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                        🤖
                    </div>

                    <div class="bg-gray-100 rounded-xl px-4 py-3 text-sm text-gray-500">
                        PhyBot sedang berpikir...
                    </div>

                </div>

            </div>


            <!-- CONTOH PERTANYAAN -->
            <div class="px-4 md:px-6 pb-3">

                <p class="text-xs font-semibold text-gray-400 mb-2">
                    CONTOH PERTANYAAN
                </p>

                <div class="flex gap-2 overflow-x-auto pb-2">

                    <button
                        type="button"
                        class="contoh-pertanyaan shrink-0 text-xs border border-blue-200 text-blue-700 rounded-full px-3 py-2 hover:bg-blue-50"
                    >
                        Apa itu vektor?
                    </button>

                    <button
                        type="button"
                        class="contoh-pertanyaan shrink-0 text-xs border border-blue-200 text-blue-700 rounded-full px-3 py-2 hover:bg-blue-50"
                    >
                        Bagaimana mencari komponen vektor?
                    </button>

                    <button
                        type="button"
                        class="contoh-pertanyaan shrink-0 text-xs border border-blue-200 text-blue-700 rounded-full px-3 py-2 hover:bg-blue-50"
                    >
                        Jelaskan penjumlahan vektor
                    </button>

                </div>

            </div>


            <!-- FORM PESAN -->
            <div class="border-t border-gray-100 p-4 md:p-5">

                <form id="form-chat" class="flex items-end gap-2 md:gap-3">

                    <textarea
                        id="pesan"
                        name="pesan"
                        rows="1"
                        maxlength="3000"
                        placeholder="Tulis pertanyaanmu di sini..."
                        class="flex-1 resize-none border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 min-h-[48px] max-h-32"
                        required
                    ></textarea>

                    <button
                        type="submit"
                        id="btn-kirim"
                        class="w-12 h-12 shrink-0 rounded-xl bg-[#3F7FD9] text-white flex items-center justify-center hover:bg-[#173A70] transition disabled:opacity-50"
                        title="Kirim pesan"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="21"
                            height="21"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="m22 2-7 20-4-9-9-4Z"/>
                            <path d="M22 2 11 13"/>
                        </svg>

                    </button>

                </form>

                <p class="text-center text-xs text-gray-400 mt-3">
                    PhyBot dapat melakukan kesalahan.
                    Periksa kembali informasi penting.
                </p>

            </div>

        </div>

    </div>

</main>


<!-- STYLE CHAT -->
<style>

#chat-box {
    scroll-behavior: smooth;
}

#pesan {
    line-height: 1.5;
}

/* Tampilan umum pesan */
.chat-pesan {
    white-space: normal;
    overflow-wrap: anywhere;
    line-height: 1.75;
    word-break: normal;
}

/* Judul jawaban */
.chat-pesan h1,
.chat-pesan h2,
.chat-pesan h3 {
    color: #173A70;
    font-weight: 700;
    line-height: 1.4;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.chat-pesan h1 {
    font-size: 1.2rem;
}

.chat-pesan h2 {
    font-size: 1.1rem;
}

.chat-pesan h3 {
    font-size: 1rem;
}

/* Paragraf */
.chat-pesan p {
    margin-bottom: 0.65rem;
}

.chat-pesan p:last-child {
    margin-bottom: 0;
}

/* Teks tebal */
.chat-pesan strong {
    color: #173A70;
    font-weight: 700;
}

/* Daftar */
.chat-pesan ul,
.chat-pesan ol {
    margin: 0.5rem 0 0.8rem 1.4rem;
    padding-left: 0.5rem;
}

.chat-pesan ul {
    list-style-type: disc;
}

.chat-pesan ol {
    list-style-type: decimal;
}

.chat-pesan li {
    margin-bottom: 0.35rem;
    padding-left: 0.2rem;
}

/* Garis pemisah */
.chat-pesan hr {
    border: 0;
    border-top: 1px solid #dbe3ef;
    margin: 1rem 0;
}

/* Rumus matematika */
.chat-pesan .rumus-display {
    display: block;
    max-width: 100%;
    overflow-x: auto;
    margin: 0.8rem 0;
    padding: 0.3rem 0;
    text-align: center;
}

.chat-pesan .rumus-inline {
    display: inline;
}

.chat-pesan mjx-container {
    max-width: 100%;
}

.chat-pesan mjx-container[display="true"] {
    overflow-x: auto;
    overflow-y: hidden;
    padding: 0.3rem 0;
}

/* Kode */
.chat-pesan pre {
    overflow-x: auto;
    padding: 0.8rem;
    margin: 0.6rem 0;
    border-radius: 0.6rem;
    background: #f1f5f9;
}

.chat-pesan code {
    overflow-wrap: anywhere;
    border-radius: 0.25rem;
    background: #f1f5f9;
    padding: 0.1rem 0.25rem;
}

</style>


<!-- LIBRARY MARKDOWN DAN KEAMANAN HTML -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.2.6/dist/purify.min.js"></script>


<!-- JAVASCRIPT CHATBOT -->
<script>

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("form-chat");
    const input = document.getElementById("pesan");
    const chatBox = document.getElementById("chat-box");
    const btnKirim = document.getElementById("btn-kirim");
    const typing = document.getElementById("typing-indicator");

    let sedangMengirim = false;


    // MENUNGGU MATHJAX SIAP
    async function tungguMathJax() {

        let percobaan = 0;

        while (
            (!window.MathJax || !window.MathJax.typesetPromise) &&
            percobaan < 50
        ) {
            await new Promise(resolve => setTimeout(resolve, 100));
            percobaan++;
        }

        if (window.MathJax && window.MathJax.startup) {
            try {
                await window.MathJax.startup.promise;
            } catch (error) {
                console.error("MathJax gagal dimuat:", error);
            }
        }

    }


    // MENGUBAH KARAKTER HTML MENJADI AMAN
    function escapeHTML(teks) {

        return String(teks).replace(/[&<>"']/g, function (karakter) {

            const daftarKarakter = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;"
            };

            return daftarKarakter[karakter];

        });

    }


    // MERENDER MARKDOWN DAN RUMUS LATEX
    async function isiJawabanBot(bubble, teks) {

        const daftarRumus = [];

        /*
         * Rumus LaTeX diamankan terlebih dahulu
         * agar tanda matematika tidak rusak oleh Markdown.
         */
        const teksTanpaRumus = String(teks).replace(
            /\\\[([\s\S]*?)\\\]|\\\(([\s\S]*?)\\\)/g,
            function (cocok, rumusBlok, rumusInline) {

                const indeks = daftarRumus.length;

                daftarRumus.push({
                    blok: rumusBlok !== undefined,
                    isi: rumusBlok !== undefined
                        ? rumusBlok.trim()
                        : rumusInline.trim()
                });

                return "PHYZONEFORMULA" + indeks + "END";

            }
        );


        // PROSES MARKDOWN
        let html;

        if (window.marked) {

            html = window.marked.parse(teksTanpaRumus, {
                gfm: true,
                breaks: true
            });

        } else {

            html = escapeHTML(teksTanpaRumus)
                .replace(/\n/g, "<br>");

        }


        // KEMBALIKAN RUMUS LATEX
        daftarRumus.forEach(function (rumus, indeks) {

            const kelas = rumus.blok
                ? "rumus-display"
                : "rumus-inline";

            const delimiter = rumus.blok
                ? "\\[" + escapeHTML(rumus.isi) + "\\]"
                : "\\(" + escapeHTML(rumus.isi) + "\\)";

            const pengganti =
                '<span class="' + kelas + '">' +
                delimiter +
                '</span>';

            html = html.replace(
                "PHYZONEFORMULA" + indeks + "END",
                pengganti
            );

        });


        // AMANKAN HTML HASIL MARKDOWN
        if (window.DOMPurify) {

            html = window.DOMPurify.sanitize(html, {
                USE_PROFILES: {
                    html: true
                }
            });

        }


        // TAMPILKAN JAWABAN
        bubble.innerHTML = html;


        // RENDER RUMUS MENGGUNAKAN MATHJAX
        await tungguMathJax();

        if (window.MathJax && window.MathJax.typesetPromise) {

            try {

                window.MathJax.typesetClear([bubble]);

                await window.MathJax.typesetPromise([bubble]);

            } catch (error) {

                console.error("Gagal merender rumus:", error);

            }

        }

    }


    // MENAMBAHKAN PESAN KE RUANG CHAT
    function tambahPesan(teks, pengirim) {

        const wrapper = document.createElement("div");

        wrapper.className = "flex items-start gap-3";

        if (pengirim === "user") {
            wrapper.classList.add("justify-end");
        }

        const avatar = document.createElement("div");

        avatar.className =
            "w-9 h-9 rounded-full flex items-center justify-center shrink-0 " +
            (pengirim === "user" ? "bg-yellow-100" : "bg-blue-100");

        avatar.textContent =
            pengirim === "user" ? "👤" : "🤖";


        const kolom = document.createElement("div");

        kolom.className = "max-w-[85%] min-w-0";


        const nama = document.createElement("p");

        nama.className = "text-xs text-gray-400 mb-1";

        nama.textContent =
            pengirim === "user" ? "Kamu" : "PhyBot";


        const bubble = document.createElement("div");

        bubble.className =
            "chat-pesan rounded-2xl px-4 py-3 text-sm " +
            (pengirim === "user"
                ? "bg-[#3F7FD9] text-white rounded-tr-sm"
                : "bg-blue-50 text-gray-700 rounded-tl-sm");


        // PESAN PENGGUNA TETAP TEKS BIASA
        if (pengirim === "user") {

            bubble.textContent = teks;

        } else {

            // JAWABAN PHYBOT DIPROSES MARKDOWN DAN LATEX
            isiJawabanBot(bubble, teks);

        }


        kolom.appendChild(nama);
        kolom.appendChild(bubble);


        if (pengirim === "user") {

            wrapper.appendChild(kolom);
            wrapper.appendChild(avatar);

        } else {

            wrapper.appendChild(avatar);
            wrapper.appendChild(kolom);

        }


        chatBox.appendChild(wrapper);

        chatBox.scrollTop = chatBox.scrollHeight;

    }


    // MENGIRIM PESAN KE PHYBOT
    async function kirimPesan(teks) {

        if (sedangMengirim || !teks.trim()) {
            return;
        }

        sedangMengirim = true;

        tambahPesan(teks, "user");

        input.value = "";
        input.style.height = "48px";

        btnKirim.disabled = true;

        typing.classList.remove("hidden");

        chatBox.scrollTop = chatBox.scrollHeight;


        const formData = new FormData();

        formData.append("pesan", teks);


        try {

            const response = await fetch("proses_chat.php", {
                method: "POST",
                body: formData
            });


            const data = await response.json();


            if (!response.ok || !data.success) {

                throw new Error(
                    data.message ||
                    "Terjadi kesalahan saat mengirim pesan."
                );

            }


            tambahPesan(data.jawaban, "bot");


        } catch (error) {

            tambahPesan(
                "Maaf, terjadi kendala saat menghubungi PhyBot.\n\n" +
                error.message +
                "\n\nSilakan coba lagi.",
                "bot"
            );

        } finally {

            sedangMengirim = false;

            btnKirim.disabled = false;

            typing.classList.add("hidden");

            input.focus();

        }

    }


    // FORM KIRIM PESAN
    form.addEventListener("submit", function (event) {

        event.preventDefault();

        const teks = input.value.trim();

        if (teks) {
            kirimPesan(teks);
        }

    });


    // ENTER UNTUK MENGIRIM PESAN
    input.addEventListener("keydown", function (event) {

        if (event.key === "Enter" && !event.shiftKey) {

            event.preventDefault();

            form.requestSubmit();

        }

    });


    // TINGGI TEXTAREA OTOMATIS
    input.addEventListener("input", function () {

        input.style.height = "48px";

        input.style.height =
            Math.min(input.scrollHeight, 128) + "px";

    });


    // TOMBOL CONTOH PERTANYAAN
    document.querySelectorAll(".contoh-pertanyaan").forEach(function (tombol) {

        tombol.addEventListener("click", function () {

            if (sedangMengirim) {
                return;
            }

            input.value = tombol.textContent.trim();

            kirimPesan(input.value);

        });

    });

});

</script>


<?php require_once "includes/footer.php"; ?>