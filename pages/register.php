<?php
include '../css/style.css';
include '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $weight = $_POST['weight'];
    $height = $_POST['height'];
    $restrictions = isset($_POST['restrictions']) ? implode(",", $_POST['restrictions']) : '';
    $diabetes = isset($_POST['diabetes']) ? 1 : 0;
    $cholesterol = isset($_POST['cholesterol']) ? 1 : 0;
    $hypertension = isset($_POST['hypertension']) ? 1 : 0;

    $sql = "INSERT INTO users (username, email, password, weight, height, dietary_restrictions, condition_diabetes, condition_cholesterol, condition_hypertension)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssddsiii", $username, $email, $password, $weight, $height, $restrictions, $diabetes, $cholesterol, $hypertension);
    $stmt->execute();

    // Fetch newly registered user
    $id = $stmt->insert_id;
    $res = $conn->query("SELECT * FROM users WHERE id = $id");
    $_SESSION['user_id'] = $id;
    $_SESSION['user_data'] = $res->fetch_assoc();

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2 style="text-align: center;">Register</h2>
<form method="POST">
    <label for="username">Username:</label>
    <input id="username" name="username" type="text" required>

    <label for="email">Email:</label>
    <input id="email" name="email" type="email" required>

    <label for="password">Password:</label>
    <input id="password" name="password" type="password" required minlength="8">

    <label for="weight">Weight (kg):</label>
    <input id="weight" name="weight" type="number" step="0.1" required>

    <label for="height">Height (cm):</label>
    <input id="height" name="height" type="number" step="0.1" required>

    <label>Dietary Restrictions:</label><br>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="sugar" class="substituted" name="restrictions[]" value="sugar">
        <label for="sugar">Sugar</label>
    </div>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="salt" class="substituted" name="restrictions[]" value="salt">
        <label for="salt">Salt</label>
    </div>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="fat" class="substituted" name="restrictions[]" value="fat">
        <label for="fat">Fat</label>
    </div><br>

    <label>Health Conditions:</label><br>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="diabetes" class="substituted" name="diabetes">
        <label for="diabetes">Diabetes</label>
    </div>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="cholesterol" class="substituted" name="cholesterol">
        <label for="cholesterol">Cholesterol</label>
    </div>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="hypertension" class="substituted" name="hypertension">
        <label for="hypertension">Hypertension</label>
    </div><br>
    <div style="text-align: center; margin: 10px 0;">
        <button type="submit">Register</button>
    </div>
    
</form>


<p style="text-align: center;">Already have an account? <a href="login.php">Login here</a></p>

</body>
</html>
