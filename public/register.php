<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
require_once '../config/db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        try {
            $stmt->execute(['username' => $username, 'password' => $hashed_password]);
            $success = "Registration successful. You can now log in.";
        } catch (PDOException $e) {
            $error = "Username already exists. Please choose a different one.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <form method="POST" action="register.php">
        <label>Username:</label>
        <input type="text" name="username" required>
        <br>
        <label>Password:</label>
        <input type="password" name="password" required>
        <br>
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required>
        <br>
        <button type="submit">Register</button>
    </form>
    <p><a href="login.php">Already have an account? Log in</a></p>
</body>
</html>
