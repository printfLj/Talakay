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

<main class="profile-page">
    <section class="profile-card">
        <h2>Welcome, <?= htmlspecialchars($user['name'] ?? 'User') ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
        <?php if (!empty($user['created_at'])): ?>
            <p><strong>Member since:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
        <?php endif; ?>
        <a class="btn" href="logout.php">Logout</a>
    </section>

    <section class="profile-card">
        <h3>Friends</h3>
        <?php if ($message): ?>
            <p class="status"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="post" class="stack-form">
            <input type="hidden" name="action" value="add_friend">
            <input type="email" name="friend_email" placeholder="Neighbor's email" required>
            <button type="submit">Add Friend</button>
        </form>
        <div class="friend-list">
            <?php if (empty($friends)): ?>
                <p class="muted">No friends yet. Add someone using their email.</p>
            <?php endif; ?>
            <?php foreach ($friends as $f): ?>
                <a class="friend-pill <?= $activeFriend === $f ? 'active' : '' ?>" href="profile.php?friend=<?= urlencode($f) ?>">
                    <?= htmlspecialchars($f) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="profile-card">
        <h3>Messages</h3>
        <?php if (!$activeFriend): ?>
            <p class="muted">Add a friend to start a conversation.</p>
        <?php else: ?>
            <div class="conversation">
                <?php if (empty($conversation)): ?>
                    <p class="muted">No messages yet.</p>
                <?php else: ?>
                    <?php foreach ($conversation as $msg): ?>
                        <div class="message <?= $msg['from'] === $user['email'] ? 'from-me' : 'from-them' ?>">
                            <div class="meta">
                                <span><?= htmlspecialchars($msg['from']) ?></span>
                                <span><?= htmlspecialchars($msg['created_at'] ?? '') ?></span>
                            </div>
                            <p><?= nl2br(htmlspecialchars($msg['body'] ?? '')) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <form method="post" class="stack-form">
                <input type="hidden" name="action" value="send_message">
                <input type="hidden" name="to" value="<?= htmlspecialchars($activeFriend) ?>">
                <textarea name="body" placeholder="Send a message" required></textarea>
                <button type="submit">Send</button>
            </form>
        <?php endif; ?>
    </section>
</main>

<?php include('includes/footer.php'); ?>
</body>
</html>
