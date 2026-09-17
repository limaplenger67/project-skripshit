<?php
require_once "includes/auth.php";

// Hapus semua data session dan hancurkan session-nya
session_unset();
session_destroy();

header("Location: login.php");
exit;
?>