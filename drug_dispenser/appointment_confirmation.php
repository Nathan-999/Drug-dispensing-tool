<?php
session_start();

// Check if the necessary session variables are set
if (!isset($_SESSION['appointment_confirmation'])) {
  header('Location: patient_appointments.php');
  exit();
}

// Retrieve the appointment confirmation message from the session
$confirmationMessage = $_SESSION['appointment_confirmation'];

// Clear the appointment confirmation session variable
unset($_SESSION['appointment_confirmation']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Appointment Confirmation</title>
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
      text-align: center;
    }

    h1 {
      color: #333;
    }

    p {
      margin-top: 30px;
      font-size: 18px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Appointment Confirmation</h1>
    <p><?php echo $confirmationMessage; ?></p>
  </div>
</body>
</html>
