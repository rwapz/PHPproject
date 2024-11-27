<?php
session_start();

// Toggle the posters enabled state
if (isset($_SESSION['posters_enabled'])) {
    $_SESSION['posters_enabled'] = !$_SESSION['posters_enabled'];
} else {
    $_SESSION['posters_enabled'] = true;
}

header("Location: posters.php");
exit();
?>