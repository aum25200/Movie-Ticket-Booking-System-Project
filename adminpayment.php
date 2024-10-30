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

// Fetch movie titles for dropdown
$movies = [];
$stmt = $mysqli_connect->prepare("SELECT title FROM movies");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $movies[] = $row['title'];
}
$stmt->close();

// Handle form submission for creating and updating records
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_id'])) {
        // Deleting a payment record
        $delete_id = $_POST['delete_id'];
        $stmt = $mysqli_connect->prepare("DELETE FROM payments WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['edit_id'])) {
        // Loading data for editing
        $edit_id = $_POST['edit_id'];
        $stmt = $mysqli_connect->prepare("SELECT * FROM payments WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $payment_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } elseif (isset($_POST['full_name'])) {
        // Saving a new or updated payment record
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip_code = $_POST['zip_code'];
        $card_name = $_POST['card_name'];
        $card_number = $_POST['card_number'];
        $expiry_month = $_POST['expiry_month'];
        $expiry_year = $_POST['expiry_year'];
        $cvv = $_POST['cvv'];
        $selected_seats = $_POST['selected_seats'];
        $total_price = $_POST['total_price'];
        $movie_title = $_POST['movie_title'];
        $showtime = $_POST['showtime'];
        $expiry = "$expiry_month/$expiry_year";

        if (!empty($_POST['id'])) {
            // Update existing record
            $id = $_POST['id'];
            $stmt = $mysqli_connect->prepare("UPDATE payments SET full_name=?, email=?, address=?, city=?, state=?, zip_code=?, card_name=?, card_number=?, expiry=?, cvv=?, movie_title=?, selected_seats=?, total_price=?, showtime=? WHERE id=?");
            $stmt->bind_param("ssssssssssssssi", $full_name, $email, $address, $city, $state, $zip_code, $card_name, $card_number, $expiry, $cvv, $movie_title, $selected_seats, $total_price, $showtime, $id);
        } else {
            // Insert new record
            $stmt = $mysqli_connect->prepare("INSERT INTO payments (full_name, email, address, city, state, zip_code, card_name, card_number, expiry, cvv, movie_title, selected_seats, total_price, showtime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssssss", $full_name, $email, $address, $city, $state, $zip_code, $card_name, $card_number, $expiry, $cvv, $movie_title, $selected_seats, $total_price, $showtime);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Operation successful!'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all payments for display
$payments = [];
$stmt = $mysqli_connect->prepare("SELECT * FROM payments");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Payment Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Your existing CSS styles */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        h4 {
            margin-bottom: 15px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007BFF;
            color: white;
            font-weight: 500;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        button {
            padding: 8px 15px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .edit-btn {
            background-color: #28a745;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .receipt-btn {
            background-color: #17a2b8;
        }
        .edit-btn:hover {
            background-color: #218838;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .receipt-btn:hover {
            background-color: #138496;
        }
        .form-container {
            margin: 20px 0;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .form-container button {
            background-color: #007BFF;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .form-container input:focus, .form-container select:focus {
            border-color: #007BFF;
            outline: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Payments Records</h2>

    <form class="form-container" method="POST" action="">
        <h4><?= isset($payment_data) ? 'Edit Payment Record' : 'Add New Payment Record' ?></h4>
        <input type="hidden" name="id" value="<?= isset($payment_data) ? $payment_data['id'] : '' ?>">
        <input type="text" name="full_name" placeholder="Full Name" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['full_name']) : '' ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['email']) : '' ?>" required>
        <input type="text" name="address" placeholder="Address" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['address']) : '' ?>" required>
        <input type="text" name="city" placeholder="City" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['city']) : '' ?>" required>
        <input type="text" name="state" placeholder="State" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['state']) : '' ?>" required>
        <input type="text" name="zip_code" placeholder="Zip Code" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['zip_code']) : '' ?>" required>
        <input type="text" name="card_name" placeholder="Name on Card" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['card_name']) : '' ?>" required>
        <input type="text" name="card_number" placeholder="Card Number" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['card_number']) : '' ?>" required>
        <input type="text" name="expiry_month" placeholder="Expiry Month (MM)" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['expiry']) : '' ?>" required>
        <input type="text" name="expiry_year" placeholder="Expiry Year (YY)" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['expiry']) : '' ?>" required>
        <input type="text" name="cvv" placeholder="CVV" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['cvv']) : '' ?>" required>
        <select name="movie_title" required>
            <option value="" disabled selected>Select Movie</option>
            <?php foreach ($movies as $movie): ?>
                <option value="<?= htmlspecialchars($movie) ?>" <?= (isset($payment_data) && $payment_data['movie_title'] === $movie) ? 'selected' : '' ?>><?= htmlspecialchars($movie) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="selected_seats" placeholder="Selected Seats" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['selected_seats']) : '' ?>" required>
        <input type="text" name="total_price" placeholder="Total Price" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['total_price']) : '' ?>" required>
        <input type="text" name="showtime" placeholder="Showtime" value="<?= isset($payment_data) ? htmlspecialchars($payment_data['showtime']) : '' ?>" required>
        <button type="submit"><?= isset($payment_data) ? 'Update Record' : 'Add Record' ?></button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Movie Title</th>
                <th>Selected Seats</th>
                <th>Total Price</th>
                <th>Showtime</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= htmlspecialchars($payment['id']) ?></td>
                    <td><?= htmlspecialchars($payment['full_name']) ?></td>
                    <td><?= htmlspecialchars($payment['email']) ?></td>
                    <td><?= htmlspecialchars($payment['movie_title']) ?></td>
                    <td><?= htmlspecialchars($payment['selected_seats']) ?></td>
                    <td>Rs <?= number_format($payment['total_price'], 2) ?></td>
                    <td><?= htmlspecialchars($payment['showtime']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($payment['id']) ?>">
                            <button type="submit" class="edit-btn">Edit</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= htmlspecialchars($payment['id']) ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
                        </form><br><br>
                        <form method="GET" action="adminreceipt.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($payment['id']) ?>">
                            <button type="submit" class="receipt-btn">Generate Receipt</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
