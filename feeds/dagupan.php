<?php
    require_once '../includes/init.php';
    $posts = json_decode(file_get_contents("data/posts.json"), true);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dagupan</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include('../includes/nav.php'); ?>

<div class="page-content">

    <div class="community-hero">
        <h1>Digital Town Plaza</h1>
        <p>A place to stay up-to-date with news, information, and opportunities for special interest groups in our community, such as businesses, libraries, and youth.</p>
    </div>

    <div class="container">
    
        <!-- SIDEBAR -->
        <div class="sidebar">
            <h3>Communities</h3>
            <ul>
                <li><a href="dagupan.php">Dagupan City</a></li>
                <li>Lingayen</li>
                <li><a href="binmaley.php">Binmaley</a></li>
                <li>San Fabian</li>
                <li>Calasiao</li>
            </ul>
        </div>

        <!-- MAIN FEED -->
        <div class="main">
            <h2>Dagupan City – Community Feed</h2>

            <?php foreach($posts as $p): ?>
                <div class="post">
                    <div class="post-header">
                        <span class="author"><?= $p["author"] ?></span>
                        <span class="location"><?= $p["location"] ?></span>
                    </div>

                    <div class="post-title"><?= $p["title"] ?></div>
                        <p class="post-body"><?= $p["body"] ?></p>

                        <div class="post-footer">
                            <button>👍 Agree</button>
                            <button>❤️ Support</button>
                            <button>⚠️ Needs Attention</button>
                        </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Add more cards as needed -->
    </div>

</div>
<?php
    include('../includes/footer.php');
?>
</body>
</html>
