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
        <a href="rooms/pets.php" class="card-link">
            <article class="card">
                <div class="card-media" style="background-image:url('assets/animal_hub.jpg');"></div>
                <div class="card-body">
                    <h3>Stray Pets</h3>
                    <p>Keep up-to-date with lost and found pets, adoption leads, and vet resources across Pangasinan.</p>
                    <span class="card-action">Enter room →</span>
                </div>
            </article>
        </a>

        <a href="rooms/environment.php" class="card-link">
            <article class="card">
                <div class="card-media" style="background-image:url('assets/environment.jpg');"></div>
                <div class="card-body">
                    <h3>Environment</h3>
                    <p>Track cleanups, waste collection, and local climate efforts. Join neighbors making streets cleaner.</p>
                    <span class="card-action">Enter room →</span>
                </div>
            </article>
        </a>

        <a href="rooms/traffic.php" class="card-link">
            <article class="card">
                <div class="card-media" style="background-image:url('assets/traffic.jfif');"></div>
                <div class="card-body">
                    <h3>Traffic & Roads</h3>
                    <p>Road work, drainage fixes, and jeepney reroutes. Share updates before you head out.</p>
                    <span class="card-action">Enter room →</span>
                </div>
            </article>
        </a>

        <a href="rooms/public_safety.php" class="card-link">
            <article class="card1">
                <div class="card-media" style="background-image:url('/assets/Dagupan_lighting.jpg');"></div>
                <div class="card-body">
                    <h3>Public Safety</h3>
                    <p>Stay informed on local safety alerts, fire incidents, and emergency tips. Help keep your neighborhood prepared.</p>
                    <span class="card-action">See updates →</span>
                </div>
            </article>
        </a>

        <a href="rooms/local_businesses.php?tab=topics" class="card-link">
            <article class="card1">
                <div class="card-media" style="background-image:url('/assets/Lingayen_bazaar.jpg');"></div>
                <div class="card-body">
                    <h3>Local Businesses</h3>
                    <p>Updates on new shops, market days, and service recommendations. Support Pangasinan’s growing business community.</p>
                    <span class="card-action">View topics →</span>
                </div>
            </article>
        </a>

        <a href="rooms/health.php" class="card-link">
            <article class="card1">
                <div class="card-media" style="background-image:url('/assets/Calasiao_lighting.jpg');"></div>
                <div class="card-body">
                    <h3>Health & Wellness</h3>
                    <p>Get updates on medical missions, vaccination schedules, and local health resources for families.</p>
                    <span class="card-action">Go to profile →</span>
                </div>
            </article>
        </a>
    </section>

    <section class="panel-below-cards">
        <h2>Discover What’s Popular Today</h2>
        <p>Stay updated on trending community posts, hot topics, and the most active discussions happening right now.</p>
        <div class="cta-btn">
            <a href="popular_today.php">Click here</a>
        </div>
    </section>


    <section class="cards">
        <article class="card">
            <div class="card-media" style="background-image:url('assets/animal_hub.jpg');"></div>
            <div class="card-body">
                <h3>Stray Pets</h3>
                <p>Keep up-to-date with lost and found pets, adoption leads, and vet resources across Pangasinan.</p>
                <a href="rooms/pets.php">Enter room →</a>
            </div>
        </article>

        <article class="card">
            <div class="card-media" style="background-image:url('assets/environment.jpg');"></div>
            <div class="card-body">
                <h3>Environment</h3>
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
