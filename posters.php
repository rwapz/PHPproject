<?php

session_start();
include 'connect.php';

$postersEnabled = isset($_SESSION['posters_enabled']) ? $_SESSION['posters_enabled'] : true;

// Movie data with current screening status, true = available screens, false = no available screens 
$movies = [
    'Saw' => ['poster' => 'saw.jpg', 'has_screenings' => true],
    'Terrifier 3' => ['poster' => 'terrifier3.jpg', 'has_screenings' => true], 
    'Sting' => ['poster' => 'sting.jpg', 'has_screenings' => true],
    'Afraid' => ['poster' => 'afraid.jpg', 'has_screenings' => true],
    'Image' => ['poster' => 'image.jpg', 'has_screenings' => false]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Posters</title>
    <link rel="stylesheet" href="stylesheets/index1.css">
    <link rel="stylesheet" href="stylesheets/main.css">
    <style>
        .poster {
            display: none;
            position: absolute;
            z-index: 100;
        }
        .movie-item {
            position: relative;
            display: inline-block;
            margin-right: 15px;
        }
        .movie-item:hover .poster {
            display: block;
        }
        .no-screening-message {
            display: none;
            position: absolute;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px;
            z-index: 200;
            width: 200px;
            text-align: center;
        }
        .movie-item:hover .no-screening-message {
            display: block;
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Cineplex</h1>
        <nav>
            <ul>
                <li><a href="films.php">Home</a></li>
                <li><a href="upcomingfilms.php">Upcoming Films</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href='logout.php'>Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="movie-list">
        <?php foreach ($movies as $movie => $data): ?>
            <div class="movie-item">
                <span><?php echo $movie; ?></span>
                <?php if ($postersEnabled): ?>
                    <img src="images/<?php echo $data['poster']; ?>" alt="<?php echo $movie; ?>" class="poster" style="width: 200px;">
                    <?php if (!$data['has_screenings']): ?>
                        <div class="no-screening-message">
                            No current screenings of this film '<?php echo $movie; ?>'
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="toggle_posters.php">Toggle Posters: <?php echo $postersEnabled ? 'ON' : 'OFF'; ?></a>
</main>
</body>
</html>