<?php
require_once 'includes/init.php';
require_once 'models/SocialGraph.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$graph = new SocialGraph();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_friend') {
    $friendEmail = trim($_POST['friend_email'] ?? '');
    if ($friendEmail !== '' && $graph->addFriend($user['email'], $friendEmail)) {
        $message = 'Friend added!';
    } else {
        $message = 'Could not add friend. Please check the email.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send_message') {
    $to = $_POST['to'] ?? '';
    $body = trim($_POST['body'] ?? '');
    if ($to && $body !== '') {
        $graph->sendMessage($user['email'], $to, $body);
        header('Location: profile.php?friend=' . urlencode($to));
        exit;
    }
}

$friends = $graph->getFriends($user['email']);
$activeFriend = $_GET['friend'] ?? ($friends[0] ?? null);
$conversation = $activeFriend ? $graph->getConversation($user['email'], $activeFriend) : [];
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

<?php include('includes/nav.php'); ?>

<div class="profile-page">
    <!-- HEADER -->
    <div class="profile-header">
        <div class="profile-info">
            <h1>Welcome, <?= htmlspecialchars($user['name'] ?? 'User') ?></h1>
            <p class="profile-email">📧 <?= htmlspecialchars($user['email'] ?? '') ?></p>
            <?php if (!empty($user['created_at'])): ?>
                <p class="profile-meta">Member since <?= htmlspecialchars($user['created_at']) ?></p>
            <?php endif; ?>
        </div>
        <a class="btn logout-btn" href="logout.php">Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="profile-container">
        <!-- LEFT SIDEBAR: FRIENDS -->
        <aside class="friends-sidebar">
            <div class="sidebar-card">
                <h3>Your Friends</h3>
                <?php if ($message): ?>
                    <div class="alert <?= strpos($message, 'could not') !== false ? 'alert-error' : 'alert-success' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                <form method="post" class="add-friend-form">
                    <input type="hidden" name="action" value="add_friend">
                    <div class="form-group">
                        <input type="email" name="friend_email" placeholder="Enter neighbor's email" required>
                        <button type="submit" class="btn-small">+ Add Friend</button>
                    </div>
                </form>
                <div class="friends-section">
                    <?php if (empty($friends)): ?>
                        <p class="muted">No friends yet. Add someone using their email to start messaging.</p>
                    <?php else: ?>
                        <ul class="friends-list">
                            <?php foreach ($friends as $f): ?>
                                <li>
                                    <a class="friend-item <?= $activeFriend === $f ? 'active' : '' ?>" href="profile.php?friend=<?= urlencode($f) ?>">
                                        <span class="friend-icon">👤</span>
                                        <span class="friend-email"><?= htmlspecialchars($f) ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </aside>

        <!-- RIGHT MAIN: MESSAGES -->
        <section class="messages-main">
            <?php if (!$activeFriend): ?>
                <div class="empty-chat">
                    <p class="empty-icon">💬</p>
                    <h3>No conversation selected</h3>
                    <p>Add a friend or select one from the list to start messaging.</p>
                </div>
            <?php else: ?>
                <div class="chat-header">
                    <h3>Chat with <?= htmlspecialchars($activeFriend) ?></h3>
                </div>
                <div class="chat-area">
                    <div class="conversation">
                        <?php if (empty($conversation)): ?>
                            <div class="conversation-empty">
                                <p>👋 Say hello! Start a conversation.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($conversation as $msg): ?>
                                <div class="message-bubble <?= $msg['from'] === $user['email'] ? 'from-me' : 'from-them' ?>">
                                    <div class="message-content">
                                        <p><?= nl2br(htmlspecialchars($msg['body'] ?? '')) ?></p>
                                    </div>
                                    <div class="message-time"><?= htmlspecialchars($msg['created_at'] ?? '') ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <form method="post" class="message-form">
                    <input type="hidden" name="action" value="send_message">
                    <input type="hidden" name="to" value="<?= htmlspecialchars($activeFriend) ?>">
                    <div class="message-input-group">
                        <textarea name="body" placeholder="Type your message..." required></textarea>
                        <button type="submit" class="btn-send">Send</button>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
