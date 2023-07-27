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

// Get the patient's ID from the session
$patientId = $_SESSION['patient_id'];

// Get the list of doctors from the database
$sql = "SELECT * FROM doctor";
$result = mysqli_query($connection, $sql);

// Get the patient's current appointments with doctor details
$currentAppointmentsSql = "SELECT appointment.*, doctor.fname AS doctor_fname, doctor.lname AS doctor_lname 
                           FROM appointment 
                           INNER JOIN doctor ON appointment.doctor_id = doctor.doctor_id
                           WHERE appointment.patient_id = '$patientId' AND appointment.appointment_date >= CURDATE()";
$currentAppointmentsResult = mysqli_query($connection, $currentAppointmentsSql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve the selected doctor and appointment date from the form
  $doctorId = $_POST['doctor'];
  $appointmentDate = $_POST['appointment_date'];
  $appointmentTime = $_POST['appointment_time'];

  // Retrieve the doctor's working hours
  $workingHoursSql = "SELECT working_hours_start, working_hours_end FROM doctor WHERE doctor_id = '$doctorId'";
  $workingHoursResult = mysqli_query($connection, $workingHoursSql);
  $workingHoursRow = mysqli_fetch_assoc($workingHoursResult);
  $workingHoursStart = $workingHoursRow['working_hours_start'];
  $workingHoursEnd = $workingHoursRow['working_hours_end'];

  // Construct the appointment datetime for comparison
  $appointmentDateTime = strtotime("$appointmentDate $appointmentTime");

  // Check if the appointment time is within the doctor's working hours
  if (
    $appointmentDateTime < strtotime($appointmentDate . ' ' . $workingHoursStart) ||
    $appointmentDateTime > strtotime($appointmentDate . ' ' . $workingHoursEnd)
  ) {
    $error = "Appointment time is outside the doctor's working hours.";
  } else {
    // Insert the appointment into the database
    $insertSql = "INSERT INTO appointment (patient_id, doctor_id, appointment_date, appointment_time) 
                  VALUES ('$patientId', '$doctorId', '$appointmentDate', '$appointmentTime')";
    mysqli_query($connection, $insertSql);

    // Redirect to a confirmation page or display a success message
    header('Location: appointment_confirmation.php');
    exit();
  }
}

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Appointments</title>
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
      margin-top: 30px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      color: #333;
      font-weight: bold;
    }

    select, input[type="date"], input[type="time"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      font-size: 14px;
    }

    input[type="submit"] {
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

    input[type="submit"]:hover {
      background-color: #286090;
    }

    .error {
      color: red;
      margin-top: 10px;
      text-align: center;
    }

    .appointments {
      margin-top: 30px;
    }

    .appointments h2 {
      margin-bottom: 10px;
      color: #333;
    }

    .appointments table {
      width: 100%;
      border-collapse: collapse;
    }

    .appointments table th,
    .appointments table td {
      padding: 8px;
      text-align: left;
    }

    .appointments table th {
      background-color: #f0f0f0;
    }

    .appointments table tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Schedule an Appointment</h1>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post" action="">
      <label for="doctor">Doctor:</label>
      <select name="doctor" id="doctor" required>
        <option value="">Select a doctor</option>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <option value="<?php echo $row['doctor_id']; ?>"><?php echo $row['fname'] . ' ' . $row['lname']; ?></option>
        <?php } ?>
      </select>

      <label for="appointment_date">Appointment Date:</label>
      <input type="date" name="appointment_date" id="appointment_date" required>

      <label for="appointment_time">Appointment Time:</label>
      <input type="time" name="appointment_time" id="appointment_time" required>

      <input type="submit" value="Schedule Appointment">
    </form>

    <div class="appointments">
      <h2>Current Appointments</h2>
      <?php if (mysqli_num_rows($currentAppointmentsResult) === 0) { ?>
        <p>No appointments found.</p>
      <?php } else { ?>
        <table>
          <tr>
            <th>Doctor</th>
            <th>Date</th>
            <th>Time</th>
          </tr>
          <?php while ($appointment = mysqli_fetch_assoc($currentAppointmentsResult)) { ?>
            <tr>
              <td><?php echo $appointment['doctor_fname'] . ' ' . $appointment['doctor_lname']; ?></td>
              <td><?php echo $appointment['appointment_date']; ?></td>
              <td><?php echo $appointment['appointment_time']; ?></td>
            </tr>
          <?php } ?>
        </table>
      <?php } ?>
    </div>
  </div>
</body>
</html>
