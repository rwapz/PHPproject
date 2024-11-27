<?php
session_start();
if (isset($_SESSION['username'])) {
    header('Location: films.php');
}
include('connect.php');
$usename = $password = $email = '';
$admin = 0;
$errors = array('usename' => '', 'password' => '', 'email' => '');

if (isset($_POST['submit'])) {
    // Validate email
    if (empty($_POST['email'])) {
        $errors['email'] = 'Email space is empty<br>';
    } else {
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email space is not valid<br>'; 
        }
    }

    // Validate username
    if (empty($_POST['usename'])) {
        $errors['usename'] = 'Username space is empty<br>';
    } 

    // Validate password
    if (empty($_POST['password'])) {
        $errors['password'] = 'Password space is empty<br>';
    }

    // Check if there are any errors
    if (array_filter($errors)) {
        // Display errors to the user
        foreach ($errors as $error) {
            echo $error;
        }
    } else {
        $usename = mysqli_real_escape_string($conn, $_POST['usename']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $hashed_password = MD5($password);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        $sql = "INSERT into user (username, password, email, admin) VALUES ('$usename', '$hashed_password', '$email', '$admin')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['username'] = $usename;
            header('Location: films.php');
        } else {
            echo 'Error: ' . mysqli_error($conn);
        }
    }
}

// stuff to do with form
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="stylesheets/register.css">
    <title>Cineplex</title>
</head>
<body>
    <div class="container">
        <h3>Register Here</h3><br>
        <form action='register.php' method='POST'>
            <label>Username:</label>
            <input type='text' name='usename' required><br>
            <label>Password:</label>
            <input type='password' name='password' required><br>
            <label>Email:</label>
            <input type='email' name='email' required><br>
            <input type='submit' name='submit' value='Submit'><br>
        </form>
    </div>
</body>
</html>