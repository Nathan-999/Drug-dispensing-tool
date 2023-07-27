<?php
session_start();

// Check if the user is logged in as a doctor
if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'doctor') {
  header('Location: login.php');
  exit();
}

// Database connection details
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

// Get the doctor's ID from the session
$doctorId = $_SESSION['doctor_id'];

// Retrieve the appointments for the doctor
$sql = "SELECT appointment.*, patient.fname, patient.lname FROM appointment 
        JOIN patient ON appointment.patient_id = patient.patient_id 
        WHERE appointment.doctor_id = '$doctorId'";
$result = mysqli_query($connection, $sql);

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Doctor Appointments</title>
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

    .appointments-table {
      margin-top: 30px;
      background-color: #fff;
      padding: 10px;
      border-radius: 3px;
    }

    .appointments-table h2 {
      margin: 0;
      padding-bottom: 10px;
      border-bottom: 1px solid #ccc;
      color: #333;
    }

    .appointments-table table {
      width: 100%;
      border-collapse: collapse;
    }

    .appointments-table table th,
    .appointments-table table td {
      padding: 8px;
      text-align: left;
    }

    .appointments-table table th {
      background-color: #f0f0f0;
    }

    .appointments-table table tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .back-button {
      display: block;
      margin-top: 20px;
      padding: 10px;
      background-color: #337ab7;
      color: #fff;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
    }

    .back-button:hover {
      background-color: #286090;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Doctor Appointments</h1>
    <div class="appointments-table">
      <h2>Your Appointments</h2>
      <?php if (mysqli_num_rows($result) === 0) { ?>
        <p>No appointments scheduled.</p>
      <?php } else { ?>
        <table>
          <tr>
            <th>Patient ID</th>
            <th>Patient Name</th>
            <th>Date</th>
            <th>Time</th>
          </tr>
          <?php while ($appointment = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?php echo $appointment['patient_id']; ?></td>
              <td><?php echo $appointment['fname'] . ' ' . $appointment['lname']; ?></td>
              <td><?php echo $appointment['appointment_date']; ?></td>
              <td><?php echo $appointment['appointment_time']; ?></td>
            </tr>
          <?php } ?>
        </table>
      <?php } ?>
      <a href="doctor_page.php" class="back-button">Back</a>
    </div>
  </div>
</body>
</html>
