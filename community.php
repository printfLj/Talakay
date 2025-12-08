<?php
require_once 'includes/init.php';
require_once 'models/PostRepository.php';

$repo = new PostRepository();
$user = $_SESSION['user'] ?? null;

// Handle new post submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_post') {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $topic = $_POST['topic'] ?? 'general';
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
        header('Location: community.php?created=1');
        exit;
    }
}

// Handle reply submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply') {
    $postId = $_POST['post_id'] ?? '';
    $replyBody = trim($_POST['reply_body'] ?? '');
    $parentId = $_POST['parent_id'] ?? null;

    if ($postId && $replyBody !== '') {
        $repo->addReply($postId, [
            'parent_id' => $parentId ?: null,
            'author' => $user['name'] ?? 'Neighbor',
            'author_email' => $user['email'] ?? null,
            'body' => $replyBody,
        ]);
        header('Location: community.php?replied=1');
        exit;
    }
}

$query = $_GET['q'] ?? null;
$tag = $_GET['tag'] ?? null;
$topic = $_GET['topic'] ?? null;
$posts = $repo->search($query, $tag, $topic);

$topics = [
    'general' => 'General Updates',
    'traffic' => 'Traffic & Roads',
    'pets' => 'Stray Pets & Adoption',
    'environment' => 'Environment & Cleanups',
];

function render_replies(array $replies, ?string $parentId = null): void
{
    $children = array_values(array_filter($replies, fn($r) => ($r['parent_id'] ?? null) === $parentId));
    if (empty($children)) {
        return;
    }
    echo '<ul class="reply-list">';
    foreach ($children as $reply) {
        ?>
        <li class="reply">
            <div class="reply-header">
                <span class="author"><?= htmlspecialchars($reply['author'] ?? 'Neighbor') ?></span>
                <span class="timestamp"><?= htmlspecialchars($reply['created_at'] ?? '') ?></span>
            </div>
            <p><?= nl2br(htmlspecialchars($reply['body'] ?? '')) ?></p>
            <form method="post" class="inline-form">
                <input type="hidden" name="action" value="reply">
                <input type="hidden" name="post_id" value="<?= htmlspecialchars($reply['post_id'] ?? '') ?>">
                <input type="hidden" name="parent_id" value="<?= htmlspecialchars($reply['id']) ?>">
                <textarea name="reply_body" placeholder="Reply to this comment" required></textarea>
                <button type="submit">Reply</button>
            </form>
            <?php render_replies($replies, $reply['id']); ?>
        </li>
        <?php
    }
    echo '</ul>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Talakay Community</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<?php include('includes/nav.php'); ?>

<div class="page-content">
    <div class="community-hero">
        <div>
            <h1>Digital Town Plaza</h1>
            <p>Stay up-to-date with local issues, events, and opportunities across Pangasinan.</p>
        </div>
        <form class="search-bar" method="get">
            <input type="text" name="q" placeholder="Search posts, people, or tags" value="<?= htmlspecialchars($query ?? '') ?>">
            <select name="topic">
                <option value="">All topics</option>
                <?php foreach ($topics as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $topic === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="tag" placeholder="#tag" value="<?= htmlspecialchars($tag ?? '') ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="container">
        <div class="sidebar">
            <h3>Discussion Rooms</h3>
            <ul>
                <li><a href="rooms/traffic.php">Traffic & Roads</a></li>
                <li><a href="rooms/pets.php">Stray Pets & Adoption</a></li>
                <li><a href="rooms/environment.php">Environment</a></li>
                <li><a href="topics.php">View All Topics</a></li>
            </ul>
            <div class="sidebar-card">
                <h4>Start a Post</h4>
                <?php if ($user): ?>
                    <form method="post" class="stack-form">
                        <input type="hidden" name="action" value="create_post">
                        <input type="text" name="title" placeholder="Title" required>
                        <textarea name="body" placeholder="What\'s happening?" required></textarea>
                        <input type="text" name="location" placeholder="Location (e.g., Barangay)">
                        <label>
                            Topic
                            <select name="topic">
                                <?php foreach ($topics as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <input type="text" name="tags" placeholder="Tags (comma separated)">
                        <button type="submit">Post</button>
                    </form>
                <?php else: ?>
                    <p>Please <a href="login.php">log in</a> to create posts.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="main-content">
            <h2>Latest Posts</h2>
            <?php if (empty($posts)): ?>
                <p class="muted">No posts found. Try a different search or topic.</p>
            <?php endif; ?>

            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <div class="post-header">
                        <div>
                            <div class="author"><?= htmlspecialchars($post['author'] ?? 'Neighbor') ?></div>
                            <div class="location"><?= htmlspecialchars($post['location'] ?? '') ?></div>
                        </div>
                        <div class="pill"><?= htmlspecialchars($topics[$post['topic']] ?? 'General') ?></div>
                    </div>
                    <div class="post-title"><?= htmlspecialchars($post['title'] ?? '') ?></div>
                    <p class="post-body"><?= nl2br(htmlspecialchars($post['body'] ?? '')) ?></p>
                    <div class="post-tags">
                        <?php foreach ($post['tags'] ?? [] as $t): ?>
                            <a class="tag" href="?tag=<?= urlencode($t) ?>">#<?= htmlspecialchars($t) ?></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="post-footer">
                        <form method="post" class="stack-form">
                            <input type="hidden" name="action" value="reply">
                            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                            <textarea name="reply_body" placeholder="Reply to this post" required></textarea>
                            <button type="submit">Reply</button>
                        </form>
                    </div>
                    <?php if (!empty($post['replies'])): ?>
                        <?php
                        // Add post_id to replies for reply form context
                        $replies = array_map(function ($reply) use ($post) {
                            $reply['post_id'] = $post['id'];
                            return $reply;
                        }, $post['replies']);
                        render_replies($replies);
                        ?>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>
</body>
</html>