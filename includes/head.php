<?php
$pageTitle = isset($pageTitle) ? $pageTitle : "PhyZone";
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $pageTitle; ?> | PhyZone</title>
    <meta name="description" content="PhyZone adalah platform pembelajaran fisika untuk siswa SMA dengan materi terstruktur, visualisasi, latihan soal, dan chatbot AI." />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: "#173A70",
                        blue: {
                            DEFAULT: "#3F7FD9"
                        },
                        lightblue: "#DCEEFF",
                        lavender: "#E9E4FF",
                        warmwhite: "#FAF9F6",
                        softyellow: "#F8C95B",
                        darktext: "#243247"
                    },
                    fontFamily: {
                        heading: ["Poppins", "sans-serif"],
                        body: ["Inter", "sans-serif"]
                    }
                }
            }
        };
    </script>

    <link rel="stylesheet" href="assets/css/style.css" />
</head>