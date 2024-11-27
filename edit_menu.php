<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
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

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: new_food_drink.php"); // Redirect if no ID is provided
    exit;
}

$id = $_GET['id'];
$query = $db->prepare("SELECT * FROM menu WHERE ID=?");
$query->execute([$id]);
$foodItem = $query->fetch(PDO::FETCH_ASSOC);

if (!$foodItem) {
    header("Location: new_food_drink.php"); // Redirect if no matching food item is found
    exit; 
}

// Get existing values
$name = $foodItem['name'];
$description = $foodItem['description'];
$isCombo = $foodItem['combo'] == 1; // Check if it's a combo

// Handle form submission for editing the food item
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

    // If no errors, proceed to update
    if ($errorsflag == 0) {
        $sql = "UPDATE menu SET name = :name, description = :description, combo = :combo WHERE ID = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':combo', $shouldBeCombo); // Bind the combo value
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            header('Location: new_food_drink.php'); // Redirect after successful update
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
    <title>Edit Food Item</title>
</head>
<body>
    <h1>Edit Food Item</h1>
    <h4>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?></h4>
    <div class="logout">
        <a href="logout.php">Logout Here</a>
        <a href="new_food_drink.php">Return to Food & Drink Menu</a>
    </div>
    
    <form action="edit_menu.php?id=<?php echo htmlspecialchars($id); ?>" method="POST">
        <input type="hidden" name="ID" value="<?php echo htmlspecialchars($id); ?>">
        <label>Food Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>
        <div><?php echo $errors['name']; ?></div>
        <label>Food Description</label>
        <textarea name="description" required><?php echo htmlspecialchars($description); ?></textarea><br>
        <div><?php echo $errors['description']; ?></div>
        <label>
            <input type="checkbox" name="isCombo" <?php echo $isCombo ? 'checked' : ''; ?>> Is this part of a combo?
        </label><br>
        <input type="submit" name="submit" value="Submit">
    </form>
</body>
</html>