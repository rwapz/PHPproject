<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

// Get film name from the query string
$filmname = isset($_GET['filmname']) ? htmlspecialchars($_GET['filmname']) : 'unknown film';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Confirmation</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/index1.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Cineplex - Delete Confirmation</h1>
            <nav>
                <ul>
                    <li><a href="films.php">Home</a></li>
                    <li><a href="upcomingfilms.php">Upcoming Films</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="admin-container" style="text-align:center;">
            <h2>Success!</h2>
            <p>All bookings for <strong><?php echo $filmname; ?></strong> have been successfully deleted.</p>
            <a href="view_bookings.php" class="btn">Return to View Bookings</a>
        </div>
    </main>

    <footer>
        <p>© <?php echo date("Y"); ?> Cineplex. All rights reserved.</p>
    </footer>
</body>
</html>