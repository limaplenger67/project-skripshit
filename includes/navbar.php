<?php
$navItems = [
    ["label" => "Beranda", "href" => "#beranda"],
    ["label" => "Materi", "href" => "#materi"],
    ["label" => "Chat AI", "href" => "#chat-ai"],
    ["label" => "Tentang Kami", "href" => "#tentang"]
];
?>
<header class="sticky top-0 z-50 border-b border-darktext/10 bg-warmwhite/95 backdrop-blur">
    <nav class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
        <a href="#beranda" class="flex items-center gap-2">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-navy text-warmwhite">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><path d="M20.2 20.2c2.04-2.03.02-7.36-4.5-11.9-4.54-4.52-9.87-6.54-11.9-4.5-2.04 2.03-.02 7.36 4.5 11.9 4.54 4.52 9.87 6.54 11.9 4.5Z"/><path d="M15.7 15.7c4.52-4.54 6.54-9.87 4.5-11.9-2.03-2.04-7.36-.02-11.9 4.5-4.52 4.54-6.54 9.87-4.5 11.9 2.03 2.04 7.36.02 11.9-4.5Z"/></svg>
            </span>
            <span class="font-heading text-xl font-semibold text-navy">PhyZone</span>
        </a>

        <ul class="hidden items-center gap-8 md:flex">
            <?php foreach ($navItems as $item): ?>
                <li>
                    <a href="<?php echo $item['href']; ?>" class="text-sm font-medium text-darktext/80 transition-colors hover:text-navy">
                        <?php echo $item['label']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <a href="#materi" class="hidden items-center gap-2 rounded-full bg-navy px-5 py-2.5 text-sm font-semibold text-warmwhite transition-colors hover:bg-blue md:inline-flex">
            Mulai Belajar
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>

        <button id="menu-btn" class="flex h-10 w-10 items-center justify-center rounded-lg text-navy md:hidden" aria-label="Buka menu navigasi">
            <svg id="icon-menu" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            <svg id="icon-close" class="hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
    </nav>

    <div id="mobile-menu" class="hidden border-t border-darktext/10 bg-warmwhite px-4 pb-4 pt-2 md:hidden">
        <ul class="flex flex-col gap-1">
            <?php foreach ($navItems as $item): ?>
                <li>
                    <a href="<?php echo $item['href']; ?>" class="nav-link-mobile block rounded-lg px-3 py-2.5 text-sm font-medium text-darktext hover:bg-lightblue">
                        <?php echo $item['label']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="#materi" class="nav-link-mobile mt-3 flex items-center justify-center gap-2 rounded-full bg-navy px-5 py-2.5 text-sm font-semibold text-warmwhite">
            Mulai Belajar
        </a>
    </div>
</header>