    <?php 
        require_once 'includes/init.php';

        // make the homepage public; read session user if present
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
        <title>Home</title>
        <link rel="stylesheet" href="assets/style.css">
    </head>
    <body>

    <?php include('includes/nav.php'); ?>

    <!-- HERO SECTION -->
    <section class="hero hero-fixed">
        <div class="hero-text-panel">
            <h1>Engage With Your Community</h1>
            <p>Join discussions, share insights, and help improve communities across Pangasinan.</p>
        </div>
    </section>

    <!-- INFO PANEL -->
    <section class="info-panel">
        <h2>Visit our Community and Discussion Hubs</h2>
        <p>A space to find updates, opportunities, local concerns, and community-driven projects.</p>
        <div class="cta-btn">
            <a href="community.php">Click here</a>
        </div>
    </section>

    <!-- CARD GRID -->
    <section class="cards">
        <article class="card">
            <div class="card-media" style="background-image:url('assets/animal_hub.jpg');"></div>
            <div class="card-body">
                <h3>Animal Hub</h3>
                <p>Keep up-to-date with lost and found pets, adoption leads, and vet resources across Pangasinan.</p>
                <a href="rooms/pets.php">Enter room →</a>
            </div>
        </article>

        <article class="card">
            <div class="card-media" style="background-image:url('assets/environment.jpg');"></div>
            <div class="card-body">
                <h3>Environment Hub</h3>
                <p>Track cleanups, waste collection, and local climate efforts. Join neighbors making streets cleaner.</p>
                <a href="rooms/environment.php">Enter room →</a>
            </div>
        </article>

        <article class="card">
            <div class="card-media" style="background-image:url('assets/traffic.jfif');"></div>
            <div class="card-body">
                <h3>Traffic & Roads</h3>
                <p>Road work, drainage fixes, and jeepney reroutes. Share updates before you head out.</p>
                <a href="rooms/traffic.php">Enter room →</a>
            </div>
        </article>

        <article class="card1">
            <div class="card-media" style="background-image:url('assets/Pangasinan.jpg');"></div>
            <div class="card-body">
                <h3>Community Projects</h3>
                <p>Barangay projects and volunteer drives—see what's underway and where help is needed.</p>
                <a href="community.php">See updates →</a>
            </div>
        </article>

        <article class="card1">
            <div class="card-media" style="background-image:url('assets/Talakay_Logo.png');"></div>
            <div class="card-body">
                <h3>Topics & Discover</h3>
                <p>Browse curated topics like traffic, pets, and environment to jump into focused discussions.</p>
                <a href="community.php?tab=topics">View topics →</a>
            </div>
        </article>

        <article class="card1">
            <div class="card-media" style="background-image:url('assets/animal_hub.jpg');"></div>
            <div class="card-body">
                <h3>Meet Neighbors</h3>
                <p>Add friends, swap messages, and coordinate with people near you about local concerns.</p>
                <a href="profile.php">Go to profile →</a>
            </div>
        </article>
    </section>

    <section class="panel-below-cards">
        <h2>Discover our Major Projects</h2>
        <p>Learn more about our capital works and asset management projects happening throughout the year.</p>
        <div class="cta-btn">
            <a href="major_projects.php">Click here</a>
        </div>
    </section>


    <section class="cards">
        <article class="card">
            <div class="card-media" style="background-image:url('assets/animal_hub.jpg');"></div>
            <div class="card-body">
                <h3>Animal Hub</h3>
                <p>Keep up-to-date with lost and found pets, adoption leads, and vet resources across Pangasinan.</p>
                <a href="rooms/pets.php">Enter room →</a>
            </div>
        </article>

        <article class="card">
            <div class="card-media" style="background-image:url('assets/environment.jpg');"></div>
            <div class="card-body">
                <h3>Environment Hub</h3>
                <p>Track cleanups, waste collection, and local climate efforts. Join neighbors making streets cleaner.</p>
                <a href="rooms/environment.php">Enter room →</a>
            </div>
        </article>

        <article class="card">
            <div class="card-media" style="background-image:url('assets/traffic.jfif');"></div>
            <div class="card-body">
                <h3>Traffic & Roads</h3>
                <p>Road work, drainage fixes, and jeepney reroutes. Share updates before you head out.</p>
                <a href="rooms/traffic.php">Enter room →</a>
            </div>
        </article>
    </section>

    <section class="About-us">
        <div class="about-branding" role="img" aria-label="Talakay logo and title">
            <img src="assets/Talakay_inverted-removed.png" alt="Talakay logo" class="logo-about">
            <span class="logo-title">Talakay</span>
        </div>
        <p class="about-text">Talakay is your digital tambayan for everything in Pangasinan. It is a community hub where people can share updates, spark meaningful conversations, and join topic-based rooms that bring locals together. From barangay happenings to province-wide events, resources, and issues, Talakay keeps every voice heard and every story connected.

A space built by the community, for the community—because every discussion starts with one talakay.</p>
    </section>

    <?php
        include('includes/footer.php');
    ?>
    </body>
    </html>
