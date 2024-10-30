<?php
$host = 'localhost';
$username = 'root'; 
$password = ''; 
$database = 'movie_project';

$mysqli_connect = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli_connect->connect_error) {
    die("Connection failed: " . $mysqli_connect->connect_error);
}

$userExists = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve POST data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Will hash later
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $termsAccepted = isset($_POST['terms']);

    if (!$termsAccepted) {
        $userExists = true;
    } else {
        // Check if the username or email already exists
        $stmt = $mysqli_connect->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $userExists = true; // User already exists
        } else {
            // Hash the password before storing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Store user data securely
            $stmt = $mysqli_connect->prepare("INSERT INTO users (username, email, password, age, gender) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $username, $email, $hashedPassword, $age, $gender);
            if ($stmt->execute()) {
                header("Location: login.php"); // Redirect to login page
                exit();
            } else {
                echo "Error: " . $stmt->error; // Error handling
            }
        }
        $stmt->close();
    }
}

$mysqli_connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Movie Booking System</title>
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
        .signup-container {
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
        input, select {
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
            background-color: #28a745;
            color: white;
            border: none;
            font-size: 18px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            display: <?= $userExists ? 'block' : 'none'; ?>;
        }
    </style>
</head>
<body>

    <div class="signup-container">
        <h2>Create an Account</h2>
        <p class="error">User already exists or terms not accepted. Please log in or agree to the terms.</p>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="number" name="age" placeholder="Age" min="13" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <label>
                <input type="checkbox" name="terms" required> I agree to the <a href="#">terms and conditions</a>.
            </label>
            <button type="submit">Create Account</button>
        </form>
    </div>

</body>
</html>
