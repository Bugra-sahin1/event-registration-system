<?php 
session_start();
include_once("db.php");

if (!isset($_SESSION["admin"])) {
    die("Access denied Admin login required");
}

$id = $_GET["id"] ?? 0;
if($id == 0) {
    die("No registration selected");
}

$stmt = mysqli_prepare($conn, "SELECT * FROM registrations WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$registration = mysqli_fetch_assoc($result);

if(!$registration){
    die("Registration is not found");
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];

    if(empty($full_name) || empty($email)){
        die("Update rejected full name and email is required");
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Update rejected email format is false");
    }

    $updateStmt = mysqli_prepare($conn, "UPDATE registrations SET full_name = ?, email = ? WHERE id = ?");
    mysqli_stmt_bind_param($updateStmt, "ssi", $full_name, $email, $id);
    $updateResult = mysqli_stmt_execute($updateStmt);

    if($updateResult){
        $admin_id = $_SESSION["admin"];
        $action_type = "UPDATE";
        $entity_name = "Registration";  
        $details = "Admin UPDATED Registration: $id";

        $logStmt = mysqli_prepare($conn, "INSERT INTO audit_log (admin_id, action_type, entity_name, entity_id, details) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($logStmt, "issis", $admin_id, $action_type, $entity_name, $id, $details);
        mysqli_stmt_execute($logStmt);
        
        echo "<h2>Registration update is successful </h2>";
        echo "<a href = 'dashboard.php'>Back to dashboard</a>";
        exit;
        
    }else{
        echo "Error". mysqli_error($conn);
    }
}
?>

<link rel="stylesheet" href="style.css">
<h2>Edit Registration</h2>

<form method="post">
    Full Name: <input type="text" name="full_name" value="<?php echo $registration['full_name']; ?>" required> <br><br>
    Email: <input type="email" name="email" value="<?php echo $registration['email']; ?>" required> <br><br>
    <button type="submit">Update Registration</button>
</form>
<br>
<a href="dashboard.php">Back to dashboard</a> &nbsp; | &nbsp;
<a href="index.php">Back to home page</a>