<?php

include_once("db.php");

$id = $_GET["id"] ?? 0;

if ($id == 0) {
    die("No event selected.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $event_id = $_POST["event_id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    if (empty($name) || empty($email)) {
        die("Registration rejected  name and email are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<h2>Registration rejected  email format is invalid.</h2>
        <a href='show_events.php'>Back to events</a> &nbsp; | &nbsp;
        <a href='index.php'>Back to home page</a>");
     
  
    }


    $capacityStmt = mysqli_prepare($conn, "SELECT capacity FROM Events WHERE id = ?");
    mysqli_stmt_bind_param($capacityStmt, "i", $event_id);
    mysqli_stmt_execute($capacityStmt);
    $capacityResult = mysqli_stmt_get_result($capacityStmt);
    $capacityRow = mysqli_fetch_assoc($capacityResult);


    $countStmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM registrations WHERE event_id = ?");
    mysqli_stmt_bind_param($countStmt, "i", $event_id);
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $countRow = mysqli_fetch_assoc($countResult);

    if($countRow["total"] >= $capacityRow["capacity"]){
        die("<h2> Registration Rejected event capacity reached </h2> <br>
        <a href = 'show_events.php'> Back</a>");
    }

    $insertStmt = mysqli_prepare($conn, "INSERT INTO registrations (event_id, full_name, email) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($insertStmt, "iss", $event_id, $name, $email);
    $result = mysqli_stmt_execute($insertStmt);



    if ($result) {
        echo "<h2>Registration Successful</h2>";
        echo "<a href='show_events.php'>Back to Events</a> &nbsp; | &nbsp;";
        echo "<a href = 'index.php'>Back to home page</a>";
    } else {
        echo "<h2>ERROR</h2>";
        echo mysqli_error($conn);
    }

    exit;
}
?>

<link rel="stylesheet" href="style.css">
<h2>Register</h2>

<form method="post">
    <input type="hidden" name="event_id" value="<?php echo $id; ?>">

    Name: <input name="name" required><br><br>
    Email: <input name="email" required><br><br>

    <button type="submit">Register</button>
</form>