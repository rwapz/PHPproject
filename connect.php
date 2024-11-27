<?php

$conn = mysqli_connect('localhost', 'adminrhys', 'admin1', 'cinemas');


//prepared data objects
try {
    $db = new PDO("mysql:host=localhost;dbname=cinemas;charset=utf8", "adminrhys" , "admin1");
    
} catch (PDOException $e){
        echo $e->getMessage();
}

?>