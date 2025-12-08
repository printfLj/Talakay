<?php
    $currentPath = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $isActive = function (string $file) use ($currentPath) {
        return $currentPath === $file ? 'active' : '';
    };
?>
<header class="navbar">
    <a href="/index.php" class="branding">
        <img src="/assets/Talakay_Logo.png" alt="Logo" class="logo">
        <span class="logo-title">Talakay</span>
    </a>
    <nav class="nav-links">
        <a class="<?= $isActive('index.php') ?>" href="/index.php">Home</a>
        <a class="<?= $isActive('community.php') ?>" href="/community.php">Communities</a>
        <a class="<?= $isActive('topics.php') ?>" href="/topics.php">Topics</a>
        <a class="<?= $isActive('profile.php') ?>" href="/profile.php">Friends & Profile</a>
        <?php if (isset($_SESSION['user']) && is_array($_SESSION['user'])): ?>
            <div class="profile-links">
                <a href="/profile.php" class="profile-name"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Profile') ?></a>
            </div>
        <?php else: ?>
            <a href="/login.php" class="login-btn">Log In / Join</a>
        <?php endif; ?>
    </nav>
</header>
