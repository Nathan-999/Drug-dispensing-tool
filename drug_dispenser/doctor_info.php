<!DOCTYPE html>
<html>
<head>
  <title>Doctor Information</title>
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
    <h2>Doctor Information</h2>
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

    // Check if the user is logged in as a doctor
    if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'doctor') {
      header('Location: login.php');
      exit();
    }

    // Get the doctor's information from the database
    $doctorInfo = array();
    if (isset($_SESSION['ssn'])) {
      $ssn = $_SESSION['ssn'];
      $sql = "SELECT * FROM doctor WHERE ssn = '$ssn'";
      $result = mysqli_query($connection, $sql);

      if (mysqli_num_rows($result) === 1) {
        $doctorInfo = mysqli_fetch_assoc($result);
        $prefilledSSN = $_SESSION['ssn'];
      }
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Update the doctor's information
      $ssn = $_SESSION['ssn'];
      $fname = $_POST['fname'];
      $lname = $_POST['lname'];
      $specialty = $_POST['specialty'];
      $years_of_experience = $_POST['years_of_experience'];
      $password = $_POST['password'];

      // SQL update query
      $updateSql = "UPDATE doctor SET fname = '$fname', lname = '$lname', specialty = '$specialty', years_of_experience = '$years_of_experience'";

      // Check if the password is provided and update it
      if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateSql .= ", password = '$hashedPassword'";
      }

      $updateSql .= " WHERE ssn = '$ssn'";

      mysqli_query($connection, $updateSql);

      // Get the updated doctor's information
      $sql = "SELECT * FROM doctor WHERE ssn = '$ssn'";
      $result = mysqli_query($connection, $sql);

      if (mysqli_num_rows($result) === 1) {
        $doctorInfo = mysqli_fetch_assoc($result);
      }
    }

    // Close the database connection
    mysqli_close($connection);
    ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <label for="ssn">SSN:</label>
      <input type="text" name="ssn" id="ssn" value="<?php echo isset($prefilledSSN) ? $prefilledSSN : ''; ?>" disabled><br><br>
      <label for="fname">First Name:</label>
      <input type="text" name="fname" id="fname" value="<?php echo isset($doctorInfo['fname']) ? $doctorInfo['fname'] : ''; ?>"><br><br>
      <label for="lname">Last Name:</label>
      <input type="text" name="lname" id="lname" value="<?php echo isset($doctorInfo['lname']) ? $doctorInfo['lname'] : ''; ?>"><br><br>
      <label for="specialty">Specialty:</label>
      <input type="text" name="specialty" id="specialty" value="<?php echo isset($doctorInfo['specialty']) ? $doctorInfo['specialty'] : ''; ?>"><br><br>
      <label for="years_of_experience">Years of Experience:</label>
      <input type="number" name="years_of_experience" id="years_of_experience" value="<?php echo isset($doctorInfo['years_of_experience']) ? $doctorInfo['years_of_experience'] : ''; ?>"><br><br>
      <label for="password">Password:</label>
      <input type="password" name="password" id="password"><br><br>
      <input type="submit" name="submit" value="Update Information">
    </form>
    <br>
    <a href="doctor_page.php">Back to Doctor Page</a>
  </div>
</body>
</html>
