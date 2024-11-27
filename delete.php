<?php
session_start();
if(!isset($_SESSION['username'])) {
    header('location:index.php');
    exit; // Always use exit after header to stop further execution
}
else {
    include "connect.php";
    $username = $_SESSION['username'];
    $query = $db->prepare("SELECT * FROM user WHERE username=?");
    $query->execute(array($username));
    $control = $query->fetch(PDO::FETCH_ASSOC);
    
    // Check if user is an admin
    if($control['admin'] != 1) {
        header("Location:films.php");
        exit; // Stop execution after redirection
    }

    // Check if ID is set
    if(!isset($_GET['ID'])) {
        header('location:admin.php');
        exit; // Stop execution after redirection
    } else {
        $ID = $_GET['ID']; // Get ID from the query string
        
        // Prepare and execute delete query
        $sql = "DELETE FROM films WHERE ID = :ID";
        $stmt = $db->prepare($sql);
        $stmt->execute(['ID' => $ID]);
        
        // Redirect back to the admin page
        header('Location: admin.php');
        exit; // Always exit after redirecting
    }
}
?>