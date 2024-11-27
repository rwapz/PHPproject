<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location:index.php');
    exit; // Always use exit after header to stop further execution
}

include "connect.php";

$username = $_SESSION['username'];
$query = $db->prepare("SELECT * FROM user WHERE username=?");
$query->execute(array($username));
$control = $query->fetch(PDO::FETCH_ASSOC);

// Check if the user is an admin
if ($control['admin'] != 1) {
    header("Location:films.php");
    exit; // Stop execution after redirection
}

// Check if ID is set
if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Ensure it's an integer

    // Delete query
    $stmt = $db->prepare("DELETE FROM menu WHERE ID = ?");
    if ($stmt->execute([$id])) {
        echo "Item deleted successfully.";
    } else {
        echo "Error deleting item.";
    }

    // Redirect back to the menu view
    header("Location:new_food_drink.php");
    exit;
} else {
    echo "Invalid request.";
    exit;
}
?>