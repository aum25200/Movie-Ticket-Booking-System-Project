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
}else{
  die("Connected");
}
?>
