<?php
session_start();
include_once("db.php");

$available = $_GET["available"] ?? "";
$search = $_GET["search"] ?? "";

$sql = "SELECT Events.*, COUNT(registrations.id) AS registered_count FROM Events LEFT JOIN registrations ON Events.id = registrations.event_id WHERE Events.title LIKE ? GROUP BY Events.id";
if($available == "yes"){
    $sql.= " HAVING registered_count < Events.capacity";
}

$searchParam = "%" . $search . "%";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $searchParam);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<link rel="stylesheet" href="style.css">

<h2>Events</h2>
<form method="get">
    Search by title:
    <input type="text" name="search" value="<?php echo $search; ?>"> <br>

    Only available events: <input type="checkbox" name="available" value="yes" <?php if($available == "yes") echo "checked"; ?>>
    <button type="submit">filter</button>
    <button type="button" onclick="window.location.href='show_events.php'">Reset</button>
</form>

<?php 
if ($search == "" && $available == "") {
    echo "<p>No search or filter is active showing all events</p>";
}

if ($search != "") {
    echo "<p>Search active showing events where title contains <b>$search</b>.</p>";
}

if ($available == "yes") {
    echo "<p>Filter active showing only events with remaining seats</p>";
}
?>
<?php while ($row = mysqli_fetch_assoc($result)) { 
    $remaining = $row["capacity"] - $row["registered_count"];
?>
    <div class="event-card">
        <h3><?php echo $row["title"]; ?></h3>
        <p>Date: <?php echo $row["event_date"]; ?></p>
        <p>Registered: <?php echo $row["registered_count"]; ?>/ <?php echo $row["capacity"]; ?> </p> 
        <p>Location: <?php echo $row["location"]; ?></p>
        <p> Remaining capacity: <?php echo $remaining; ?></p>
        <?php if ($remaining > 0) { ?>
        <a href="register.php?id=<?php echo $row["id"]; ?>">Register</a>
        <?php } else { ?>
        <p style = "color:red;"> Event is full</p>
        <?php } ?>
    </div>
    <hr>
<?php } ?>

<br>
<a href="admin.php">Admin Login</a> &nbsp; | &nbsp;
<a href = "index.php">Back to home page</a> &nbsp; | &nbsp;
<a href = "dashboard.php">Back to dashboard page (Admin login required)</a>