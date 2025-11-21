<?php

require_once 'includes/init.php';

require "User.php";
$user = new User();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $result = $user->register($name, $email, $password);

    if ($result) {
        $message = "Registration Successful";
    } else {
        $message = "Registration Failed";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTER</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include('includes/nav.php'); ?>
    <h2>REGISTER HERE!</h2>
    <p><?= $message ?></p>

    <!-- REGISTER FORM --->
     <form method="post">
        <input type="text" name="name" id="name" placeholder="Full Name" required><br>
        <input type="email" name="email" id="email" placeholder="Email" required><br>
        <input type="password" name="password" id="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
     </form>
    
     <p>Already have an account? <a href="login.php">Login here.</a></p>
</body>
</html>