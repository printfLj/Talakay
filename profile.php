<?php
    include('includes/nav.php');

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    $user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Talakay</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="profile-page">
        <section class="profile-card">
            <h2>Welcome, <?= htmlspecialchars($user['name'] ?? 'User') ?></h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
            <?php if (!empty($user['created_at'])): ?>
                <p><strong>Member since:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
            <?php endif; ?>

            <p>
                <a class="btn" href="logout.php">Logout</a>
            </p>
        </section>
    </main>

<?php
    include('includes/footer.php');
?>
</body>
</html>
