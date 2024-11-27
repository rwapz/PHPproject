<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

include "connect.php"; // Include your database connection file
$username = $_SESSION['username'];
$query = $db->prepare("SELECT * FROM user WHERE username=?");
$query->execute([$username]);
$control = $query->fetch(PDO::FETCH_ASSOC);
if ($control['admin'] != 1) {
    header("Location: new_food_drink.php"); // Redirect for normal users
    exit; 
}

$name = $description = "";
$errors = ["name" => "", "description" => ""];
$errorsflag = 0;

// Handle form submission for adding a new food item
if (isset($_POST['submit'])) {
    if (empty($_POST['name'])) {
        $errors['name'] = "Name is empty.<br>";
        $errorsflag = 1;
    } else {
        $name = htmlspecialchars($_POST['name']);
    }

    if (empty($_POST['description'])) {
        $errors['description'] = "Description is empty.<br>";
        $errorsflag = 1;
    } else {
        $description = htmlspecialchars($_POST['description']);
    }

    // Check if the food item should be a combo
    $shouldBeCombo = isset($_POST['isCombo']) ? 1 : 0;

    // If no errors, proceed to insert
    if ($errorsflag == 0) {
        $sql = "INSERT INTO menu (name, description, combo) VALUES (:name, :description, :combo)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':combo', $shouldBeCombo); // Bind the combo value

        if ($stmt->execute()) {
            header('Location: new_food_drink.php'); // Redirect after successful addition
            exit;
        } else {
            echo 'Query Error: ' . $stmt->errorInfo()[2];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheets/main.css">
    <title>Add Food Item</title>
</head>
<body>
    <h1>Add Food Item</h1>
    <h4>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?></h4>
    <div class="logout">
        <a href="logout.php">Logout Here</a>
        <a href="new_food_drink.php">Return to Food & Drink Menu</a>
    </div>
    
    <form action="add_food_drink.php" method="POST">
        <label>Food Name</label>
        <input type="text" name="name" required><br>
        <div><?php echo $errors['name']; ?></div>
        <label>Food Description</label>
        <textarea name="description" required></textarea><br>
        <div><?php echo $errors['description']; ?></div>
        <label>
            <input type="checkbox" name="isCombo"> Is this a combo meal?
        </label><br>
        <input type="submit" name="submit" value="Add Food Item">
    </form>
</body>
</html>