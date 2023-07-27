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

// Check if the user is logged in as a doctor
if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'doctor') {
  header('Location: login.php');
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Doctor Dashboard</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "Helvetica Neue", Arial, sans-serif;
      background-color: #f4f4f4;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 40px;
    }

    .dashboard {
      background-color: #fff;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .dashboard h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
      font-size: 32px;
      font-weight: bold;
    }

    .dashboard ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
      text-align: center;
    }

    .dashboard ul li {
      margin: 10px 0;
    }

    .dashboard ul li a {
      display: block;
      text-decoration: none;
      color: #333;
      font-weight: bold;
      padding: 12px 20px;
      border-radius: 5px;
      background-color: #f0f0f0;
      transition: background-color 0.3s ease;
    }

    .dashboard ul li a:hover {
      background-color: #286090;
      color: #fff;
    }

    .dashboard form {
      text-align: center;
      margin-top: 30px;
    }

    .dashboard form input[type="submit"] {
      padding: 12px 20px;
      background-color: #337ab7;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-size: 16px;
      font-weight: bold;
      letter-spacing: 1px;
    }

    .dashboard form input[type="submit"]:hover {
      background-color: #286090;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="dashboard">
      <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>
      <ul>
        <li><a href="doctor_info.php">Doctor Information</a></li>
        <li><a href="prescription_management.php">Prescription Management</a></li>
        <li><a href="patient_records.php">Patient Records</a></li>
        <li><a href="doctor_appointments.php">Appointments</a></li>
      </ul>

      <form method="post" action="">
        <input type="hidden" name="logout" value="true">
        <input type="submit" value="Logout">
      </form>
    </div>
  </div>
</body>
</html>
