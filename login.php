<?php
$pageTitle = "Masuk";
require_once "config/database.php";
require_once "includes/auth.php";

// Kalau sudah login, langsung ke daftar materi
if ($isLoggedIn) {
    header("Location: materi.php");
    exit;
}

$error = "";
$usernameValue = "";
$sukses = getFlash("sukses");
$errorFlash = getFlash("error");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $usernameValue = $username;

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, nama, password FROM siswa WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $siswa = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

                if (!$siswa) {
            $error = "Username belum terdaftar. Silakan daftar dulu lewat halaman Register.";
        } elseif (!password_verify($password, $siswa["password"])) {
            $error = "Password salah. Coba periksa kembali.";
        } else {
            // Login berhasil
            $_SESSION["siswa_id"] = $siswa["id"];
            $_SESSION["siswa_nama"] = $siswa["nama"];
            header("Location: materi.php");
            exit;
        }
    }
}

require_once "includes/head.php";
?>

<body class="min-h-screen bg-warmwhite font-body text-darktext">

<?php require_once "includes/navbar.php"; ?>

<main class="flex min-h-[70vh] items-center justify-center px-4 py-16">
    <div class="w-full max-w-md rounded-2xl border border-darktext/10 bg-white p-8 shadow-lg shadow-navy/10">

        <h1 class="text-center font-heading text-2xl font-bold text-navy">Selamat Datang Kembali</h1>
        <p class="mt-2 text-center text-sm text-darktext/70">
            Masuk untuk melanjutkan belajarmu.
        </p>

        <?php if ($sukses): ?>
            <div class="mt-6 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700">
                <?php echo $sukses; ?>
            </div>
        <?php endif; ?>

        <?php if ($errorFlash): ?>
            <div class="mt-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">
                <?php echo $errorFlash; ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="mt-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="mt-6 flex flex-col gap-4">
            <div>
                <label for="username" class="mb-1 block text-sm font-medium text-darktext">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($usernameValue); ?>" required
                    class="w-full rounded-xl border border-darktext/15 bg-warmwhite px-4 py-2.5 text-sm outline-none transition focus:border-navy focus:ring-2 focus:ring-navy/20" />
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-darktext">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full rounded-xl border border-darktext/15 bg-warmwhite px-4 py-2.5 text-sm outline-none transition focus:border-navy focus:ring-2 focus:ring-navy/20" />
            </div>

            <button type="submit"
                class="mt-2 rounded-full bg-navy px-6 py-3 text-sm font-semibold text-warmwhite transition-colors hover:bg-blue">
                Masuk
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-darktext/70">
            Belum punya akun?
            <a href="register.php" class="font-semibold text-navy hover:underline">Daftar di sini</a>
        </p>
    </div>
</main>

<?php require_once "includes/footer.php"; ?>