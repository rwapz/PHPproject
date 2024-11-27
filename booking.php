<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit; // Always exit after redirect
}

include "connect.php";
$username = $_SESSION['username'];

// Fetch user ID based on the logged-in username
$query = $db->prepare("SELECT * FROM user WHERE username = ?");
$query->execute(array($username));
$control = $query->fetch(PDO::FETCH_ASSOC);
$userid = $control['ID']; // Adjust if your user table schema uses a different naming convention

// Get the film ID from the query parameter
$ID = $_GET['ID'];
$query = $db->prepare("SELECT * FROM films WHERE ID = ?");
$query->execute(array($ID));
$bookfilm = $query->fetch(PDO::FETCH_ASSOC);
$filmid = $bookfilm['ID'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - Cineplex</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/index1.css"> <!-- Unified styles -->
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Cineplex</h1>
            <nav>
                <ul>
                    <li><a href="logout.php">Logout Here</a></li>
                    <li><a href="films.php">Return to Films</a></li>
                    <li><a href="admin.php">Admin Page</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="welcome">
        <h4>Welcome <?php echo htmlspecialchars($username); ?></h4>
    </div>

    <div class="feature-film">
        <table>
            <tr>
                <td><?php echo htmlspecialchars($bookfilm['name']); ?></td>
                <td><?php echo htmlspecialchars($bookfilm['description']); ?></td>
                <td>
                    <form action="booking2.php" method="POST">
                        <label>No. of Tickets:</label>
                        <select name="tickets" id="tickets">
                            <option value=1>1</option>
                            <option value=2>2</option>
                            <option value=3>3</option>
                            <option value=4>4</option>
                            <option value=5>5</option>
                        </select>
                        <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>">
                        <input type="hidden" name="filmid" value="<?php echo htmlspecialchars($filmid); ?>">
                        <input type="submit" value="Submit">
                    </form>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>