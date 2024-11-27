<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:index.php");
    exit;
} 

include "connect.php";
$username = $_SESSION['username'];
$query = $db->prepare("SELECT * FROM user WHERE username=?");
$query->execute(array($username));
$control = $query->fetch(PDO::FETCH_ASSOC);
if ($control['admin'] != 1) {
    header("Location:films.php");
    exit; // It's a good practice to call exit after a header redirect.
}

$name = $description = ""; // Initialize both variables to empty string
$errors = array("name" => "", "description" => "");
$errorsflag = 0;

if (!isset($_GET['ID']) && !isset($_POST['ID'])) {
    header("location:admin.php");
    exit;
}

if (isset($_GET['ID'])) {
    $ID = $_GET['ID'];
    $query = $db->prepare("SELECT * FROM films WHERE ID=?");
    $query->execute(array($ID));
    $control = $query->fetch(PDO::FETCH_ASSOC);

    if ($control) {
        $name = $control['name'];
        $description = $control['description'];
    } else {
        // If no film is found, redirect to admin
        header("location:admin.php");
        exit;
    }
}

if (isset($_POST['submit'])) {
    $ID = $_POST['ID'];

    if (empty($_POST['name'])) {
        $errors['name'] = "Name is empty.<br>";
        $errorsflag = 1;
    } else {
        $name = htmlspecialchars($_POST['name']); // Sanitize user input
    }

    if (empty($_POST['description'])) {
        $errors['description'] = "Description is empty.<br>";
        $errorsflag = 1;
    } else {
        $description = htmlspecialchars($_POST['description']); // Sanitize user input
    }

    if ($errorsflag == 0) {
        $sql = "UPDATE films SET name = :name, description = :description WHERE ID = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $ID);
        
        if ($stmt->execute()) {
            header('location:admin.php'); // Correct the 'locations' typo
            exit;
        } else {
            echo 'Query Error: ' . $stmt->errorInfo()[2]; // Better error reporting for PDO
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" type="text/css" href="stylesheets/update.css"> -->
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css">
    <title>Update - Cineplex</title>
</head>
<body>
    <h1>Update - Cineplex<h1>
    <h4>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?></h4>
    <p>Update Record here</p><br>
    <div class="logout">
        <a href="logout.php">Logout Here</a>
        <a href="admin.php">Return to admin</a>
    </div>
    <form action="update.php" method="POST">
        <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
        <label>Film name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"><br>
        <div><?php echo $errors['name']; ?></div>
        <label>Film description</label>
        <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea><br>
        <div><?php echo $errors['description']; ?></div>
        <input type="submit" name="submit" value="Submit">
    </form>
</body>
</html>