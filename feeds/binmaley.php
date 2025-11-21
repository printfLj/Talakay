<?php
    require_once '../includes/init.php';
    $posts = json_decode(file_get_contents("../data/posts.json"), true);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Binmaley</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<div class="page-content">
    <div class="community-hero">
        <h1>Binmaley Feed</h1>
        <p>Local updates and community feed for Binmaley.</p>
    </div>

    <div class="container">
        <div class="main">
            <h2>Latest Posts</h2>
            <?php foreach ($posts as $p): ?>
                <div class="post">
                    <div class="post-header">
                        <span class="author"><?= $p["author"] ?></span>
                        <span class="location"><?= $p["location"] ?></span>
                    </div>
                    <div class="post-title"><?= $p["title"] ?></div>
                    <p class="post-body"><?= $p["body"] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>