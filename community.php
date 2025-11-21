<?php
    $posts = json_decode(file_get_contents("data/posts.json"), true);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Talakay</title>
    <link rel="stylesheet" href="assets/style.css">
    
</head>
<body>

<?php include('includes/nav.php'); ?>

<div class="page-content">

    <div class="community-hero">
        <h1>Digital Town Plaza</h1>
        <p>A place to stay up-to-date with news, information, and opportunities for special interest groups in our community, such as businesses, pet adoptions, and youth.</p>
    </div>

    <div class="container">
    
        <!-- SIDEBAR -->
        <div class="sidebar">
            <h3>Communities</h3>
            <ul>
                <li><a href="feeds/dagupan.php">Dagupan City</a></li>
                <li>Lingayen</li>
                <li>Binmaley</li>
                <li>San Fabian</li>
                <li>Calasiao</li>
            </ul>
        </div>

        <div class="main-content">

            <h2>Discussion Rooms</h2>

            <div class="discussion-grid">

                <div class="discussion-card">
                    <img src="assets/animal_hub.jpg">
                    <h3>Stray Pets & Adoption</h3>
                    <p>A place to keep up-to-date with lost and found pets that have been collected by our Local Laws team, including animals available to adopt and foster.</p>
                    <a href="rooms/pets.php" class="view-btn">Enter Room</a>
                </div>

                <div class="discussion-card">
                    <img src="assets/environment.jpg">
                    <h3>Environment</h3>
                    <p>Discuss issues about waste management, pollution, and local environment protection efforts.</p>
                    <a href="rooms/environment.php" class="view-btn">Enter Room</a>
                </div>

                <div class="discussion-card">
                    <img src="assets/traffic.jfif">
                    <h3>Traffic & Roads</h3>
                    <p>Share updates about road repairs, traffic situations, and transportation issues.</p>
                    <a href="rooms/traffic.php" class="view-btn">Enter Room</a>
                </div>

        </div>
        <!-- Add more cards as needed -->
        </div>
        <!-- Add more cards as needed -->
    </div>
</div>
<?php
    include('includes/footer.php');
?>
</body>
</html>