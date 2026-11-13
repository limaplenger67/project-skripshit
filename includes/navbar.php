
<?php
// Pastikan session dan autentikasi tersedia
if (!isset($isLoggedIn)) {
    require_once __DIR__ . "/auth.php";
}

// Daftar navigasi
$navItems = [
    [
        "nama" => "Beranda",
        "link" => "index.php"
    ],
    [
        "nama" => "Materi",
        "link" => "materi.php"
    ],
    [
        "nama" => "Riwayat Latihan",
        "link" => "riwayat_latihan.php"
    ],
    [
        "nama" => "Chat AI",
        "link" => "chat.php"
    ],
    [
        "nama" => "Tentang Kami",
        "link" => "index.php#tentang"
    ]
];

// Menentukan halaman aktif
$currentPage = basename($_SERVER["PHP_SELF"]);
?>

<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between items-center h-20">

            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-700 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-xl">P</span>
                </div>

                <div>
                    <h1 class="text-xl font-bold text-[#173A70]">
                        PhyZone
                    </h1>

                    <p class="text-[10px] text-gray-500 -mt-1">
                        Platform Pembelajaran Fisika SMA
                    </p>
                </div>
            </a>

            <!-- Navigasi Desktop -->
            <div class="hidden lg:flex items-center gap-1">

                <?php foreach ($navItems as $item): ?>

                    <?php
                    $isActive = (
                        $currentPage === $item["link"] ||
                        (
                            $item["nama"] === "Tentang Kami" &&
                            $currentPage === "index.php" &&
                            isset($_GET["section"])
                        )
                    );
                    ?>

                    <a href="<?= htmlspecialchars($item["link"], ENT_QUOTES, "UTF-8") ?>"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition
                       <?= $isActive
                           ? 'text-blue-700 bg-blue-50'
                           : 'text-gray-600 hover:text-blue-700 hover:bg-blue-50' ?>">
                        <?= htmlspecialchars($item["nama"], ENT_QUOTES, "UTF-8") ?>
                    </a>

                <?php endforeach; ?>

            </div>

            <!-- Akun Desktop -->
            <div class="hidden lg:flex items-center gap-3">

                <?php if ($isLoggedIn): ?>

                    <span class="text-sm text-gray-600">
                        Halo,
                        <span class="font-semibold text-[#173A70]">
                            <?= htmlspecialchars($siswaNama, ENT_QUOTES, "UTF-8") ?>
                        </span>
                    </span>

                    <a href="logout.php"
                       class="bg-[#173A70] hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                        Keluar
                    </a>

                <?php else: ?>

                    <a href="login.php"
                       class="text-sm font-semibold text-[#173A70] hover:text-blue-700 transition">
                        Masuk
                    </a>

                    <a href="register.php"
                       class="bg-[#173A70] hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                        Daftar
                    </a>

                <?php endif; ?>

            </div>

            <!-- Tombol Menu Mobile -->
            <button id="menu-btn"
                    type="button"
                    class="lg:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition"
                    aria-label="Buka menu navigasi">

                <svg id="icon-menu"
                     xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M4 6h16M4 12h16M4 18h16"/>

                </svg>

                <svg id="icon-close"
                     xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6 hidden"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M6 18L18 6M6 6l12 12"/>

                </svg>

            </button>

        </div>

        <!-- Navigasi Mobile -->
        <div id="mobile-menu" class="hidden lg:hidden pb-5">

            <div class="flex flex-col gap-1 border-t border-gray-100 pt-3">

                <?php foreach ($navItems as $item): ?>

                    <?php
                    $isActive = ($currentPage === $item["link"]);
                    ?>

                    <a href="<?= htmlspecialchars($item["link"], ENT_QUOTES, "UTF-8") ?>"
                       class="nav-link-mobile px-4 py-3 rounded-lg text-sm font-medium transition
                       <?= $isActive
                           ? 'text-blue-700 bg-blue-50'
                           : 'text-gray-600 hover:text-blue-700 hover:bg-blue-50' ?>">

                        <?= htmlspecialchars($item["nama"], ENT_QUOTES, "UTF-8") ?>

                    </a>

                <?php endforeach; ?>

            </div>

            <!-- Akun Mobile -->
            <div class="border-t border-gray-100 mt-3 pt-4">

                <?php if ($isLoggedIn): ?>

                    <div class="px-4 mb-3">
                        <p class="text-xs text-gray-500">
                            Sedang masuk sebagai
                        </p>

                        <p class="font-semibold text-[#173A70] mt-1">
                            <?= htmlspecialchars($siswaNama, ENT_QUOTES, "UTF-8") ?>
                        </p>
                    </div>

                    <a href="logout.php"
                       class="block mx-4 text-center bg-[#173A70] hover:bg-blue-800 text-white text-sm font-semibold px-5 py-3 rounded-xl transition">

                        Keluar

                    </a>

                <?php else: ?>

                    <div class="flex gap-3 px-4">

                        <a href="login.php"
                           class="flex-1 text-center border border-[#173A70] text-[#173A70] hover:bg-blue-50 text-sm font-semibold px-4 py-3 rounded-xl transition">

                            Masuk

                        </a>

                        <a href="register.php"
                           class="flex-1 text-center bg-[#173A70] hover:bg-blue-800 text-white text-sm font-semibold px-4 py-3 rounded-xl transition">

                            Daftar

                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>
</nav>