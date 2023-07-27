<?php
session_start();

// Check if the user is logged in as a pharmacist
if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'pharmacist') {
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

// Get the pharmacist's information from the database
if (isset($_SESSION['ssn'])) {
  $ssn = $_SESSION['ssn'];
  $sql = "SELECT * FROM pharmacist WHERE ssn = '$ssn'";
  $result = mysqli_query($connection, $sql);

  if (mysqli_num_rows($result) === 1) {
    $pharmacistInfo = mysqli_fetch_assoc($result);
  } else {
    // The pharmacist was not found in the database
    header('Location: pharmacist_page.php');
    exit();
  }
} else {
  // Session variable 'ssn' is not set
  header('Location: pharmacist_page.php');
  exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Update the pharmacist's information
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $password = $_POST['password'];

  // Prepare and execute the update query
  $stmt = mysqli_prepare($connection, "UPDATE pharmacist SET fname = ?, lname = ?, password = ? WHERE ssn = ?");
  mysqli_stmt_bind_param($stmt, "ssss", $fname, $lname, $password, $ssn);
  mysqli_stmt_execute($stmt);

  // Redirect to the updated pharmacist page
  header('Location: pharmacist_page.php');
  exit();
}

// Handle logout
if (isset($_POST['logout'])) {
  // Destroy the session and redirect to the login page
  session_unset();
  session_destroy();
  header('Location: login.php');
  exit();
}
?>

<h1>Edit Pharmacist Information</h1>
<form method="post" action="">
  <label for="ssn">SSN:</label>
  <input type="text" name="ssn" id="ssn" value="<?php echo isset($pharmacistInfo['ssn']) ? $pharmacistInfo['ssn'] : ''; ?>" disabled><br><br>

  <label for="fname">First Name:</label>
  <input type="text" name="fname" id="fname" value="<?php echo isset($pharmacistInfo['fname']) ? $pharmacistInfo['fname'] : ''; ?>"><br><br>

  <label for="lname">Last Name:</label>
  <input type="text" name="lname" id="lname" value="<?php echo isset($pharmacistInfo['lname']) ? $pharmacistInfo['lname'] : ''; ?>"><br><br>

  <label for="password">Password:</label>
  <input type="password" name="password" id="password"><br><br>

  <input type="submit" value="Update Information">
</form>

<form method="post" action="">
  <button type="submit" name="logout">Logout</button>
</form>
