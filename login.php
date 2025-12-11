<?php 
    require_once 'includes/init.php';
    require 'User.php';

    $user = new User();
    $message = "";

    if (isset($_SESSION["user"])) {
        header("Location: index.php");
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $loggedIn = $user->login($email, $password);

        if ($loggedIn) {
            $_SESSION["user"] = $loggedIn;
            header("Location: index.php");
            exit;
        } else {
            $message = "Invalid login credentials.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include('includes/nav.php'); ?>
    <section class="auth-page">
        <div class="auth-card">
            <h2>Login to Talakay</h2>
            <p><?= htmlspecialchars($message) ?></p>

            <div class="Login-input">
                <form method="post" class="stack-form">
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>

                <p class="login-margin">Don't have an Account? <a href="register.php">Register here.</a></p>
            </div>
        </div>
    </section>
<?php include('includes/footer.php'); ?>
</body>
</html>