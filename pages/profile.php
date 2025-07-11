<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<?php
include '../includes/db.php';
include '../includes/functions.php';
include '../css/style.css';
session_start();
redirectIfNotLoggedIn();

$user = $_SESSION['user_data'];

if (isset($_POST['delete'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    session_destroy();
    header("Location: register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $weight = $_POST['weight'];
    $height = $_POST['height'];
    $diabetes = isset($_POST['diabetes']) ? 1 : 0;
    $cholesterol = isset($_POST['cholesterol']) ? 1 : 0;
    $hypertension = isset($_POST['hypertension']) ? 1 : 0;
    $restrictions = isset($_POST['restrictions']) ? implode(",", $_POST['restrictions']) : '';

    $stmt = $conn->prepare("UPDATE users SET weight=?, height=?, condition_diabetes=?, condition_cholesterol=?, condition_hypertension=?, dietary_restrictions=? WHERE id=?");
    $stmt->bind_param("ddiiisi", $weight, $height, $diabetes, $cholesterol, $hypertension, $restrictions, $user['id']);
    $stmt->execute();

    $res = $conn->query("SELECT * FROM users WHERE id=" . $user['id']);
    $_SESSION['user_data'] = $res->fetch_assoc();
    $user = $_SESSION['user_data'];

    echo "<p style='color:green;'>Profile updated successfully.</p>";
}
?>

<h2 style="text-align: center;">User Profile</h2>


<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly><br><br>

    <label>Weight (kg):</label><br>
    <input type="number" step="0.1" name="weight" value="<?= htmlspecialchars($user['weight']) ?>"><br><br>

    <label>Height (cm):</label><br>
    <input type="number" step="0.1" name="height" value="<?= htmlspecialchars($user['height']) ?>"><br><br>

    <label>Dietary Restrictions:</label><br>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="sugar" class="substituted" name="restrictions[]" value="sugar"
               <?= strpos($user['dietary_restrictions'], 'sugar') !== false ? 'checked' : '' ?>>
        <label for="sugar">Sugar</label>
    </div>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="salt" class="substituted" name="restrictions[]" value="salt"
               <?= strpos($user['dietary_restrictions'], 'salt') !== false ? 'checked' : '' ?>>
        <label for="salt">Salt</label>
    </div>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="fat" class="substituted" name="restrictions[]" value="fat"
               <?= strpos($user['dietary_restrictions'], 'fat') !== false ? 'checked' : '' ?>>
        <label for="fat">Fat</label>
    </div><br>

    <label>Health Conditions:</label><br>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="diabetes" class="substituted" name="diabetes"
               <?= $user['condition_diabetes'] ? 'checked' : '' ?>>
        <label for="diabetes">Diabetes</label>
    </div>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="cholesterol" class="substituted" name="cholesterol"
               <?= $user['condition_cholesterol'] ? 'checked' : '' ?>>
        <label for="cholesterol">Cholesterol</label>
    </div>
    <div class="checkbox-wrapper-orange">
        <input type="checkbox" id="hypertension" class="substituted" name="hypertension"
               <?= $user['condition_hypertension'] ? 'checked' : '' ?>>
        <label for="hypertension">Hypertension</label>
    </div><br>

    <div style="text-align: center;">
  <button type="submit" name="update">Update Profile</button>
</div>

</form>


<form method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
<div style="text-align: center;">    
<button style="align-self: center;" type="submit" name="delete" class="delete-button">
        Delete Account
    </button>
</div>
</form>
<style>
    .delete-button {
  background-color: #d9534f;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.delete-button:hover {
  background-color: #c9302c;
}
</style>

<p style="text-align: center;"><a href="search.php">← Back to Search</a> | <a href="logout.php">Logout</a></p>
