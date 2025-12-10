<?php
    require_once '../includes/init.php';
    require_once '../models/PostRepository.php';
    $repo = new PostRepository();
    $posts = array_filter($repo->all(), function ($post) {
        return stripos($post['location'] ?? '', 'Dagupan') !== false;
    });
    $user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>t/Dagupan</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<!-- REDDIT-STYLE FEED -->
<div class="feed-container">
    <!-- HEADER SECTION -->
    <div class="feed-header">
        <div class="feed-header-content">
            <div class="feed-branding">
                <img src="../assets/Dagupan_pfp.png" alt="Dagupan logo" class="feed-logo">
                <h1>t/Dagupan</h1>
            </div>
            <div class="feed-actions">
                <button class="feed-btn feed-btn-primary">+ Create Post</button>
                <button class="feed-btn">Join</button>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="reddit-container">
        <!-- LEFT: FEED POSTS -->
        <div class="reddit-feed">
            <?php foreach($posts as $p): ?>
                <div class="reddit-post">
                    <!-- Vote Section -->
                    <div class="post-vote-section">
                        <button class="vote-btn">▲</button>
                        <span class="vote-count">0</span>
                        <button class="vote-btn">▼</button>
                    </div>

                    <!-- Post Content -->
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-tag"><?= htmlspecialchars($p['topic'] ?? 'GENERAL') ?></span>
                            <span class="post-author">Posted by <?= htmlspecialchars($p['author'] ?? 'Neighbor') ?></span>
                            <span class="post-location">📍 <?= htmlspecialchars($p['location'] ?? '') ?></span>
                        </div>
                        <h3 class="post-title"><?= htmlspecialchars($p['title'] ?? '') ?></h3>
                        <p class="post-body"><?= nl2br(htmlspecialchars($p['body'] ?? '')) ?></p>
                        <div class="post-actions">
                            <button class="action-btn">💬 Comment</button>
                            <button class="action-btn">📤 Share</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- RIGHT: SIDEBAR -->
        <div class="reddit-sidebar">
            <div class="sidebar-section">
                <h4>About t/Dagupan</h4>
                <p>Discover the latest news, events, and stories from the bustling city nestled along the western coast of Luzon. Share your favorite spots, culinary delights, and experiences with fellow Dagupenos and visitors.</p>
                <div class="sidebar-stats">
                    <div class="stat">
                        <div class="stat-number">538</div>
                        <div class="stat-label">Weekly visitors</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">11</div>
                        <div class="stat-label">Weekly contributions</div>
                    </div>
                </div>
                <p class="sidebar-date">📅 Created Jan 5, 2022</p>
                <p class="sidebar-privacy">🔓 Public</p>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
