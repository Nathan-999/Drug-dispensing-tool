<!DOCTYPE html>
<html>
<head>
  <title>Pharmacist Information</title>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      padding: 0;
      background-color: #f2f2f2;
      font-family: Arial, sans-serif;
    }

    .form-container {
      max-width: 400px;
      padding: 20px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h1 {
      margin-top: 0;
      color: #333;
    }

    form {
      margin-top: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: #555;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    input[type="submit"] {
      width: 100%;
      padding: 10px;
      background-color: #4CAF50;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #45a049;
    }

    .logout-btn {
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <?php
  session_start();

  // Handle logout
  if (isset($_POST['logout'])) {
    // Destroy the session
    session_unset();
    session_destroy();

    // Redirect to the login page without further execution
    header('Location: login.php');
    exit();
  }

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

  // Handle form submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update the pharmacist's information
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $ssn = $_SESSION['ssn'];

    if (!empty($_POST['password'])) {
      // New password is provided, hash it
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

      // Prepare and execute the update query with password change
      $stmt = mysqli_prepare($connection, "UPDATE pharmacist SET fname = ?, lname = ?, password = ? WHERE ssn = ?");
      mysqli_stmt_bind_param($stmt, "ssss", $fname, $lname, $password, $ssn);
    } else {
      // No new password, update without password change
      $stmt = mysqli_prepare($connection, "UPDATE pharmacist SET fname = ?, lname = ? WHERE ssn = ?");
      mysqli_stmt_bind_param($stmt, "sss", $fname, $lname, $ssn);
    }

    mysqli_stmt_execute($stmt);

    // Redirect to the updated pharmacist page
    header('Location: pharmacist_info.php');
    exit();
  }

  // Get the pharmacist's information from the database
  if (isset($_SESSION['ssn'])) {
    $ssn = $_SESSION['ssn'];
    $sql = "SELECT * FROM pharmacist WHERE ssn = '$ssn'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) === 1) {
      $pharmacistInfo = mysqli_fetch_assoc($result);
      $prefilledSSN = $_SESSION['ssn'];
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
  ?>

  <div class="form-container">
    <h1>Pharmacist Information</h1>
    <form method="post" action="">
      <label for="ssn">SSN:</label>
      <input type="text" name="ssn" id="ssn" value="<?php echo isset($prefilledSSN) ? $prefilledSSN : ''; ?>" disabled><br><br>

      <label for="fname">First Name:</label>
      <input type="text" name="fname" id="fname" value="<?php echo isset($pharmacistInfo['fname']) ? $pharmacistInfo['fname'] : ''; ?>"><br><br>

      <label for="lname">Last Name:</label>
      <input type="text" name="lname" id="lname" value="<?php echo isset($pharmacistInfo['lname']) ? $pharmacistInfo['lname'] : ''; ?>"><br><br>

      <label for="password">Password:</label>
      <input type="password" name="password" id="password"><br><br>

      <input type="submit" value="Update Information">
    </form>

    <form method="post" action="">
      <input type="hidden" name="logout" value="true">
      <input type="submit" value="Logout" class="logout-btn">
    </form>
  </div>
</body>
</html>
