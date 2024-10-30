<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'movie_project';

$mysqli_connect = new mysqli($host, $username, $password, $database);

if ($mysqli_connect->connect_error) {
    die("Connection failed: " . $mysqli_connect->connect_error);
}

// Fetch receipt details from the database using the provided ID
$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $mysqli_connect->prepare("SELECT * FROM payments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $receipt_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Prepare data for the receipt
    if (!$receipt_data) {
        die("Receipt not found.");
    }
} else {
    die("Invalid request.");
}

$mysqli_connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f9fc;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        h1 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }
        h3 {
            text-align: center;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .section p {
            margin: 5px 0;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            color: #007BFF;
            border-top: 2px solid #007BFF;
            padding-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #666;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
        }
        .line {
            border-top: 1px dashed #007BFF;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo">
        
    </div>
    <h1>Movie Booking Receipt</h1>
    <h3>Thank You for Your Purchase!</h3>
    <div class="section">
        <p><strong>Customer Information</strong></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($receipt_data['full_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($receipt_data['email']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($receipt_data['address']) ?>, <?= htmlspecialchars($receipt_data['city']) ?>, <?= htmlspecialchars($receipt_data['state']) ?>, <?= htmlspecialchars($receipt_data['zip_code']) ?></p>
    </div>
    <div class="section">
        <p><strong>Payment Details</strong></p>
        <p><strong>Name on Card:</strong> <?= htmlspecialchars($receipt_data['card_name']) ?></p>
        <p><strong>Movie Title:</strong> <?= htmlspecialchars($receipt_data['movie_title']) ?></p>
        <p><strong>Selected Seats:</strong> <?= htmlspecialchars($receipt_data['selected_seats']) ?></p>
        <p><strong>Showtime:</strong> <?= htmlspecialchars($receipt_data['showtime']) ?></p>
    </div>
    <div class="line"></div>
    <div class="section total">
        <p><strong>Total Price:</strong> Rs <?= htmlspecialchars($receipt_data['total_price']) ?></p>
    </div>
    <div class="footer">
        <p>Thank you for choosing us!</p>
        <p>For any inquiries, please contact our support team.</p>
    </div>
</div>

</body>
</html>
