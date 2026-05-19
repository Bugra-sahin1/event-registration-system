<?php
session_start();
include_once("db.php");

if (!isset($_SESSION["admin"])) {
    die("Access denied. Why: admin login required.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST["title"];
    $date = $_POST["date"];
    $capacity = $_POST["capacity"];
    $location = $_POST["location"];

    if ($capacity <= 0) {
        echo "Rejected. Why: capacity must be greater than 0.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO Events (title, event_date, capacity, location) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssis", $title, $date, $capacity, $location);


        if (mysqli_stmt_execute($stmt)) {
        $event_id = mysqli_insert_id($conn);

        $admin_id = $_SESSION["admin"];
        $action_type = "create_event";
        $entity_name = "Event";
        $details = "Admin created event with (id: $event_id)";

        $log_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (admin_id, action_type, entity_name, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($log_stmt, "issis", $admin_id, $action_type, $entity_name, $event_id, $details);
        mysqli_stmt_execute($log_stmt);

            echo "Event added successfully.";
        } else {
            echo "Event add failed: " . mysqli_error($conn);
        }
    }
}
?>

<link rel="stylesheet" href="style.css">
<h2>Create Event</h2>

<form method="post">
    Title: <input name="title" required><br><br>
    Date: <input type="date" name="date" required><br><br>
    Capacity: <input type="number" name="capacity" required><br><br>
    Location: <input type="text" name="location" required> <br><br>
    <button type="submit">Add Event</button>
</form>

<br>
<a href="dashboard.php">Back to Dashboard</a>