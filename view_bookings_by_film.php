<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("location:index.php");
    exit;
} else {
    include "connect.php";
    $username = $_SESSION['username'];

    $query = $db->prepare("SELECT * FROM user WHERE username=?");
    $query->execute([$username]);
    $control = $query->fetch(PDO::FETCH_ASSOC);

    // Redirect if the user is not an admin
    if ($control['admin'] != 1) {
        header("Location:films.php");
        exit;
    }
}

// Check if the film ID is provided in the query string
$filmid = isset($_GET['filmid']) ? intval($_GET['filmid']) : 0;

// Fetch bookings for the given film ID
$bookingsQuery = $db->prepare("SELECT bookings.*, films.name AS film_name FROM bookings JOIN films ON bookings.filmsid = films.ID WHERE films.ID = ?");
$bookingsQuery->execute([$filmid]);

// Fetch the film name for display
$filmNameQuery = $db->prepare("SELECT name FROM films WHERE ID = ?");
$filmNameQuery->execute([$filmid]);
$filmDetails = $filmNameQuery->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cineplex - View Bookings for Film</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/index1.css"> 
</head>
<body>
    <header>
        <div class="container">
            <h1>Cineplex - View Bookings for Film</h1>
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
        <div class="admin-container">
            <h4>Welcome <?php echo htmlspecialchars($username); ?></h4>
            <h3>Bookings for Film: 
                <?php 
                echo isset($filmDetails['name']) ? htmlspecialchars($filmDetails['name']) : 'Unknown Film';
                ?>
            </h3>

            <?php 
            $bookingsQuery->execute([$filmid]); 

            if ($bookingsQuery->rowCount() > 0): ?>
                <table>
                    <tr>
                        <th>Booking ID</th>
                        <th>User ID</th>
                        <th>Tickets</th>
                    </tr>
                    <?php while ($row = $bookingsQuery->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['userid']); ?></td>
                            <td><?php echo htmlspecialchars($row['tickets']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No bookings found for this film.</p>
            <?php endif; ?>
            <a href="view_booking_grouped.php" class="btn">Return to Screening Bookings</a><br>
            <a href="view_bookings.php">Return to  All Bookings</a>
        </div>
    </main>

    <footer>
        <p>© <?php echo date("Y"); ?> Cineplex. All rights reserved.</p>
    </footer>
</body>
</html>