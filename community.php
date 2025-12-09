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

// Discover tab handling (integrated into community): trending, foryou, topics, local
$discoverTab = $_GET['tab'] ?? 'trending';
$renderTopicsView = false;
if ($discoverTab === 'trending') {
    usort($posts, function ($a, $b) {
        return count($b['replies'] ?? []) <=> count($a['replies'] ?? []);
    });
} elseif ($discoverTab === 'foryou') {
    if ($user) {
        $userLocation = $user['location'] ?? null;
        $userEmail = $user['email'] ?? null;
        $filtered = array_filter($posts, function ($p) use ($userLocation, $userEmail) {
            if (!empty($p['author_email']) && $p['author_email'] === $userEmail) return true;
            if ($userLocation && !empty($p['location']) && stripos($p['location'], $userLocation) !== false) return true;
            return false;
        });
        if (!empty($filtered)) {
            $posts = array_values($filtered);
        } else {
            usort($posts, function ($a, $b) {
                return count($b['replies'] ?? []) <=> count($a['replies'] ?? []);
            });
        }
    } else {
        usort($posts, function ($a, $b) {
            return count($b['replies'] ?? []) <=> count($a['replies'] ?? []);
        });
    }
} elseif ($discoverTab === 'topics') {
    $renderTopicsView = true;
} elseif ($discoverTab === 'local') {
    if ($user && !empty($user['location'])) {
        $loc = $user['location'];
        $posts = array_values(array_filter($posts, function ($p) use ($loc) {
            return !empty($p['location']) && stripos($p['location'], $loc) !== false;
        }));
    } else {
        usort($posts, function ($a, $b) {
            return (empty($b['location']) ? 0 : 1) <=> (empty($a['location']) ? 0 : 1);
        });
    }
}

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
            <h1>Your Digital Town Plaza</h1>
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

    <div class="reddit-container">
        <!-- Left Sidebar -->
        <div class="reddit-sidebar">
            <div class="sidebar-section create-post-box">
                <h4>Create Post</h4>
                <?php if ($user): ?>
                    <p class="user-greeting">Welcome, <?= htmlspecialchars($user['name'] ?? 'Neighbor') ?></p>
                    <form method="post" class="stack-form create-form">
                        <input type="hidden" name="action" value="create_post">
                        <input type="text" name="title" placeholder="Post title" required>
                        <textarea name="body" placeholder="What's on your mind?" required></textarea>
                        <input type="text" name="location" placeholder="Location (barangay, municipality)">
                        <select name="topic">
                            <option value="">Select a topic</option>
                            <?php foreach ($topics as $key => $label): ?>
                                <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="tags" placeholder="Tags (comma separated)">
                        <button type="submit" class="create-btn">Post</button>
                    </form>
                <?php else: ?>
                    <p class="login-prompt">Log in to create posts and engage with the community</p>
                    <a href="login.php" class="create-btn">Log In</a>
                <?php endif; ?>
            </div>

            <div class="sidebar-section">
                <h4>Communities</h4>
                <div class="community-list">
                    <a href="rooms/traffic.php" class="community-item">
                        <span class="icon">🚗</span>
                        <span class="label">Traffic & Roads</span>
                    </a>
                    <a href="rooms/pets.php" class="community-item">
                        <span class="icon">🐾</span>
                        <span class="label">Stray Pets</span>
                    </a>
                    <a href="rooms/environment.php" class="community-item">
                        <span class="icon">🌱</span>
                        <span class="label">Environment</span>
                    </a>
                    <a href="?tab=topics" class="community-item">
                        <span class="icon">📋</span>
                        <span class="label">All Topics</span>
                    </a>
                </div>
            </div>

            <div class="sidebar-section">
                <h4>Filters</h4>
                <form method="get" class="filter-form">
                    <select name="topic" class="filter-select">
                        <option value="">All Topics</option>
                        <?php foreach ($topics as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $topic === $key ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Apply</button>
                </form>
            </div>
        </div>

        <!-- Main Feed -->
        <div class="reddit-feed">
            <div class="feed-header">
                <div>
                    <h2>Community Feed</h2>
                    <p class="feed-subtitle">Latest discussions from your community</p>
                </div>
                <div class="discover-controls">
                    <div class="discover-tabs">
                        <a href="?tab=trending" class="<?= ($discoverTab === 'trending') ? 'active-tab' : '' ?>">Trending</a>
                        <a href="?tab=foryou" class="<?= ($discoverTab === 'foryou') ? 'active-tab' : '' ?>">For You</a>
                        <a href="?tab=topics" class="<?= ($discoverTab === 'topics') ? 'active-tab' : '' ?>">Topics</a>
                        <a href="?tab=local" class="<?= ($discoverTab === 'local') ? 'active-tab' : '' ?>">Local</a>
                    </div>
                    <form class="search-box" method="get">
                    <input type="text" name="q" placeholder="Search posts..." value="<?= htmlspecialchars($query ?? '') ?>">
                    <input type="text" name="tag" placeholder="#tag" value="<?= htmlspecialchars($tag ?? '') ?>">
                    <button type="submit">Search</button>
                    </form>
                </div>
                </form>
            </div>

            <div class="posts-container">
                <?php if ($renderTopicsView): ?>
                    <div class="discussion-grid">
                        <?php foreach ($topics as $key => $label): ?>
                            <div class="discussion-card">
                                <h3><?= htmlspecialchars($label) ?></h3>
                                <p class="muted">Explore posts and discussions about <?= htmlspecialchars($label) ?>.</p>
                                <a class="view-btn" href="?topic=<?= urlencode($key) ?>">View</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <?php if (empty($posts)): ?>
                        <div class="empty-state">
                            <p>No posts found. Try adjusting your filters or be the first to post!</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($posts as $post): ?>
                        <article class="reddit-post">
                            <div class="post-vote-section">
                                <button class="vote-btn up" title="Upvote">▲</button>
                                <span class="vote-count">0</span>
                                <button class="vote-btn down" title="Downvote">▼</button>
                            </div>

                            <div class="post-content">
                                <div class="post-meta">
                                    <span class="community-tag"><?= htmlspecialchars($topics[$post['topic']] ?? 'General') ?></span>
                                    <span class="post-author">Posted by <strong><?= htmlspecialchars($post['author'] ?? 'Neighbor') ?></strong></span>
                                    <?php if (!empty($post['location'])): ?>
                                        <span class="post-location">📍 <?= htmlspecialchars($post['location']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="post-title"><?= htmlspecialchars($post['title'] ?? '') ?></h3>
                                <p class="post-body"><?= nl2br(htmlspecialchars($post['body'] ?? '')) ?></p>

                                <?php if (!empty($post['tags'])): ?>
                                    <div class="post-tags">
                                        <?php foreach ($post['tags'] ?? [] as $t): ?>
                                            <a class="tag-badge" href="?tag=<?= urlencode($t) ?>">#<?= htmlspecialchars($t) ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="post-actions">
                                    <button class="action-btn comment-btn" data-post-id="<?= htmlspecialchars($post['id']) ?>">
                                        💬 Comment
                                    </button>
                                    <button class="action-btn share-btn">📤 Share</button>
                                </div>

                            <?php if (!empty($post['replies'])): ?>
                                <div class="replies-section">
                                    <div class="replies-header">
                                        <span class="reply-count"><?= count($post['replies']) ?> Comments</span>
                                    </div>
                                    <?php
                                    $replies = array_map(function ($reply) use ($post) {
                                        $reply['post_id'] = $post['id'];
                                        return $reply;
                                    }, $post['replies']);
                                    render_replies($replies);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <div class="reply-form-container" id="reply-<?= htmlspecialchars($post['id']) ?>" style="display:none;">
                                <form method="post" class="reply-form">
                                    <input type="hidden" name="action" value="reply">
                                    <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                                    <textarea name="reply_body" placeholder="What are your thoughts?" required></textarea>
                                    <div class="reply-actions">
                                        <button type="submit" class="reply-btn">Comment</button>
                                        <button type="button" class="cancel-btn" onclick="document.getElementById('reply-<?= htmlspecialchars($post['id']) ?>').style.display='none'">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.comment-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const postId = this.dataset.postId;
                const form = document.getElementById('reply-' + postId);
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            });
        });
    </script>
</div>
<?php include('includes/footer.php'); ?>
</body>
</html>