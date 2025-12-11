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

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_post') {
    if (!$user) {
        header('Location: login.php');
        exit;
    }
    $postId = $_POST['post_id'] ?? '';
    if ($postId && $repo->deletePost($postId, $user['email'])) {
        header('Location: community.php?deleted=1');
        exit;
    } else {
        header('Location: community.php?error=1');
        exit;
    }
}

// Handle reply deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_reply') {
    if (!$user) {
        header('Location: login.php');
        exit;
    }
    $postId = $_POST['post_id'] ?? '';
    $replyId = $_POST['reply_id'] ?? '';
    if ($postId && $replyId && $repo->deleteReply($postId, $replyId, $user['email'])) {
        header('Location: community.php?deleted=1');
        exit;
    } else {
        header('Location: community.php?error=1');
        exit;
    }
}

// Handle post editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_post') {
    if (!$user) {
        header('Location: login.php');
        exit;
    }
    $postId = $_POST['post_id'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $topic = $_POST['topic'] ?? 'general';
    $tags = $_POST['tags'] ?? '';
    $location = $_POST['location'] ?? '';

    if ($title !== '' && $body !== '') {
        if ($repo->editPost($postId, [
            'title' => $title,
            'body' => $body,
            'topic' => $topic,
            'tags' => $tags,
            'location' => $location,
        ], $user['email'])) {
            header('Location: community.php?edited=1');
            exit;
        } else {
            header('Location: community.php?error=1');
            exit;
        }
    }
}

// Handle reply editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_reply') {
    if (!$user) {
        header('Location: login.php');
        exit;
    }
    $postId = $_POST['post_id'] ?? '';
    $replyId = $_POST['reply_id'] ?? '';
    $body = trim($_POST['body'] ?? '');

    if ($postId && $replyId && $body !== '') {
        if ($repo->editReply($postId, $replyId, $body, $user['email'])) {
            header('Location: community.php?edited=1');
            exit;
        } else {
            header('Location: community.php?error=1');
            exit;
        }
    }
}

$query = $_GET['q'] ?? null;
$tag = $_GET['tag'] ?? null;
$topic = $_GET['topic'] ?? null;
$posts = $repo->search($query, $tag, $topic);

$discoverTab = $_GET['tab'] ?? 'trending';
$renderTopicsView = false;
if ($discoverTab === 'trending') {
    usort($posts, function ($a, $b) {
        return count($b['replies'] ?? []) <=> count($a['replies'] ?? []);
    });
} elseif ($discoverTab === 'foryou') {
    if ($user) {
        $filtered = array_filter($posts, function ($p) use ($user) {
            if (!empty($p['author_email']) && $p['author_email'] === $user['email']) return true;
            if (!empty($user['location']) && !empty($p['location']) && stripos($p['location'], $user['location']) !== false) return true;
            return false;
        });
        if (!empty($filtered)) {
            $posts = array_values($filtered);
        } else {
            usort($posts, fn($a, $b) => count($b['replies'] ?? []) <=> count($a['replies'] ?? []));
        }
    } else {
        usort($posts, fn($a, $b) => count($b['replies'] ?? []) <=> count($a['replies'] ?? []));
    }
} elseif ($discoverTab === 'topics') {
    $renderTopicsView = true;
} elseif ($discoverTab === 'local') {
    if ($user && !empty($user['location'])) {
        $posts = array_values(array_filter($posts, fn($p) => !empty($p['location']) && stripos($p['location'], $user['location']) !== false));
    } else {
        usort($posts, fn($a, $b) => (empty($b['location']) ? 0 : 1) <=> (empty($a['location']) ? 0 : 1));
    }
}

$topics = [
    'general' => 'General Updates',
    'traffic' => 'Traffic & Roads',
    'pets' => 'Stray Pets & Adoption',
    'environment' => 'Environment & Cleanups',
    'public_safety' => 'Public Safety',
    'local_businesses' => 'Local Businesses',
    'health' => 'Health & Wellness',
];

function render_replies(array $replies, ?string $parentId = null, ?array $currentUser = null, ?string $postId = null, int $depth = 0): void
{
    $children = array_values(array_filter($replies, fn($r) => ($r['parent_id'] ?? null) === $parentId));
    if (empty($children)) {
        return;
    }
    echo '<ul class="reply-list" style="margin-left: ' . ($depth * 20) . 'px;">';
    foreach ($children as $reply) {
        $formId = 'reply-form-' . htmlspecialchars($reply['id']);
        $editFormId = 'edit-reply-form-' . htmlspecialchars($reply['id']);
        ?>
        <li class="reply">
            <div class="reply-header">
                <span class="author">👤 <?= htmlspecialchars($reply['author'] ?? 'Neighbor') ?></span>
                <span class="timestamp"><?= htmlspecialchars($reply['created_at'] ?? '') ?></span>
                <div class="reply-actions">
                    <button class="reply-action-btn" onclick="document.getElementById('<?= $formId ?>').style.display = document.getElementById('<?= $formId ?>').style.display === 'none' ? 'block' : 'none';" title="Reply to this comment">↩️ Reply</button>
                    <?php if ($currentUser && $currentUser['email'] === $reply['author_email']): ?>
                        <button class="reply-action-btn edit" onclick="document.getElementById('<?= $editFormId ?>').style.display = document.getElementById('<?= $editFormId ?>').style.display === 'none' ? 'block' : 'none';" title="Edit reply">✏️ Edit</button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete_reply">
                            <input type="hidden" name="post_id" value="<?= htmlspecialchars($postId ?? '') ?>">
                            <input type="hidden" name="reply_id" value="<?= htmlspecialchars($reply['id']) ?>">
                            <button type="submit" class="reply-action-btn delete" onclick="return confirm('Delete this reply?');" title="Delete reply">🗑️ Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <p class="reply-body"><?= nl2br(htmlspecialchars($reply['body'] ?? '')) ?></p>
            <?php if (!empty($reply['edited_at'])): ?>
                <p class="reply-edited-note">Edited: <?= htmlspecialchars($reply['edited_at']) ?></p>
            <?php endif; ?>
            
            <?php if ($currentUser && $currentUser['email'] === $reply['author_email']): ?>
                <div id="<?= $editFormId ?>" class="edit-reply-form" style="display:none;">
                    <form method="post">
                        <input type="hidden" name="action" value="edit_reply">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($postId ?? '') ?>">
                        <input type="hidden" name="reply_id" value="<?= htmlspecialchars($reply['id']) ?>">
                        <textarea name="body" placeholder="Edit your reply..." required><?= htmlspecialchars($reply['body'] ?? '') ?></textarea>
                        <div class="edit-reply-actions">
                            <button type="submit" class="edit-reply-save-btn">Save</button>
                            <button type="button" class="edit-reply-cancel-btn" onclick="document.getElementById('<?= $editFormId ?>').style.display = 'none';">Cancel</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
            
            <div id="<?= $formId ?>" class="nested-reply-form" style="display:none;">
                <form method="post">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="post_id" value="<?= htmlspecialchars($postId ?? '') ?>">
                    <input type="hidden" name="parent_id" value="<?= htmlspecialchars($reply['id']) ?>">
                    <textarea name="reply_body" placeholder="Write your reply..." required></textarea>
                    <div class="nested-form-actions">
                        <button type="submit" class="nested-reply-btn">Post Reply</button>
                        <button type="button" class="nested-cancel-btn" onclick="document.getElementById('<?= $formId ?>').style.display = 'none';">Cancel</button>
                    </div>
                </form>
            </div>
            
            <?php render_replies($replies, $reply['id'], $currentUser, $postId, $depth + 1); ?>
        </li>
        <?php
    }
    echo '</ul>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <a href="rooms/public_safety.php" class="community-item">
                        <span class="icon">🦺</span>
                        <span class="label">Public Safety</span>
                    </a>
                    <a href="rooms/local_businesses.php" class="community-item">
                        <span class="icon">🛒</span>
                        <span class="label">Local Businesses</span>
                    </a>
                    <a href="rooms/health.php" class="community-item">
                        <span class="icon">❤️‍🩹</span>
                        <span class="label">Health & Wellness</span>
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
                                    <?php if ($user && $user['email'] === $post['author_email']): ?>
                                        <button class="action-btn edit-btn" onclick="document.getElementById('edit-form-<?= htmlspecialchars($post['id']) ?>').style.display = document.getElementById('edit-form-<?= htmlspecialchars($post['id']) ?>').style.display === 'none' ? 'block' : 'none';">✏️ Edit</button>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="action" value="delete_post">
                                            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                                            <button type="submit" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this post?');">🗑️ Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <?php if ($user && $user['email'] === $post['author_email']): ?>
                                    <div id="edit-form-<?= htmlspecialchars($post['id']) ?>" class="edit-post-form" style="display:none;">
                                        <form method="post">
                                            <input type="hidden" name="action" value="edit_post">
                                            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                                            <input type="text" name="title" value="<?= htmlspecialchars($post['title'] ?? '') ?>" placeholder="Post title" required>
                                            <textarea name="body" placeholder="What's on your mind?" required><?= htmlspecialchars($post['body'] ?? '') ?></textarea>
                                            <input type="text" name="location" placeholder="Location (barangay, municipality)" value="<?= htmlspecialchars($post['location'] ?? '') ?>">
                                            <select name="topic">
                                                <option value="">Select a topic</option>
                                                <?php foreach ($topics as $key => $label): ?>
                                                    <option value="<?= $key ?>" <?= ($post['topic'] ?? '') === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="text" name="tags" placeholder="Tags (comma separated)" value="<?= htmlspecialchars(implode(', ', $post['tags'] ?? [])) ?>">
                                            <div class="edit-form-actions">
                                                <button type="submit" class="edit-save-btn">Save Changes</button>
                                                <button type="button" class="edit-cancel-btn" onclick="document.getElementById('edit-form-<?= htmlspecialchars($post['id']) ?>').style.display = 'none';">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($post['edited_at'])): ?>
                                    <p class="post-edited-note">Edited: <?= htmlspecialchars($post['edited_at']) ?></p>
                                <?php endif; ?>

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
                                    render_replies($replies, null, $user, $post['id']);
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
                if (form) form.style.display = form.style.display === 'none' ? 'block' : 'none';
            });
        });
    </script>
</div>
<?php include('includes/footer.php'); ?>
</body>
</html>