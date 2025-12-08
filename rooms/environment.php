<?php
require_once '../includes/init.php';
require_once '../models/PostRepository.php';

$repo = new PostRepository();
$user = $_SESSION['user'] ?? null;
$topic = 'environment';

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
        header('Location: environment.php');
        exit;
    }
}

$posts = $repo->search(null, null, $topic);
$topicLabel = 'Environment & Cleanups';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $topicLabel ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="page-content">
    <div class="community-hero">
        <h1><?= htmlspecialchars($topicLabel) ?></h1>
        <p>Waste management, cleanups, and climate-ready communities.</p>
    </div>

    <div class="container">
        <aside class="sidebar">
            <h3>Other Rooms</h3>
            <ul>
                <li><a href="traffic.php">Traffic & Roads</a></li>
                <li><a href="pets.php">Stray Pets</a></li>
                <li><a href="../topics.php">Topics Overview</a></li>
            </ul>
            <div class="sidebar-card">
                <?php if ($user): ?>
                    <form method="post" class="stack-form">
                        <input type="hidden" name="action" value="create_post">
                        <input type="text" name="title" placeholder="Title" required>
                        <textarea name="body" placeholder="Share an update" required></textarea>
                        <input type="text" name="location" placeholder="Location">
                        <input type="text" name="tags" placeholder="Tags (comma separated)">
                        <button type="submit">Post</button>
                    </form>
                <?php else: ?>
                    <p>Please <a href="../login.php">log in</a> to post.</p>
                <?php endif; ?>
            </div>
        </aside>

        <main class="main-content">
            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <div class="post-header">
                        <div>
                            <div class="author"><?= htmlspecialchars($post['author'] ?? 'Neighbor') ?></div>
                            <div class="location"><?= htmlspecialchars($post['location'] ?? '') ?></div>
                        </div>
                        <div class="pill"><?= htmlspecialchars($topicLabel) ?></div>
                    </div>
                    <div class="post-title"><?= htmlspecialchars($post['title'] ?? '') ?></div>
                    <p class="post-body"><?= nl2br(htmlspecialchars($post['body'] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>

