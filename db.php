<?php
$conn = mysqli_connect("localhost", "root", "", "it306_project");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>