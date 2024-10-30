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

// Handle form submissions for adding or updating movies
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_movie'])) {
        // Adding a new movie
        $title = $_POST['title'];
        $genre = $_POST['genre'];
        $duration = $_POST['duration'];
        $rating = $_POST['rating'];

        $stmt = $mysqli_connect->prepare("INSERT INTO movies (title, genre, duration, rating) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $title, $genre, $duration, $rating);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_movie'])) {
        // Updating an existing movie
        $id = $_POST['movie_id'];
        $title = $_POST['title'];
        $genre = $_POST['genre'];
        $duration = $_POST['duration'];
        $rating = $_POST['rating'];

        $stmt = $mysqli_connect->prepare("UPDATE movies SET title = ?, genre = ?, duration = ?, rating = ? WHERE id = ?");
        $stmt->bind_param("ssdsi", $title, $genre, $duration, $rating, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle movie deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $mysqli_connect->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch movies from the database
$movies = [];
$stmt = $mysqli_connect->prepare("SELECT * FROM movies");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}
$stmt->close();

// Close the connection after all operations
$mysqli_connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Movies</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h2>Manage Movies</h2>

<div class="form-container">
    <h3>Add New Movie</h3>
    <form method="POST" action="">
        <input type="text" name="title" placeholder="Movie Title" required>
        <input type="text" name="genre" placeholder="Genre" required>
        <input type="number" name="duration" placeholder="Duration (mins)" required>
        <input type="number" name="rating" placeholder="Rating (out of 10)" step="0.1" required>
        <button type="submit" name="add_movie">Add Movie</button>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Genre</th>
        <th>Duration (in mins)</th>
        <th>Rating</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($movies as $movie): ?>
    <tr>
        <td><?= htmlspecialchars($movie['id']) ?></td>
        <td><?= htmlspecialchars($movie['title']) ?></td>
        <td><?= htmlspecialchars($movie['genre']) ?></td>
        <td><?= htmlspecialchars($movie['duration']) ?></td>
        <td><?= htmlspecialchars($movie['rating']) ?></td>
        <td>
            <a href="?edit=<?= $movie['id'] ?>">Edit</a>
            <a href="?delete=<?= $movie['id'] ?>" onclick="return confirm('Are you sure you want to delete this movie?');">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Edit Movie Form -->
<?php if (isset($_GET['edit'])): ?>
    <?php
    // Re-establish connection for fetching movie details to edit
    $mysqli_connect = new mysqli($host, $username, $password, $database);
    if ($mysqli_connect->connect_error) {
        die("Connection failed: " . $mysqli_connect->connect_error);
    }

    $edit_id = $_GET['edit'];
    $stmt = $mysqli_connect->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $movie_to_edit = $result->fetch_assoc();
    $stmt->close();
    ?>
    <div class="form-container">
        <h3>Edit Movie</h3>
        <form method="POST" action="">
            <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie_to_edit['id']) ?>">
            <input type="text" name="title" value="<?= htmlspecialchars($movie_to_edit['title']) ?>" required>
            <input type="text" name="genre" value="<?= htmlspecialchars($movie_to_edit['genre']) ?>" required>
            <input type="number" name="duration" value="<?= htmlspecialchars($movie_to_edit['duration']) ?>" required>
            <input type="number" name="rating" value="<?= htmlspecialchars($movie_to_edit['rating']) ?>" step="0.1" required>
            <button type="submit" name="update_movie">Update Movie</button>
        </form>
    </div>
<?php endif; ?>

</body>
</html>
