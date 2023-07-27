<?php
session_start();

// Check if the user is logged in as a patient
if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'patient') {
  header('Location: login.php');
  exit();
}

// Replace the database connection details with your own
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'duka';

// Create a MySQLi connection
$connection = mysqli_connect($hostname, $username, $password, $database);

// Check if the connection was successful
if (!$connection) {
  die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Get the patient's information from the form
$ssn = $_POST['ssn'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$gender = $_POST['gender'];
$password = $_POST['password'];

// Prepare the update query
$sql = "UPDATE patient SET fname = ?, lname = ?, phone = ?, email = ?, gender = ?, password = ? WHERE ssn = ?";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "sssssss", $fname, $lname, $phone, $email, $gender, $password, $ssn);

// Execute the update query
if (mysqli_stmt_execute($stmt)) {
  // Update successful
  header('Location: patient_page.php');
  exit();
} else {
  // Update failed
  die("Failed to update patient information: " . mysqli_error($connection));
}
?>
