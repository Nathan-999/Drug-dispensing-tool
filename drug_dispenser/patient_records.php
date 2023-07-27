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

// Retrieve the list of patients from the patient table
$sql = "SELECT * FROM patient";

// Check if a search query is provided
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($searchQuery)) {
  $searchQuery = mysqli_real_escape_string($connection, $searchQuery);
  $sql .= " WHERE fname LIKE '%$searchQuery%' OR lname LIKE '%$searchQuery%'";
}

$result = mysqli_query($connection, $sql);

// Retrieve the list of drugs from the drug table
$drugSql = "SELECT * FROM drug";
$drugResult = mysqli_query($connection, $drugSql);

// Handle form submission (prescribing drugs)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve the selected patient and drug from the form
  $patientId = $_POST['patient'];
  $drugId = $_POST['drug'];

  // Retrieve the frequency and amount from the form
  $frequency = $_POST['frequency'];
  $amount = $_POST['amount'];

  // Retrieve the patient's first name and last name
  $patientSql = "SELECT fname, lname FROM patient WHERE patient_id = '$patientId'";
  $patientResult = mysqli_query($connection, $patientSql);
  $patientData = mysqli_fetch_assoc($patientResult);
  $fname = $patientData['fname'];
  $lname = $patientData['lname'];

  // Retrieve the drug and dosage form information
  $drugInfoSql = "SELECT drug_name, dosage_form, stock_amount FROM drug WHERE drug_id = '$drugId'";
  $drugInfoResult = mysqli_query($connection, $drugInfoSql);
  $drugInfoData = mysqli_fetch_assoc($drugInfoResult);
  $drug = $drugInfoData['drug_name'];
  $dosageForm = $drugInfoData['dosage_form'];
  $stockAmount = $drugInfoData['stock_amount'];

  // Check if there is enough stock available
  if ($stockAmount < $amount) {
    $errorMessage = "Not enough stock available for the selected drug.";
  } else {
    // Insert the prescription into the prescription table
    $prescriptionSql = "INSERT INTO prescription (patient_id, fname, lname, drug_id, drug_name, dosage_form, frequency, amount) 
                        VALUES ('$patientId', '$fname', '$lname', '$drugId', '$drug', '$dosageForm', '$frequency', '$amount')";
    mysqli_query($connection, $prescriptionSql);

    // Update the stock amount in the drug table
    $updatedStockAmount = $stockAmount - $amount;
    $updateStockSql = "UPDATE drug SET stock_amount = '$updatedStockAmount' WHERE drug_id = '$drugId'";
    mysqli_query($connection, $updateStockSql);

    // Display a success message
    $successMessage = "Prescription added successfully!";
  }
}

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Patient Records</title>
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

    .patient-table {
      margin-top: 30px;
      background-color: #fff;
      padding: 10px;
      border-radius: 3px;
    }

    .patient-table h2 {
      margin: 0;
      padding-bottom: 10px;
      border-bottom: 1px solid #ccc;
      color: #333;
    }

    .patient-table table {
      width: 100%;
      border-collapse: collapse;
    }

    .patient-table table th,
    .patient-table table td {
      padding: 8px;
      text-align: left;
    }

    .patient-table table th {
      background-color: #f0f0f0;
    }

    .patient-table table tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .prescription-form {
      margin-top: 20px;
    }

    .prescription-form label {
      display: block;
      margin-bottom: 10px;
      color: #333;
      font-weight: bold;
    }

    .prescription-form select,
    .prescription-form input[type="text"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      font-size: 14px;
    }

    .prescription-form input[type="submit"] {
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

    .prescription-form input[type="submit"]:hover {
      background-color: #286090;
    }

    .success-message {
      margin-top: 20px;
      background-color: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 3px;
    }

    .error-message {
      margin-top: 20px;
      background-color: #f8d7da;
      color: #721c24;
      padding: 10px;
      border-radius: 3px;
    }

    .back-button {
      display: block;
      margin-top: 20px;
      background-color: #ccc;
      color: #333;
      padding: 10px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      font-size: 16px;
      text-decoration: none;
      text-align: center;
      transition: background-color 0.3s ease;
    }

    .back-button:hover {
      background-color: #999;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Patient Records</h1>

    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <label for="search">Search Patient:</label>
      <input type="text" name="search" id="search" placeholder="Enter first or last name">
      <input type="submit" value="Search">
    </form>

    <div class="patient-table">
      <h2>Patient List</h2>
      <?php if (mysqli_num_rows($result) === 0) { ?>
        <p>No patients found.</p>
      <?php } else { ?>
        <?php if (!empty($searchQuery)) { ?>
          <p>Search Results for: <?php echo htmlspecialchars($searchQuery); ?></p>
        <?php } ?>
        <table>
          <tr>
            <th>Patient ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Prescribe Drug</th>
          </tr>
          <?php while ($patient = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?php echo $patient['patient_id']; ?></td>
              <td><?php echo $patient['fname']; ?></td>
              <td><?php echo $patient['lname']; ?></td>
              <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="prescription-form">
                  <input type="hidden" name="patient" value="<?php echo $patient['patient_id']; ?>">
                  <label for="drug">Prescribe Drug:</label>
                  <select name="drug" id="drug" required>
                    <option value="">Select a drug</option>
                    <?php
                    // Reset the drugResult pointer
                    mysqli_data_seek($drugResult, 0);

                    while ($drug = mysqli_fetch_assoc($drugResult)) { ?>
                      <option value="<?php echo $drug['drug_id']; ?>"><?php echo $drug['drug_name']; ?></option>
                    <?php } ?>
                  </select>
                  <label for="amount">Amount:</label>
                  <input type="number" name="amount" id="amount" min="1" required>
                  <label for="frequency">Frequency:</label>
                  <input type="text" name="frequency" id="frequency" required>
                  <input type="submit" value="Prescribe">
                </form>
              </td>
            </tr>
          <?php } ?>
        </table>
      <?php } ?>
      <?php if (isset($successMessage)) { ?>
        <div class="success-message"><?php echo $successMessage; ?></div>
      <?php } ?>
      <?php if (isset($errorMessage)) { ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
      <?php } ?>
    </div>

    <a class="back-button" href="doctor_page.php">Back</a>
  </div>
</body>
</html>
