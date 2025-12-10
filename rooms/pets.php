<?php
require_once '../includes/init.php';
require_once '../models/PostRepository.php';

$repo = new PostRepository();
$user = $_SESSION['user'] ?? null;
$topic = 'pets';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_post') {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $tags = $_POST['tags'] ?? '';
    $location = $_POST['location'] ?? '';

    if ($title !== '' && $body !== '') {
        $repo->addPost([
            'author' => $user['name'] ?? 'Neighbor',
            'author_email' => $user['email'] ?? null,
            'title' => $title,
            'body' => $body,
            'topic' => $topic,
            'tags' => $tags,
            'location' => $location,
        ]);
        header('Location: traffic.php');
        exit;
    }
}

$posts = $repo->search(null, null, $topic);
$topicLabel = 'Stray Pets';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>t/Traffic</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<?php include '../includes/nav.php'; ?>

<!-- REDDIT-STYLE FEED -->
<div class="feed-container">
    <!-- HEADER SECTION -->
    <div class="feed-header">
        <div class="feed-header-content">
            <div class="feed-branding">
                <img src="../assets/pet_pfp.jfif" alt="Traffic logo" class="feed-logo">
                <h1>t/Stray Pets</h1>
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
            <?php foreach ($posts as $post): ?>
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
                            <span class="post-tag"><?= htmlspecialchars($topicLabel) ?></span>
                            <span class="post-author">Posted by <?= htmlspecialchars($post['author'] ?? 'Neighbor') ?></span>
                            <span class="post-location">📍 <?= htmlspecialchars($post['location'] ?? '') ?></span>
                        </div>
                        <h3 class="post-title"><?= htmlspecialchars($post['title'] ?? '') ?></h3>
                        <p class="post-body"><?= nl2br(htmlspecialchars($post['body'] ?? '')) ?></p>
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
                <h4>About this community</h4>
                <p>Road closures, drainage fixes, jeepney routes, and traffic advisories. Stay informed about transportation updates and share real-time traffic conditions in your area.</p>
                <div class="sidebar-stats">
                    <div class="stat">
                        <div class="stat-number">284</div>
                        <div class="stat-label">Weekly visitors</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">7</div>
                        <div class="stat-label">Weekly contributions</div>
                    </div>
                </div>
                <p class="sidebar-date">📅 Created Mar 12, 2022</p>
                <p class="sidebar-privacy">🔓 Public</p>
            </div>

            <?php if ($user): ?>
                <div class="sidebar-section">
                    <h4>Create a Post</h4>
                    <form method="post" class="create-form">
                        <input type="hidden" name="action" value="create_post">
                        <input type="text" name="title" placeholder="Title" required>
                        <textarea name="body" placeholder="Share an update" required></textarea>
                        <input type="text" name="location" placeholder="Location">
                        <input type="text" name="tags" placeholder="Tags (comma separated)">
                        <button type="submit" class="create-btn">Post</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="sidebar-section">
                    <p style="text-align: center;">Please <a href="../login.php">log in</a> to post.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>

