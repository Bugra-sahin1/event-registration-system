<?php
session_start();
include_once("db.php");

if(!isset($_SESSION["admin"])){
    die("Denied Admin login required");
}
 $id = $_GET["id"] ?? 0;
if($id == 0) {
    die("No event is selected");
}

$eventStmt = mysqli_prepare($conn, "SELECT * FROM Events WHERE id = ?");
mysqli_stmt_bind_param($eventStmt, "i", $id);
mysqli_stmt_execute($eventStmt);
$eventResult = mysqli_stmt_get_result($eventStmt);
$event = mysqli_fetch_assoc($eventResult);

if(!$event){
    die("Event not found");
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $title= $_POST["title"];
    $event_date= $_POST["event_date"];
    $capacity= $_POST["capacity"];
    $location = $_POST["location"];
    
    if($capacity < 1) {
        die("Update rejected capacity must be at least 1");
    }

    $updateStmt = mysqli_prepare($conn, "UPDATE Events SET title = ?, event_date = ?, capacity = ?, location = ? WHERE id = ?");
    mysqli_stmt_bind_param($updateStmt, "ssisi", $title, $event_date, $capacity, $location, $id);
    $result = mysqli_stmt_execute($updateStmt);
    if ($result) {
        $admin_id = $_SESSION["admin"];
        $action_type = "UPDATE";
        $entity_name = "Event";
        $details = "Admin UPDATED Event id: $id";

        $logStmt = mysqli_prepare($conn, "INSERT INTO audit_log (admin_id, action_type, entity_name, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($logStmt, "issis", $admin_id, $action_type, $entity_name, $id, $details);
        mysqli_stmt_execute($logStmt);

        echo "<h2> Event Update Succesfully </h2>";
        echo "<a href = 'dashboard.php'> Back to Dashboard</a>";
        exit;
    }else{
        echo "Error:". mysqli_error($conn);
    }
}
?>

<link rel="stylesheet" href="style.css">
<h2>Edit Event</h2>

<form method="POST">
    Title: <input type="text" name="title" value="<?php echo $event['title']; ?>" required> <br><br>
    Date: <input type="date" name="event_date" value="<?php echo $event['event_date']; ?>" required> <br><br>
    Capacity: <input type="number" name="capacity" value="<?php echo $event['capacity']; ?>" required> <br><br>
    Location: <input type="text" name="location" value="<?php echo $event['location']; ?>" required> <br><br>
    <button type="submit"> Update Event</button>
</form>
<br>
<a href="dashboard.php">Back to dashboard</a> &nbsp; | &nbsp;
<a href="index.php">Back to Home page</a>