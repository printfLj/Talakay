<?php
require_once 'includes/init.php';
require_once 'models/PostRepository.php';

$repo = new PostRepository();
$topic = $_GET['topic'] ?? null;
$topics = [
    'traffic' => ['label' => 'Traffic & Roads', 'desc' => 'Road closures, repairs, and congestion updates.'],
    'pets' => ['label' => 'Stray Pets & Adoption', 'desc' => 'Lost/found pets, adoptions, and vet resources.'],
    'environment' => ['label' => 'Environment & Cleanups', 'desc' => 'Waste collection, cleanups, and climate actions.'],
    'general' => ['label' => 'General Updates', 'desc' => 'Barangay notices, events, and community wins.'],
];

$posts = $topic ? $repo->search(null, null, $topic) : $repo->all();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Topics - Talakay</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/nav.php'; ?>

<div class="page-content">
    <div class="community-hero">
        <div>
            <h1>Topics</h1>
            <p>Curated spaces for the conversations Pangasinenses care about most.</p>
        </div>
    </div>

    <div class="container topics-layout">
        <aside class="sidebar">
            <h3>Rooms</h3>
            <ul>
                <?php foreach ($topics as $key => $meta): ?>
                    <li>
                        <a href="topics.php?topic=<?= $key ?>" class="<?= $topic === $key ? 'active' : '' ?>">
                            <?= htmlspecialchars($meta['label']) ?>
                        </a>
                        <p class="muted small"><?= htmlspecialchars($meta['desc']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="sidebar-card">
                <p>Looking for something specific?</p>
                <a class="cta-link" href="community.php">Go to Search & Feed →</a>
            </div>
        </aside>

        <main class="main-content">
            <h2><?= $topic && isset($topics[$topic]) ? htmlspecialchars($topics[$topic]['label']) : 'All Topics' ?></h2>
            <?php if (empty($posts)): ?>
                <p class="muted">No posts yet. Be the first to share an update.</p>
            <?php endif; ?>

            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <div class="post-header">
                        <div>
                            <div class="author"><?= htmlspecialchars($post['author'] ?? 'Neighbor') ?></div>
                            <div class="location"><?= htmlspecialchars($post['location'] ?? '') ?></div>
                        </div>
                        <div class="pill"><?= htmlspecialchars($topics[$post['topic']]['label'] ?? 'General') ?></div>
                    </div>
                    <div class="post-title"><?= htmlspecialchars($post['title'] ?? '') ?></div>
                    <p class="post-body"><?= nl2br(htmlspecialchars($post['body'] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>

