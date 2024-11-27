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

// Fetch bookings grouped by film
$query = $db->query("SELECT films.ID, films.name AS film_name, COUNT(bookings.id) AS total_bookings 
                     FROM films 
                     LEFT JOIN bookings ON films.ID = bookings.filmsid 
                     GROUP BY films.ID");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cineplex - Grouped View Bookings</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/index1.css"> 
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
            <h3>Group View of Bookings</h3>
            <a href="view_bookings.php">Return to  All Bookings</a>

            <?php if ($query->rowCount() > 0): ?>
                <table>
                    <tr>
                        <th>Film Name</th>
                        <th>Total Bookings</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['film_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_bookings']); ?></td>
                            <td>
                                <a href="view_bookings_by_film.php?filmid=<?php echo $row['ID']; ?>" class="btn">View Bookings</a>
                                <form action="delete_bookings.php" method="post" style="display:inline-block;">
                                    <input type="hidden" name="filmid" value="<?php echo $row['ID']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete all bookings for <?php echo htmlspecialchars($row['film_name']); ?>? This action cannot be undone.');">Delete All Bookings</button>
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