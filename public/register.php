<?php
require_once '../src/config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e9ecef;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            width: 100%;
            max-width: 400px;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .password-toggle {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="card p-4">
        <h4 class="text-center mb-4">Register</h4>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <span class="password-toggle position-absolute top-50 end-0 translate-middle-y me-3">👁️</span>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Register</button>
            <div class="text-center mt-3">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </form>
    </div>
    <script>
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        const passwordToggles = document.querySelectorAll('.password-toggle');

        passwordToggles.forEach((toggle, index) => {
            toggle.addEventListener('click', () => {
                const type = passwordInputs[index].type === 'password' ? 'text' : 'password';
                passwordInputs[index].type = type;
                toggle.textContent = type === 'password' ? '👁️' : '👁‍🗨';
            });
        });
    </script>
</body>
</html>
