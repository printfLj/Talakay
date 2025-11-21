<?php 
    include('includes/nav.php');

    require "User.php";

    session_start();

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
    <title>LOGIN</title>
</head>
<body>
    <section class="login-title"></div>
        <h2>LOGIN HERE!</h2>
        <p><?= $message ?></p>

        <div class="Login-input"></div>
            <!-- LOGIN FORM -->
            <form method="post">
            <input type="email" name="email" id="email" placeholder="Email" required><br>
            <input type="password" name="password" id="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
            </form>

            <p>Don't have an Account? <a href="register.php">Register here.</a></p>
        
        </div>
    </section>
<?php
    include('includes/footer.php');
?>
</body>
</html>