<?php
session_start();
include_once("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = mysqli_prepare($conn, "SELECT * FROM admins WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);


    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);

    if ($admin && password_verify($password, $admin["password_hash"])) {
        $_SESSION["admin"] = $admin["id"];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Login failed : invalid email or password.";
    }
}
?>

<link rel="stylesheet" href="style.css">
<h2>Admin Login</h2>

<form method="post">
    Email: <input name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>

<br>
<a href="show_events.php">Back to Events</a> &nbsp; | &nbsp;
<a href ="index.php">Back to home page</a>