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

if (!$stmt) {
    die("Error preparing statement: " . $mysqli_connect->error);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $movies[] = $row['title'];
}
$stmt->close();

// Fetching movie title and pricing information from URL
$movie_title = $_GET['title'] ?? 'Unknown Movie';
$selected_seats = $_GET['seat'] ?? '';
$total_price = $_GET['total'] ?? 0;

// Optionally fetch showtime from URL
$showtime = $_GET['showtime'] ?? '';

$movie_details = [];
if ($movie_title != 'Unknown Movie') {
    $stmt = $mysqli_connect->prepare("SELECT * FROM movies WHERE title = ?");
    $stmt->bind_param("s", $movie_title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $movie_details = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600&display=swap');
        body {
            background-image: url('akshar-dave-WYCeSzr7SQ4-unsplash (1).jpg');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0; 
            padding: 0;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 700px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin: auto; 
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .col {
            flex: 1;
            min-width: 300px;
        }
        .inputBox {
            margin: 15px 0;
        }
        .inputBox span {
            margin-bottom: 10px;
            display: block;
            font-weight: 500;
        }
        .inputBox input, .inputBox select {
            width: 100%;
            border: 1px solid #ccc;
            padding: 10px 15px;
            font-size: 15px;
            border-radius: 5px;
        }
        button {
            padding: 15px 30px;
            font-size: 18px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .payment-icons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .payment-icons img {
            width: 60px; 
            margin: 0 10px;
        }
        h3 {
            text-align: center;
            margin: 20px 0;
        }
        p {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <form id="payment-form" method="POST" action="">
        <h3 class="title">Payment Information</h3>
        <div class="row">
            <div class="col">
                <h4>Address</h4>
                <div class="inputBox">
                    <span>Full Name :</span>
                    <input type="text" name="full_name" placeholder="Abc" required>
                </div>
                <div class="inputBox">
                    <span>Email :</span>
                    <input type="email" name="email" placeholder="example@example.com" required>
                </div>
                <div class="inputBox">
                    <span>Address :</span>
                    <input type="text" name="address" placeholder="room - street - locality" required>
                </div>
                <div class="inputBox">
                    <span>City :</span>
                    <input type="text" name="city" placeholder="Mumbai" required>
                </div>
                <div class="flex">
                    <div class="inputBox">
                        <span>State :</span>
                        <input type="text" name="state" placeholder="Maharashtra" required>
                    </div>
                    <div class="inputBox">
                        <span>Zip Code :</span>
                        <input type="text" name="zip_code" placeholder="12345" required>
                    </div>
                </div>
            </div>

            <div class="col">
                <h4>Payment</h4>
                <div class="inputBox">
                    <span>Name on Card :</span>
                    <input type="text" name="card_name" placeholder="Abc" required>
                </div>
                <div class="inputBox">
                    <span>Credit Card Number :</span>
                    <input type="text" name="card_number" placeholder="1111-2222-3333-4444" required>
                </div>
                <div class="inputBox">
                    <span>Exp Month :</span>
                    <input type="text" name="expiry_month" placeholder="MM" required>
                </div>
                <div class="flex">
                    <div class="inputBox">
                        <span>Exp Year :</span>
                        <input type="text" name="expiry_year" placeholder="YY" required>
                    </div>
                    <div class="inputBox">
                        <span>CVV :</span>
                        <input type="text" name="cvv" placeholder="123" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="inputBox">
            <span>Select Movie:</span>
            <select name="movie_title" required>
                <option value="">Select a movie</option>
                <?php foreach ($movies as $movie): ?>
                    <option value="<?= htmlspecialchars($movie) ?>" <?= $movie === $movie_title ? 'selected' : '' ?>><?= htmlspecialchars($movie) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <h4 style="text-align: center; margin: 20px 0;"> OR <br><br>Online Payments Available</h4>
        <div class="payment-icons" style="display: flex; justify-content: center;">
            <img src="png-transparent-google-pay-or-tez-hd-logo-thumbnail.png" alt="Google Pay">
            <img src="icons8-paytm-144.png" alt="Paytm">
            <img src="icons8-phone-pe-144.png" alt="PhonePe">
        </div><br>

        <input type="hidden" name="selected_seats" value="<?= htmlspecialchars($selected_seats) ?>">
        <input type="hidden" name="showtime" value="<?= htmlspecialchars($showtime) ?>">
        <input type="hidden" name="total_price" value="<?= htmlspecialchars($total_price) ?>">

        <p id="selected-seats"><b>Selected Seats: <?= htmlspecialchars($selected_seats) ?></b></p>
        <p id="showtime"><b>Showtime: <?= htmlspecialchars($showtime) ?></b></p>
        <p id="total-price"><b>Total Price: Rs <?= htmlspecialchars($total_price) ?></b></p><br>

        <?php if (!empty($movie_details)): ?>
            <p><strong>Genre:</strong> <?= htmlspecialchars($movie_details['genre']) ?></p>
            <p><strong>Duration:</strong> <?= htmlspecialchars($movie_details['duration']) ?> mins</p>
            <p><strong>Rating:</strong> <?= htmlspecialchars($movie_details['rating']) ?>/10</p>
        <?php endif; ?>

        <button type="submit">Pay Now</button>
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    if (empty($full_name) || empty($email) || empty($address) || empty($city) || empty($state) || empty($zip_code) || empty($card_name) || empty($card_number) || empty($expiry_month) || empty($expiry_year) || empty($cvv) || empty($movie_title)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if (!ctype_digit($zip_code) || strlen($zip_code) != 5) {
        die("Zip code must be a 5-digit number.");
    }

    $stmt = $mysqli_connect->prepare("INSERT INTO payments (full_name, email, address, city, state, zip_code, card_name, card_number, expiry, cvv, movie_title, selected_seats, total_price, showtime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $expiry = "$expiry_month/$expiry_year";
        $stmt->bind_param("ssssssssssssss", $full_name, $email, $address, $city, $state, $zip_code, $card_name, $card_number, $expiry, $cvv, $movie_title, $selected_seats, $total_price, $showtime);

        if ($stmt->execute()) {
            echo "<script>alert('Payment successful!'); window.location.href = 'thankyou.html';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $mysqli_connect->error;
    }
}
?>

</body>
</html>
