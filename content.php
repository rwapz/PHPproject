<?php
session_start();
if(!isset($_SESSION['username'])) {
    header('location:index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cineplex</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/content.css"> <!-- Main CSS file -->
    <link rel="stylesheet" type="text/css" href="stylesheets/video.css"> <!-- Link to the external CSS file for videos -->
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

    <div class="welcome">
        <h2>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Login Success!</p>
    </div>

    <section class="featured-films">
        <h2>Featured Films</h2>
        <div class="film-list">
            <div class="film" onclick="playTrailer('video/sawtrailer.mp4')">
                <img src="images/saw.jpg" alt="Film 1">
                <h3>Saw (20th Anniversary)</h3>
                <p>In SAW, two men awaken in a grim room, shackled and terrified.
                     Their only way out? Obey Jigsaw’s sickening instructions — even if it means killing each other.</p>
            </div>
            <div class="film" onclick="playTrailer('video/terrifier3trailer.mp4')">
                <img src="images/terrifier3.jpg" alt="Film 2">
                <h3>Terrifier 3</h3>
                <p>Following the shocking events of Terrifier 2, survivor Sienna is plagued by disturbing
                     visions and begins to realise there’s no escaping her past, or outrunning Art the Clown.</p>
            </div>
            <div class="film" onclick="playTrailer('video/stingtrailer.mp4')">
                <img src="images/sting.jpg" alt="Film 3">
                <h3>Sting</h3>
                <p>STING spins a web of thrilling terror when 12-year-old Charlotte’s pet spider rapidly transforms
                     into a giant flesh-eating monster, forcing the young girl to fight for her family’s survival.</p>
            </div>
            
        </div>
    </section>

    <!-- Video Modal -->
    <div id="videoModal" onclick="closeModal()">
        <video id="trailer" controls autoplay>
            <source id="videoSource" src="video/" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <footer>
        <p>© <?php echo date("Y"); ?> Cineplex. All rights reserved.</p>
    </footer>

    <script>
        function playTrailer(videoPath) {
            const modal = document.getElementById("videoModal");
            const video = document.getElementById("trailer");
            const source = document.getElementById("videoSource");

            source.src = videoPath; // Set the video source
            video.load(); // Load the video
            modal.style.display = "flex"; // Show the modal
            video.play(); // Play the video automatically
        }

        function closeModal() {
            const modal = document.getElementById("videoModal");
            const video = document.getElementById("trailer");

            modal.style.display = "none"; // Hide the modal
            video.pause(); // Pause the video
            video.currentTime = 0; // Reset the video to the beginning
        }
    </script>
</body>
</html>