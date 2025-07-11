<?php
include '../includes/db.php';
include '../css/style.css';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_data'] = $user;
        header("Location: profile.php");
        exit();
    } else {
        $error = "❌ Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2 style="text-align: center;">Login</h2>
<form method="POST">
    <label for="username">Username:</label><br>
    <input id="username" name="username" type="text" required><br>

    <label for="password">Password:</label><br>
    <input id="password" name="password" type="password" required><br>

    <?php if (!empty($error)): ?>
        <div style="color: red; margin: 10px 0; font-weight: bold;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <div style="text-align: center; margin: 10px 0;">
        <button type="submit">Login</button>
    </div>
</form>

<p style="text-align: center;">Don't have an account? <a href="register.php">Register here</a></p>

</body>
</html>
