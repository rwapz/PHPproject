<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

// Include database connection
include "connect.php";

// Verify the user's role (only admin can delete bookings)
$username = $_SESSION['username'];
$query = $db->prepare("SELECT * FROM user WHERE username = ?");
$query->execute([$username]);
$control = $query->fetch(PDO::FETCH_ASSOC);

if ($control['admin'] != 1) {
    header("Location:films.php");
    exit;
}

// Check if the film ID is provided in the POST request
if (isset($_POST['filmid'])) {
    $filmid = intval($_POST['filmid']); // Cast to integer for safety

    // Prepare and execute the delete query
    $deleteQuery = $db->prepare("DELETE FROM bookings WHERE filmsid = ?");
    
    // Fetch film name for confirmation message
    $filmQuery = $db->prepare("SELECT name FROM films WHERE ID = ?");
    $filmQuery->execute([$filmid]);
    $filmDetails = $filmQuery->fetch(PDO::FETCH_ASSOC);

    if ($deleteQuery->execute([$filmid])) {
        // Redirect to confirmation page with success message
        header("Location: delete_confirmation.php?filmname=" . urlencode($filmDetails['name']));
    } else {
        // If query fails, redirect with an error message
        header("Location:view_bookings.php?error=Failed to delete bookings for film ID {$filmid}.");
    }
} else {
    // If no film ID is provided, redirect to view_bookings.php
    header("Location:view_bookings.php?error=No film ID provided.");
}
exit;