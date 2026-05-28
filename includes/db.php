<?php
$conn = mysqli_connect("localhost", "root", "", "voting_system");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
