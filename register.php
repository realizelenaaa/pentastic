<?php
include('config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $username, $password, $role_id);
        if ($stmt->execute()) {
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "Error registering user.";
        }
    }
}

$roles = $conn->query("SELECT * FROM roles");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register - Pentastic</title>
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

        .register-container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
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
        input[type="password"],
        select {
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            padding: 12px;
            background-color: #28a745;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        .login-link {
            margin-top: 16px;
            text-align: center;
            font-size: 14px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            text-align: center;
            margin-top: 10px;
            font-weight: 600;
        }

        .success-message {
            color: #28a745;
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
    <?php if (isset($success)) echo '<meta http-equiv="refresh" content="3;url=login.php">'; ?>
</head>

<body>
    <div class="register-container">
        <h2>Create Your Pentastic Account</h2>
        <form method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Choose a username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>

            <label for="role_id">Role</label>
            <select id="role_id" name="role_id" required>
                <?php while ($row = $roles->fetch_assoc()) { ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['role_name']) ?></option>
                <?php } ?>
            </select>

            <button type="submit">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success-message'>$success</p>"; ?>
    </div>
</body>

</html>