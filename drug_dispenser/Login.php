<!DOCTYPE html>
<html>
<head>
  <title>Main Page</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
      margin: 0;
      padding: 0;
    }

    .header {
      background-color: #337ab7;
      padding: 10px;
      text-align: center;
    }

    .header a {
      display: inline-block;
      margin: 0 10px;
      color: #fff;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .header a:hover {
      background-color: #286090;
    }

    .login-container {
      width: 400px;
      margin: 0 auto;
      margin-top: 100px;
      padding: 30px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }

    .login-container h2 {
      margin-top: 0;
      padding: 20px;
      background-color: #337ab7;
      color: #fff;
      text-align: center;
      border-top-left-radius: 5px;
      border-top-right-radius: 5px;
    }

    .login-container form {
      margin-top: 30px;
    }

    .login-container label {
      display: block;
      margin-bottom: 10px;
      color: #333;
      font-weight: bold;
    }

    .login-container input[type="text"],
    .login-container input[type="password"],
    .login-container select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      font-size: 14px;
    }

    .login-container input[type="submit"] {
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

    .login-container input[type="submit"]:hover {
      background-color: #286090;
    }

    .login-container p.error {
      color: red;
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="header">
    <a href="index.php">Home</a>
    <a href="about_us.php">About Us</a>
    <a href="testimonies.php">Testimonies</a>
    <a href="services.php">Services</a>
  </div>
  <div class="login-container">
    <h2>Login</h2>
    <?php
    session_start();

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

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $ssn = $_POST['ssn'];
      $userType = $_POST['user_type'];
      $password = $_POST['password'];

      // Check if all fields are filled
      if (empty($ssn) || empty($userType)) {
        $error = "Please fill in all fields.";
      } else {
        // Validate user credentials based on user type
        $table = '';
        $idColumnName = '';

        switch ($userType) {
          case 'doctor':
            $table = 'doctor';
            $idColumnName = 'doctor_id';
            break;
          case 'patient':
            $table = 'patient';
            $idColumnName = 'patient_id';
            break;
          case 'administrator':
            $table = 'administrator';
            $idColumnName = 'administrator_id';
            break;
          case 'pharmacist':
            $table = 'pharmacist';
            $idColumnName = 'pharmacist_id';
            break;
          default:
            $error = "Invalid user type.";
            break;
        }

        if (!empty($table) && !empty($idColumnName)) {
          // Query the database to check if the user exists
          $sql = "SELECT * FROM $table WHERE ssn = '$ssn'";
          $result = mysqli_query($connection, $sql);

          if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // Check if the user has a password set
            if (empty($row['password'])) {
              // Update the user's password
              $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
              $updateSql = "UPDATE $table SET password = '$hashedPassword' WHERE ssn = '$ssn'";
              mysqli_query($connection, $updateSql);

              $_SESSION['name'] = $row['fname'] . ' ' . $row['lname'];
              $_SESSION['user_type'] = $userType;
              $_SESSION['ssn'] = $ssn;
              $_SESSION[$idColumnName] = $row[$idColumnName];

              header("Location: $userType" . "_page.php");
              exit();
            } else {
              // Verify the entered password
              if (password_verify($password, $row['password'])) {
                $_SESSION['name'] = $row['fname'] . ' ' . $row['lname'];
                $_SESSION['user_type'] = $userType;
                $_SESSION['ssn'] = $ssn;
                $_SESSION[$idColumnName] = $row[$idColumnName];

                header("Location: $userType" . "_page.php");
                exit();
              } else {
                $error = "Invalid password.";
              }
            }
          } else {
            $error = "Invalid SSN.";
          }
        }
      }
    }
    ?>
    <form method="post" action="">
      <label for="ssn">SSN:</label>
      <input type="text" name="ssn" id="ssn" required><br><br>
      <label for="user_type">User Type:</label>
      <select name="user_type" id="user_type" required>
        <option value="">Select user type</option>
        <option value="doctor">Doctor</option>
        <option value="patient">Patient</option>
        <option value="administrator">Administrator</option>
        <option value="pharmacist">Pharmacist</option>
      </select><br><br>
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required><br><br>
      <input type="submit" value="Login">
    </form>
  </div>
</body>
</html>
