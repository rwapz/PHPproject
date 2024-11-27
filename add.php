<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("location:index.php");
    exit;
} else {
    include "connect.php";
    $username = $_SESSION['username'];
    $query = $db->prepare("SELECT * FROM user WHERE username=?");
    $query->execute(array($username));
    $control = $query->fetch(PDO::FETCH_ASSOC);
    if($control['admin'] != 1) {
        header("Location:films.php");
        exit;
    }
}

$Name = $Description = "";
$errors = array('name'=>'', 'description'=>'');
$errorflag = 0;

if(isset($_POST['submit'])){
    if(empty($_POST['name'])) {
        $errors['name'] = 'Name is required.';
        $errorflag = 1;
    } else {
        $Name = htmlspecialchars($_POST['name']);
    }
    
    if(empty($_POST['description'])) {
        $errors['description'] = 'Description is required.';
        $errorflag = 1;
    } else {
        $Description = htmlspecialchars($_POST['description']);
    }
    
    if($errorflag == 0) {
        $nameEscaped = mysqli_real_escape_string($conn, $Name);
        $descriptionEscaped = mysqli_real_escape_string($conn, $Description);
        $sql = "INSERT INTO films (name, description) VALUES ('$nameEscaped', '$descriptionEscaped')";
        
        if(mysqli_query($conn, $sql)) {
            header("Location:admin.php");
            exit;
        } else {
            echo 'Error: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Film - Cineplex</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/content.css"> <!-- Existing CSS -->
    <link rel="stylesheet" type="text/css" href="stylesheets/video.css"> <!-- Existing video CSS -->
    <link rel="stylesheet" type="text/css" href="stylesheets/admin.css"> <!-- Admin CSS -->
    <link rel="stylesheet" type="text/css" href="stylesheets/add.css"> <!-- Updated Add CSS -->
</head>
<body>
    <div class="container">
        <p style="text-align: center;">Welcome, <?php echo htmlspecialchars($username); ?></p>
        <h2>Add a New Film</h2>
        <form action="add.php" method="POST">
            <label for="name">Film Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($Name); ?>">
            <span class="error"><?php echo $errors['name']; ?></span>

            <label for="description">Description</label>
            <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($Description); ?>">
            <span class="error"><?php echo $errors['description']; ?></span>

            <input type="submit" value="Add Film" name="submit"> <!-- Add Film Button -->
        </form>
        
        <div class="link-box">
            <a href="admin.php">Back to Admin</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>