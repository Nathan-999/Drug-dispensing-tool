<?php
session_start();

// Check if the user is logged in as a doctor
if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'doctor') {
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
$specialty = $_POST['specialty'];
$yearsOfExperience = $_POST['years_of_experience'];
$password = $_POST['password'];

// Update the doctor's information in the database
$sql = "UPDATE doctor SET fname = '$fname', lname = '$lname', specialty = '$specialty', years_of_experience = '$yearsOfExperience', password = '$password' WHERE ssn = '$ssn'";
$result = mysqli_query($connection, $sql);

if ($result) {
  // Redirect to the doctor page
  header('Location: doctor_page.php');
  exit();
} else {
  // Handle the update error
  echo "Failed to update doctor information: " . mysqli_error($connection);
}
?>
