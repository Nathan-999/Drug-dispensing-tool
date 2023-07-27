<?php
session_start();

// Check if the user is logged in as a patient
if (!isset($_SESSION['name']) || $_SESSION['user_type'] !== 'patient') {
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

// Get the patient ID from the session
$patientId = $_SESSION['patient_id'];

// Retrieve the prescriptions for the patient from the prescription table
$sql = "SELECT drug.drug_name AS drug, prescription.frequency, drug.dosage_form, prescription.amount
        FROM prescription
        JOIN drug ON prescription.drug_id = drug.drug_id
        WHERE prescription.patient_id = '$patientId'";
$result = mysqli_query($connection, $sql);

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Prescriptions</title>
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

    .prescriptions-table {
      margin-top: 30px;
      background-color: #fff;
      padding: 10px;
      border-radius: 3px;
    }

    .prescriptions-table h2 {
      margin: 0;
      padding-bottom: 10px;
      border-bottom: 1px solid #ccc;
      color: #333;
    }

    .prescriptions-table table {
      width: 100%;
      border-collapse: collapse;
    }

    .prescriptions-table table th,
    .prescriptions-table table td {
      padding: 8px;
      text-align: left;
    }

    .prescriptions-table table th {
      background-color: #f0f0f0;
    }

    .prescriptions-table table tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Prescriptions</h1>
    <div class="prescriptions-table">
      <h2>Your Prescriptions</h2>
      <?php if (mysqli_num_rows($result) === 0) { ?>
        <p>No prescriptions found.</p>
      <?php } else { ?>
        <table>
          <tr>
            <th>Drug</th>
            <th>Frequency</th>
            <th>Dosage Form</th>
            <th>Amount Prescribed</th>
          </tr>
          <?php while ($prescription = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?php echo $prescription['drug']; ?></td>
              <td><?php echo $prescription['frequency']; ?></td>
              <td><?php echo $prescription['dosage_form']; ?></td>
              <td><?php echo $prescription['amount']; ?></td>
            </tr>
          <?php } ?>
        </table>
      <?php } ?>
    </div>
  </div>
</body>
</html>
