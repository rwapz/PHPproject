<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location:index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cineplex - Films</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/index1.css">
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css">
    
    <style>
        .poster {
            display: none;
            position: absolute;
            z-index: 100;
        }
        .movie-item {
            position: relative;
            display: inline-block;
            margin-right: 15px;
        }
        .movie-item:hover .poster {
            display: block;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Cineplex</h1>
            <nav>
                <ul>
                    <li><a href="films.php">Home</a></li>
                    <li><a href="new_food_drink.php">Menu</a></li>
                    <li><a href="upcomingfilms.php">Upcoming Films</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href='logout.php'>Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="welcome">
        <h2>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Login Success!</p>
    </div>

    <!-- Database Films Section -->
    <section class="films-list-section">
        <h2>All Films</h2>
        <table>
            <tr>
                <th>Film Name</th>
                <th>Description</th>
                <th>Action</th>
                <th><a href="admin.php">Admin page</a></th>
            </tr>
            <?php
            include "connect.php";

            // Check if posters are enabled
            $postersEnabled = isset($_SESSION['posters_enabled']) ? $_SESSION['posters_enabled'] : true;

            $query = $db->query("SELECT * FROM films");
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>";
                if ($postersEnabled) {
                    // Display the movie name with poster hover
                    echo "<div class='movie-item'>";
                    echo htmlspecialchars($row['name']);
                    // Assuming image filenames follow the same naming pattern as the film title
                    $posterFileName = strtolower(str_replace(' ', '', $row['name'])) . '.jpg'; // Changed to .jpg
                    echo "<img src='images/$posterFileName' alt='" . htmlspecialchars($row['name']) . "' class='poster' style='width: 200px;'>";
                    echo "</div>";
                } else {
                    // Just display the film name if posters are not enabled
                    echo htmlspecialchars($row['name']);
                }
                echo "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td><a href=\"booking.php?ID=" . htmlspecialchars($row['ID']) . "\">Book Here</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </section>
</body>
</html>