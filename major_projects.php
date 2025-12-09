<?php
require_once 'includes/init.php';
require_once 'models/PostRepository.php';

$repo = new PostRepository();
$all = $repo->all();
// sort by created_at if present else newest first by array order
usort($all, function($a, $b) {
    $ta = strtotime($a['created_at'] ?? '1970-01-01');
    $tb = strtotime($b['created_at'] ?? '1970-01-01');
    return $tb <=> $ta;
});

// small set of local feeds (exists in /feeds)
$feeds = [
    ['file' => 'feeds/dagupan.php', 'label' => 'Dagupan City'],
    ['file' => 'feeds/binmaley.php', 'label' => 'Binmaley'],
    ['file' => 'feeds/lingayen.php', 'label' => 'Lingayen'],
    ['file' => 'feeds/san_fabian.php', 'label' => 'San Fabian'],
    ['file' => 'feeds/calasiao.php', 'label' => 'Calasiao'],
];

$latest = array_slice($all, 0, 12);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Major Projects — Pangasinan News</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include('includes/nav.php'); ?>

<div class="page-content">
    <div class="community-hero">
        <div>
            <h1>Major Projects & Local News</h1>
            <p>Latest news, projects and city feeds across Pangasinan to spark local discussions.</p>
        </div>
    </div>

    <div class="reddit-container">
        <div class="reddit-sidebar">
            <div class="sidebar-section">
                <h4>Local Feeds</h4>
                <div class="community-list">
                    <?php foreach ($feeds as $f): ?>
                        <a class="community-item" href="<?= htmlspecialchars($f['file']) ?>">
                            <span class="icon">📰</span>
                            <span class="label"><?= htmlspecialchars($f['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="sidebar-section">
                <h4>Quick Links</h4>
                <div class="community-list">
                    <a class="community-item" href="community.php">Community Feed</a>
                    <a class="community-item" href="?tab=topics">Topics</a>
                </div>
            </div>
        </div>

        <div class="reddit-feed">
            <div class="feed-header">
                <h2>Latest from around Pangasinan</h2>
                <p class="feed-subtitle">An aggregation of recent local posts and community updates.</p>
            </div>

            <div class="posts-container">
                <?php if (empty($latest)): ?>
                    <div class="empty-state"><p>No updates available.</p></div>
                <?php endif; ?>

                <?php foreach ($latest as $p): ?>
                    <article class="reddit-post">
                        <div class="post-vote-section">
                            <div class="vote-count">•</div>
                        </div>
                        <div class="post-content">
                            <div class="post-meta">
                                <span class="community-tag"><?= htmlspecialchars($p['topic'] ? $p['topic'] : 'General') ?></span>
                                <span class="post-author">Posted by <strong><?= htmlspecialchars($p['author'] ?? 'Neighbor') ?></strong></span>
                                <?php if (!empty($p['location'])): ?><span class="post-location">📍 <?= htmlspecialchars($p['location']) ?></span><?php endif; ?>
                            </div>
                            <h3 class="post-title"><?= htmlspecialchars($p['title'] ?? '') ?></h3>
                            <p class="post-body"><?= nl2br(htmlspecialchars($p['body'] ?? '')) ?></p>
                            <div class="post-actions">
                                <a class="view-btn" href="community.php?topic=<?= urlencode($p['topic'] ?? '') ?>">See related</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
