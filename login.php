<?php
session_start();

$host = 'localhost';
$username = 'root'; 
$password = ''; 
$database = 'movie_project';

$mysqli_connect = new mysqli($host, $username, $password, $database);

if ($mysqli_connect->connect_error) {
    die("Connection failed: " . $mysqli_connect->connect_error);
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameInput = trim($_POST['username']);
    $passwordInput = $_POST['password'];

    $stmt = $mysqli_connect->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $usernameInput);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($storedPassword);
        $stmt->fetch();

        // Direct password comparison without hashing
        if ($passwordInput === $storedPassword) {
            $_SESSION['isLoggedIn'] = true;
            $_SESSION['username'] = htmlspecialchars($usernameInput);
            header("Location: index.html");
            exit();
        } else {
            $errorMessage = "Invalid credentials, please try again.";
        }
    } else {
        $errorMessage = "User not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Movie Booking System</title>
    <style>
        body {
            background-image: url('akshar-dave-WYCeSzr7SQ4-unsplash (1).jpg');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;   
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            font-size: 18px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            color: #007BFF;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <br><br>
        <a href="forgotpassword.html" class="forgot-password">Forgot Password?</a>
    </form>
</div>

</body>
</html>
