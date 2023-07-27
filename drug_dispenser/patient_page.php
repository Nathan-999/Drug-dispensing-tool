<!DOCTYPE html>
<html>
<head>
  <title>Patient Dashboard</title>
  <style>
    /* CSS styles */

    body {
      margin: 0;
      padding: 0;
      font-family: "Helvetica Neue", Arial, sans-serif;
      background-color: #f4f4f4;
    }

    .container {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .dashboard {
      text-align: center;
      background-color: #fff;
      padding: 40px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      max-width: 600px;
      width: 100%;
      transition: box-shadow 0.3s ease;
    }

    .dashboard:hover {
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .dashboard h1 {
      margin-bottom: 30px;
      color: #333;
      font-size: 32px;
      font-weight: bold;
    }

    .dashboard ul {
      list-style-type: none;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      margin-top: 30px;
    }

    .dashboard ul li {
      margin: 10px;
    }

    .dashboard ul li a {
      text-decoration: none;
      color: #333;
      font-weight: bold;
      padding: 12px 20px;
      border-radius: 10px;
      background-color: #f0f0f0;
      transition: background-color 0.3s ease;
      display: inline-block;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .dashboard ul li a:hover {
      background-color: #ff6f00;
      color: #fff;
    }

    .dashboard form {
      margin-top: 30px;
    }

    .dashboard form input[type="submit"] {
      padding: 12px 20px;
      background-color: #ff6f00;
      color: #fff;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-size: 16px;
      font-weight: bold;
      letter-spacing: 1px;
    }

    .dashboard form input[type="submit"]:hover {
      background-color: #e65100;
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

  // Check if the user is logged in as a patient
  if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit();
  }
  ?>

  <div class="container">
    <div class="dashboard">
      <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>
      <ul>
        <li><a href="patient_appointments.php">Appointments</a></li>
        <li><a href="patient_prescriptions.php">Prescriptions</a></li>
        <li><a href="patient_info.php">Patient Information</a></li>
      </ul>

      <form method="post" action="">
        <input type="hidden" name="logout" value="true">
        <input type="submit" value="Logout">
      </form>
    </div>
  </div>
</body>
</html>