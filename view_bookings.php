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

// Fetch bookings from the database
$query = $db->query("SELECT bookings.*, films.name AS film_name FROM bookings JOIN films ON bookings.filmsid = films.ID");

// Define a static ticket price (update this value as needed)
$ticketPrice = 4.99; // Assuming each ticket costs £4.99
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cineplex - View Bookings</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/index1.css"> 
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css"> 
</head>
<body>
    <header>
        <div class="container">
            <h1>Cineplex - Admin View Bookings!</h1>
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
            <h3>View Bookings</h3>
            <a href="view_booking_grouped.php">Showings Bookings</a>

            <?php if ($query->rowCount() > 0): ?>
                <table>
                    <tr>
                        <th>Booking ID</th>
                        <th>Film Name</th>
                        <th>User ID</th>
                        <th>Tickets</th>
                        <th>Total Spent (£)</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['film_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['userid']); ?></td>
                            <td><?php echo htmlspecialchars($row['tickets']); ?></td>
                            <td>
                                <?php 
                                // Calculate the total spent by the user on this booking using a predefined ticket price
                                $totalSpent = $row['tickets'] * $ticketPrice;
                                echo number_format($totalSpent, 2); // Format the total price to 2 decimal places
                                ?>
                            </td>
                            <td>
                                <form action="delete_bookings.php" method="post" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete all bookings for <?php echo htmlspecialchars($row['film_name']); ?>? This action cannot be undone.');">
                                    <input type="hidden" name="filmid" value="<?php echo $row['filmsid']; ?>">
                                    <button type="submit">Delete All Bookings for This Film</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No bookings found.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>© <?php echo date("Y"); ?> Cineplex. All rights reserved.</p>
    </footer>
</body>
</html>