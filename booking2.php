<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;  // Stop script execution after redirection
}

include "connect.php"; // Ensure this file connects to your database

// Initialize variables for booking information
$bookingMessage = '';
$totalCost = 0;
$filmDetails = null;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid = $_POST['userid']; // Get the user ID safely
    $filmid = $_POST['filmid']; // Get the film ID
    $tickets = $_POST['tickets']; // Get tickets from form

    // Prepare and execute the booking insert statement
    $stmt = $db->prepare("INSERT INTO bookings (userid, filmsid, tickets) VALUES (?, ?, ?)");

    if ($stmt->execute([$userid, $filmid, $tickets])) {
        // Booking Success
        $bookingMessage = "Booking Success!<br>";

        // Fetch film details
        $query = $db->prepare("SELECT * FROM films WHERE ID = ?");
        $query->execute([$filmid]);
        $filmDetails = $query->fetch(PDO::FETCH_ASSOC);

        // Fetch film name and cost calculation
        if ($filmDetails) {
            $filmname = htmlspecialchars($filmDetails['name']); // Sanitize output
            $totalCost = $tickets * 4.99;  // Assuming each ticket costs £4.99

            // Prepare final messages
            $bookingMessage .= "You have booked $tickets ticket(s) for <strong>$filmname</strong>.<br>";
            $bookingMessage .= "The total cost is £" . number_format($totalCost, 2) . ".";
        } else {
            $bookingMessage .= "No film found with that ID.";
        }
    } else {
        $bookingMessage = 'Query Error: ' . $stmt->errorInfo()[2]; // Provide error feedback
    }
} else {
    header("location:films.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/index1.css"> <!-- Unified styles -->
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css">
    <style>
        .film-details {
            display: flex; /* Use flexbox for layout */
            margin-top: 20px; /* Spacing above */
        }
        .film-poster {
            max-width: 300px; /* Set max width for image */
            margin-right: 20px; /* Space between image and text */
            border-radius: 8px; /* Rounded corners for image */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5); /* Optional shadow for image */
        }
        .booking-info {
            flex-grow: 1; /* Allow text section to take remaining space */
            line-height: 1.6; /* Consistent line height */
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Cineplex - Booking Confirmation</h1>
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

<!-- <div class="container">
    <div class="welcome">
        <h4>Hello <?php echo htmlspecialchars($_SESSION['username']); ?></h4>
    </div> -->

    <div>
        <h2>Booking Details</h2>
        <p class="booking-message"><?php echo $bookingMessage; ?></p>

        <?php if ($filmDetails): ?>
            <div class="film-details">
                <!-- Display the booked film's poster -->
                <?php
                $posterFileName = strtolower(str_replace(' ', '', $filmDetails['name'])) . '.jpg'; // Assuming JPG format
                ?>
                <img src='images/<?php echo $posterFileName; ?>' alt='<?php echo htmlspecialchars($filmDetails['name']); ?>' class='film-poster'>

                <div class="booking-info">
                    <h4>Film: <?php echo htmlspecialchars($filmDetails['name']); ?></h4>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($filmDetails['description']); ?></p>
                    <p><strong>Number of Tickets:</strong> <?php echo $tickets; ?></p>
                    <p><strong>Total Cost:</strong> £<?php echo number_format($totalCost, 2); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<footer>
    <p>&copy; 2023 Cineplex. All rights reserved.</p>
</footer>
</body>
</html>