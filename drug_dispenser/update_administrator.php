<?php
session_start();

// Check if the user is logged in as an administrator
if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'administrator') {
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

// Get the form data
$ssn = $_POST['ssn'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$password = $_POST['password'];

// Update the administrator's information in the database
$sql = "UPDATE administrator SET fname = '$fname', lname = '$lname', password = '$password' WHERE ssn = '$ssn'";
$result = mysqli_query($connection, $sql);

if ($result) {
  // Redirect to the administrator page
  header('Location: administrator_page.php');
  exit();
} else {
  // Handle the update error
  echo "Failed to update administrator information: " . mysqli_error($connection);
}
?>
