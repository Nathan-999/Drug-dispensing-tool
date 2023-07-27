<!DOCTYPE html>
<html>
<head>
  <title>Admin Information</title>
  <style>
   body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
    }

    .form-container {
      width: 400px;
      margin: 0 auto;
      margin-top: 100px;
      padding: 30px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }

    .form-container h2 {
      margin-top: 0;
      padding: 20px;
      background-color: #337ab7;
      color: #fff;
      text-align: center;
      border-top-left-radius: 5px;
      border-top-right-radius: 5px;
    }

    .form-container form {
      margin-top: 30px;
    }

    .form-container label {
      display: block;
      margin-bottom: 10px;
      color: #333;
      font-weight: bold;
    }

    .form-container input[type="text"],
    .form-container input[type="password"],
    .form-container input[type="number"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      font-size: 14px;
    }

    .form-container input[type="submit"] {
      width: 100%;
      padding: 10px;
      background-color: #337ab7;
      color: #fff;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    .form-container input[type="submit"]:hover {
      background-color: #286090;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Admin Information</h2>
    <?php
    session_start(); // Start session

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

    // Check if the user is logged in as an administrator
    if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'administrator') {
      header('Location: login.php');
      exit();
    }

    // Get the admin's information from the database
    $adminInfo = array();
    if (isset($_SESSION['ssn'])) {
      $adminSSN = $_SESSION['ssn'];
      $sql = "SELECT * FROM administrator WHERE SSN = '$adminSSN'";
      $result = mysqli_query($connection, $sql);

      if (mysqli_num_rows($result) === 1) {
        $adminInfo = mysqli_fetch_assoc($result);
      }
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Update the admin's information
      $fname = $_POST['fname'];
      $lname = $_POST['lname'];
      $phone = $_POST['phone'];
      $email = $_POST['email'];
      $password = $_POST['password'];

      // SQL update query
      $updateSql = "UPDATE administrator SET fname = '$fname', lname = '$lname', phone = '$phone', email = '$email'";

      // Check if the password is provided and update it
      if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateSql .= ", password = '$hashedPassword'";
      }

      $updateSql .= " WHERE SSN = '$adminSSN'";

      mysqli_query($connection, $updateSql);

      // Get the updated admin's information
      $sql = "SELECT * FROM administrator WHERE SSN = '$adminSSN'";
      $result = mysqli_query($connection, $sql);

      if (mysqli_num_rows($result) === 1) {
        $adminInfo = mysqli_fetch_assoc($result);
      }
    }

    // Close the database connection
    mysqli_close($connection);
    ?>
    <form method="post" action="">
      <label for="SSN">SSN:</label>
      <input type="text" name="SSN" id="SSN" value="<?php echo isset($adminInfo['SSN']) ? $adminInfo['SSN'] : ''; ?>" disabled><br><br>
      <label for="fname">First Name:</label>
      <input type="text" name="fname" id="fname" value="<?php echo isset($adminInfo['fname']) ? $adminInfo['fname'] : ''; ?>"><br><br>
      <label for="lname">Last Name:</label>
      <input type="text" name="lname" id="lname" value="<?php echo isset($adminInfo['lname']) ? $adminInfo['lname'] : ''; ?>"><br><br>
      <label for="phone">Phone:</label>
      <input type="text" name="phone" id="phone" value="<?php echo isset($adminInfo['phone']) ? $adminInfo['phone'] : ''; ?>"><br><br>
      <label for="email">Email:</label>
      <input type="text" name="email" id="email" value="<?php echo isset($adminInfo['email']) ? $adminInfo['email'] : ''; ?>"><br><br>
      <label for="password">Password:</label>
      <input type="password" name="password" id="password"><br><br>
      <input type="submit" name="submit" value="Update Information">
    </form>
    <br>
    <a href="administrator_page.php">Back to Admin Page</a>
  </div>
</body>
</html>
