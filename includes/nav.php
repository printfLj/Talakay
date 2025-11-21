<header class="navbar">
    <div class="logo">Talakay</div>

    <nav class="nav-links">
        <a href="/index.php">Home</a>
        <a href="/community.php">Communities and Hubs</a>
        <a href="#">Topics</a>
        <a href="#">About Us</a>
        <?php if (isset($_SESSION['user']) && is_array($_SESSION['user'])): ?>
            <div class="profile-links">
                <a href="/profile.php" class="profile-name"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Profile') ?></a>
            </div>
        <?php else: ?>
            <a href="/login.php" class="login-btn">Log In / Join</a>
        <?php endif; ?>
    </nav>
</header>
