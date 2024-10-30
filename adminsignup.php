<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'movie_project';

// Create connection
$mysqli_connect = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli_connect->connect_error) {
    die("Connection failed: " . $mysqli_connect->connect_error);
}

// Handle form submission for deleting users
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    // Delete user
    $userId = $_POST['delete_id'];
    $stmt = $mysqli_connect->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
}

// Fetch all users for display
$users = [];
$stmt = $mysqli_connect->prepare("SELECT id, username, email, password, age, gender FROM users");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();
$mysqli_connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Movie Booking System</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f0f2f5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        button {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .form-container {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }
    </style>
</head>
<body>

<h2>User Accounts Data</h2>

<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Password</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="userTable">
        <?php foreach ($users as $user): ?>
            <tr id="user-<?= $user['id']; ?>">
                <td><?= htmlspecialchars($user['username']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td><?= htmlspecialchars($user['password']); ?></td>
                <td><?= htmlspecialchars($user['age']); ?></td>
                <td><?= htmlspecialchars($user['gender']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($user['id']); ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
