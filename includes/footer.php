<?php
$footerLinks = [
    ["label" => "Beranda", "href" => "#beranda"],
    ["label" => "Materi", "href" => "#materi"],
    ["label" => "Chat AI", "href" => "#chat-ai"],
    ["label" => "Tentang Kami", "href" => "#tentang"]
];
?>
    <footer class="bg-navy py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex flex-col items-center gap-8 md:flex-row md:justify-between">
                <div class="text-center md:text-left">
                    <a href="#beranda" class="inline-flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-warmwhite/10 text-warmwhite">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><path d="M20.2 20.2c2.04-2.03.02-7.36-4.5-11.9-4.54-4.52-9.87-6.54-11.9-4.5-2.04 2.03-.02 7.36 4.5 11.9 4.54 4.52 9.87 6.54 11.9 4.5Z"/><path d="M15.7 15.7c4.52-4.54 6.54-9.87 4.5-11.9-2.03-2.04-7.36-.02-11.9 4.5-4.52 4.54-6.54 9.87-4.5 11.9 2.03 2.04 7.36.02 11.9-4.5Z"/></svg>
                        </span>
                        <span class="font-heading text-xl font-semibold text-warmwhite">PhyZoneee</span>
                    </a>
                    <p class="mt-3 max-w-sm text-sm leading-relaxed text-warmwhite/70">
                        Platform pembelajaran fisika untuk siswa SMA dengan materi terstruktur, visualisasi, latihan soal, dan chatbot AI.
                    </p>
                </div>

                <nav aria-label="Navigasi footer">
                    <ul class="flex flex-wrap items-center justify-center gap-x-7 gap-y-3 md:justify-end">
                        <?php foreach ($footerLinks as $link): ?>
                            <li>
                                <a href="<?php echo $link['href']; ?>" class="text-sm font-medium text-warmwhite/80 transition-colors hover:text-warmwhite">
                                    <?php echo $link['label']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>

            <div class="mt-10 border-t border-warmwhite/15 pt-6 text-center">
                <p class="text-sm text-warmwhite/60">
                    &copy; <?php echo date("Y"); ?> PhyZone. Seluruh hak cipta dilindungi.
                </p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>