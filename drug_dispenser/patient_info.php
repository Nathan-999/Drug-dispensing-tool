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

// Get the patient's information from the database
$ssn = $_SESSION['ssn'];
$sql = "SELECT * FROM patient WHERE ssn = '$ssn'";
$result = mysqli_query($connection, $sql);
$patient = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Update the patient's information
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $gender = $_POST['gender'];
  $password = $_POST['password'];

  // Prepare and execute the update query
  $stmt = mysqli_prepare($connection, "UPDATE patient SET fname = ?, lname = ?, phone = ?, email = ?, gender = ? WHERE ssn = ?");
  mysqli_stmt_bind_param($stmt, "ssssss", $fname, $lname, $phone, $email, $gender, $ssn);
  mysqli_stmt_execute($stmt);

  // Check if the password is provided
  if (!empty($password)) {
    // Prepare and execute the update query for the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($connection, "UPDATE patient SET password = ? WHERE ssn = ?");
    mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $ssn);
    mysqli_stmt_execute($stmt);
  }

  // Redirect to the patient information page to reflect the changes
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Patient Information</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
    }

    h1 {
      text-align: center;
      color: #333;
    }

    form {
      max-width: 400px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      color: #666;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
      margin-bottom: 20px;
    }

    input[type="submit"] {
      background-color: #333;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #555;
    }

    .back-button {
      text-align: center;
      margin-top: 10px;
    }

    .back-button a {
      color: #333;
      text-decoration: none;
      background-color: #f0f0f0;
      padding: 8px 16px;
      border-radius: 4px;
    }

    .back-button a:hover {
      background-color: #ddd;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Patient Information</h1>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <label for="fname">First Name:</label>
      <input type="text" name="fname" id="fname" value="<?php echo $patient['fname']; ?>"><br>

      <label for="lname">Last Name:</label>
      <input type="text" name="lname" id="lname" value="<?php echo $patient['lname']; ?>"><br>

      <label for="phone">Phone:</label>
      <input type="text" name="phone" id="phone" value="<?php echo $patient['phone']; ?>"><br>

      <label for="email">Email:</label>
      <input type="email" name="email" id="email" value="<?php echo $patient['email']; ?>"><br>

      <label for="gender">Gender:</label>
      <input type="text" name="gender" id="gender" value="<?php echo $patient['gender']; ?>"><br>

      <label for="password">Password:</label>
      <input type="password" name="password" id="password"><br>

      <input type="submit" value="Update">
    </form>

    <div class="back-button">
      <a href="patient_page.php">Back</a>
    </div>
  </div>
</body>
</html>
