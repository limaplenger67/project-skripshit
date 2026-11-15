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
                    Yuk, tanyakan apa saja tentang materi Fisika,
                    khususnya materi Vektor kelas 10 SMA.
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

                            Senang bertemu denganmu. Ada materi Vektor yang ingin kamu pelajari hari ini?

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

.chat-pesan {
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

</style>


<!-- JAVASCRIPT CHATBOT -->
<script>

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("form-chat");
    const input = document.getElementById("pesan");
    const chatBox = document.getElementById("chat-box");
    const btnKirim = document.getElementById("btn-kirim");
    const typing = document.getElementById("typing-indicator");

    let sedangMengirim = false;


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

        kolom.className = "max-w-[85%]";


        const nama = document.createElement("p");

        nama.className = "text-xs text-gray-400 mb-1";

        nama.textContent =
            pengirim === "user" ? "Kamu" : "PhyBot";


        const bubble = document.createElement("div");

        bubble.className =
            "chat-pesan rounded-2xl px-4 py-3 text-sm leading-relaxed " +
            (pengirim === "user"
                ? "bg-[#3F7FD9] text-white rounded-tr-sm"
                : "bg-blue-50 text-gray-700 rounded-tl-sm");

        bubble.textContent = teks;


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


    // MENGIRIM PESAN KE GEMINI
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