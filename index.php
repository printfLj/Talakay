<?php 
    include ("includes/nav.php");

    // `includes/nav.php` handles session_start(); make the homepage public
    $user = null;
    if (isset($_SESSION['user'])) {
        $user = $_SESSION["user"];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talakay</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-text">
        <h1>Engage With Your Community</h1>
        <p>Join discussions, share insights, and help improve communities across Pangasinan.</p>
        <a href="community.php" class="cta-btn">Visit Community Hubs</a>
    </div>
</section>

<!-- INFO PANEL -->
<section class="info-panel">
    <h2>Visit our Community and Discussion Hubs</h2>
    <p>A space to find updates, opportunities, local concerns, and community-driven projects.</p>
</section>

<!-- CARD GRID -->
<section class="cards">
    <div class="card">
        <h3>Local Issues & Concerns</h3>
        <p>Report issues, share observations, and help prioritize community needs.</p>
        <a href="#">Learn more →</a>
    </div>

    <div class="card">
        <h3>Pet Adoption</h3>
        <p>Help abandoned pets find a new home by connecting with local rescue groups.</p>
        <a href="#">Learn more →</a>
    </div>

    <div class="card">
        <h3>Barangay Projects</h3>
        <p>Get updates on government or volunteer-led projects happening near you.</p>
        <a href="#">Learn more →</a>
    </div>
</section>

<?php
    include('includes/footer.php');
?>
</body>
</html>
