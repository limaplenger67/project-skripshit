<?php
$pageTitle = "Daftar Akun";
require_once "config/database.php";
require_once "includes/auth.php";

// Kalau sudah login, langsung ke daftar materi
if ($isLoggedIn) {
    header("Location: materi.php");
    exit;
}

$error = "";
$namaValue = "";
$usernameValue = "";
$emailValue = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama     = trim($_POST["nama"]);
    $username = trim($_POST["username"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $konfirmasi = $_POST["konfirmasi_password"];

    $namaValue = $nama;
    $usernameValue = $username;
    $emailValue = $email;

    // Validasi
    if ($nama === "" || $username === "" || $email === "" || $password === "") {
        $error = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak sama.";
    } else {
        // Cek username dan email sudah dipakai atau belum
        $stmt = mysqli_prepare($conn, "SELECT id FROM siswa WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username atau email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmtInsert = mysqli_prepare($conn, "INSERT INTO siswa (nama, username, email, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmtInsert, "ssss", $nama, $username, $email, $hash);

            if (mysqli_stmt_execute($stmtInsert)) {
                setFlash("sukses", "Pendaftaran berhasil. Silakan masuk dengan akun barumu.");
                header("Location: login.php");
                exit;
            } else {
                $error = "Terjadi kesalahan. Coba lagi.";
            }
            mysqli_stmt_close($stmtInsert);
        }
        mysqli_stmt_close($stmt);
    }
}

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main class="flex min-h-[70vh] items-center justify-center px-4 py-16">
    <div class="w-full max-w-md rounded-2xl border border-darktext/10 bg-white p-8 shadow-lg shadow-navy/10">

        <h1 class="text-center font-heading text-2xl font-bold text-navy">Buat Akun Baru</h1>
        <p class="mt-2 text-center text-sm text-darktext/70">
            Daftar gratis untuk mulai belajar di PhyZone.
        </p>

        <?php if ($error !== ""): ?>
            <div class="mt-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="mt-6 flex flex-col gap-4">
            <div>
                <label for="nama" class="mb-1 block text-sm font-medium text-darktext">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($namaValue); ?>" required
                    class="w-full rounded-xl border border-darktext/15 bg-warmwhite px-4 py-2.5 text-sm outline-none transition focus:border-navy focus:ring-2 focus:ring-navy/20" />
            </div>

            <div>
                <label for="username" class="mb-1 block text-sm font-medium text-darktext">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($usernameValue); ?>" required
                    class="w-full rounded-xl border border-darktext/15 bg-warmwhite px-4 py-2.5 text-sm outline-none transition focus:border-navy focus:ring-2 focus:ring-navy/20" />
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-darktext">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($emailValue); ?>" required
                    class="w-full rounded-xl border border-darktext/15 bg-warmwhite px-4 py-2.5 text-sm outline-none transition focus:border-navy focus:ring-2 focus:ring-navy/20" />
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-darktext">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full rounded-xl border border-darktext/15 bg-warmwhite px-4 py-2.5 text-sm outline-none transition focus:border-navy focus:ring-2 focus:ring-navy/20" />
            </div>

            <div>
                <label for="konfirmasi_password" class="mb-1 block text-sm font-medium text-darktext">Konfirmasi Password</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" required
                    class="w-full rounded-xl border border-darktext/15 bg-warmwhite px-4 py-2.5 text-sm outline-none transition focus:border-navy focus:ring-2 focus:ring-navy/20" />
            </div>

            <button type="submit"
                class="mt-2 rounded-full bg-navy px-6 py-3 text-sm font-semibold text-warmwhite transition-colors hover:bg-blue">
                Daftar
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-darktext/70">
            Sudah punya akun?
            <a href="login.php" class="font-semibold text-navy hover:underline">Masuk di sini</a>
        </p>
    </div>
</main>

<?php require_once "includes/footer.php"; ?>