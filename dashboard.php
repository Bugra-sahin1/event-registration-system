<?php

session_start();
include_once("db.php");


if (!isset($_SESSION["admin"])) {
    die("<h2>Access denied. Why: admin login required.</h2>
    <a href='admin.php'>Go to login page</a>
     &nbsp; | &nbsp; <a href='index.php'>Back to home page</a>
      &nbsp; | &nbsp;<a href='show_events.php'>Back to events</a>");
      exit;
}


function addLog($conn, $action_type, $entity_name, $entity_id, $details){
    $admin_id = $_SESSION["admin"];

    $stmt = mysqli_prepare($conn, "INSERT INTO audit_log (admin_id, action_type, entity_name, entity_id, details) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issis", $admin_id, $action_type, $entity_name, $entity_id, $details);
    return mysqli_stmt_execute($stmt);
}

if(isset($_GET["delete_event"])){
    $event_id = $_GET["delete_event"];

    $deleteRegsStmt = mysqli_prepare($conn, "DELETE FROM registrations WHERE event_id = ?");
    mysqli_stmt_bind_param($deleteRegsStmt, "i", $event_id);
    mysqli_stmt_execute($deleteRegsStmt);

    $deleteEventStmt = mysqli_prepare($conn, "DELETE FROM events WHERE id = ?");
    mysqli_stmt_bind_param($deleteEventStmt, "i", $event_id);
    $deleteResult = mysqli_stmt_execute($deleteEventStmt);

    if($deleteResult){
        addLog($conn, "DELETE", "Event", $event_id, "Admin DELETED Event with id: $event_id");
        header("Location: dashboard.php");
        exit;
    }else {
        echo "<p style = 'color: red;' There is error: ". mysqli_error($conn). "</p>";
    }
}



if(isset($_GET["delete_registration"])){
    $registration_id = $_GET["delete_registration"];

    $deleteStmt = mysqli_prepare($conn, "DELETE FROM registrations WHERE id = ?");
    mysqli_stmt_bind_param($deleteStmt, "i", $registration_id);
    $deleteResult = mysqli_stmt_execute($deleteStmt);

    if($deleteResult) {
        addLog($conn, "DELETE", "Registration", $registration_id, "Admin DELETED Registration with id: $registration_id");
        header("Location: dashboard.php");
        exit;
    }else {
        echo "<p style= 'color: red;'> There is a error;". mysqli_error($conn). "</p>";
    }
}
?>

<link rel="stylesheet" href="style.css">

<script>
window.addEventListener("pageshow", function(event){
    if(event.persisted){
        window.location.reload();
    }
})
</script>

<h2>Admin Dashboard</h2>

<a href="create_events.php">Create Event</a><br>
<a href="show_events.php">Show Events</a><br>
<a href="logout.php">Logout</a>
<hr>

<h3>Events</h3>
<?php 
$eventSql = "SELECT * FROM Events";
$eventResult = mysqli_query($conn, $eventSql);

?>

<table border="1" cellpadding="6">
    <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Capacity</th>
        <th>Location</th>
        <th>Action</th>
    </tr>
    <?php while ($event = mysqli_fetch_assoc($eventResult)) { ?>
    <tr>
        <td><?php echo $event["title"]; ?></td>
        <td><?php echo $event["event_date"]; ?></td>
        <td><?php echo $event["capacity"]; ?></td>
        <td><?php echo $event["location"]; ?></td>
        <td> <a href="edit_event.php?id=<?php echo $event['id']; ?>"> Edit Event</a>
            | <a href="dashboard.php?delete_event=<?php echo $event['id']; ?>"
         onclick="return confirm('Are you sure for deleting.');">
        Delete Event</a></td>
    </tr>
    <?php } ?>
</table>



<h3>Registrations</h3>

<?php 
 $sql = " SELECT registrations.id, registrations.full_name, registrations.email, Events.title FROM registrations JOIN Events ON registrations.event_id = Events.id";
 $result = mysqli_query($conn , $sql);
 ?>

 <table border="1" cellpadding="6">
    <tr>
        <th>Event</th>
        <th>Full name</th>
        <th>Email</th>
        <th>Action</th>
    </tr>
<?php 
while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td> <?php echo $row["title"]; ?></td>
    <td><?php echo $row["full_name"]; ?></td>
    <td><?php echo $row["email"]; ?></td>
    <td>
        <a href = "edit_registration.php?id=<?php echo $row['id']; ?>">Edit user</a> |
        <a href="dashboard.php?delete_registration=<?php echo $row['id']; ?>"
        onclick="return confirm('Are you sure for deleting ? ');"> Remove User </a>
    </td>
</tr>

<?php } ?>
 </table>  

 <hr>

 <h3>Audit Log</h3>
 <?php
 $logSql = "SELECT * FROM audit_log ORDER BY action_time DESC";
 $logResult = mysqli_query($conn, $logSql);
 ?>

 <table>
    <tr>
        <th>Admin ID</th>
        <th>Action Type</th>
        <th>Entity</th>
        <th>Entity ID</th>
        <th>Details</th>
        <th>Time</th>
    </tr>
    <?php while($log = mysqli_fetch_assoc($logResult)) { ?>
    <tr>
        <td><?php echo $log["admin_id"]; ?></td>
        <td><?php echo $log["action_type"]; ?></td>
        <td><?php echo $log["entity_name"]; ?></td>
        <td><?php echo $log["entity_id"]; ?></td>
        <td><?php echo $log["details"]; ?></td>
        <td><?php echo $log["action_time"]; ?></td>
    </tr>
    <?php } ?>

 </table>