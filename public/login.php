<?php
require_once '../src/config/db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
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
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .password-toggle {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="card p-4">
        <h4 class="text-center mb-4">Login</h4>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
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
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <div class="text-center mt-3">
                <a href="register.php">Don't have an account? Register</a>
            </div>
        </form>
    </div>
    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.querySelector('.password-toggle');

        passwordToggle.addEventListener('click', () => {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            passwordToggle.textContent = type === 'password' ? '👁️' : '👁‍🗨';
        });
    </script>
</body>
</html>
