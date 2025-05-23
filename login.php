<?php
session_start();
include('config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use INNER JOIN to authenticate and also fetch the role of the user
    $stmt = $conn->prepare("
        SELECT users.*, roles.role_name 
        FROM users 
        INNER JOIN roles ON users.role_id = roles.id 
        WHERE users.username = ?
    ");
    // This INNER JOIN lets us fetch both user data and their associated role name in one query
    // which is critical for role-based redirection and permissions.

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_name'];

        // Redirect based on role
        if ($_SESSION['role'] === 'Admin') {
            header("Location: admin_dashboard.php");
            exit;
        } else {
            header("Location: user_dashboard.php");
            exit;
        }
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login - Pentastic</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        input[type="password"] {
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .register-link {
            margin-top: 16px;
            text-align: center;
            font-size: 14px;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            text-align: center;
            margin-top: 10px;
            font-weight: 600;
        }

        form label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
            display: block;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login to Pentastic</h2>
        <form method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>

            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </form>

        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
    </div>
</body>

</html>