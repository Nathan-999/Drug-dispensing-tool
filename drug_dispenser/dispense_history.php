<?php
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

// Retrieve the drugs prescribed to patients
$sql = "SELECT drug_name, dosage_form, amount FROM prescription";
$result = mysqli_query($connection, $sql);

// Check if any records were found
if (mysqli_num_rows($result) === 0) {
  echo 'No drugs prescribed to patients.';
} else {
  // Start HTML output
  echo '<!DOCTYPE html>';
  echo '<html>';
  echo '<head>';
  echo '<style>
          table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
          }
          th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
          }
          th {
            background-color: #f0f0f0;
          }
        </style>';
  echo '</head>';
  echo '<body>';

  // Display the drugs prescribed in a table with CSS styling
  echo '<table>';
  echo '<tr>
          <th>Drug Name</th>
          <th>Dosage Form</th>
          <th>Amount</th>
        </tr>';
  while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    echo '<td>' . $row['drug_name'] . '</td>';
    echo '<td>' . $row['dosage_form'] . '</td>';
    echo '<td>' . $row['amount'] . '</td>';
    echo '</tr>';
  }
  echo '</table>';

  // End HTML output
  echo '</body>';
  echo '</html>';
}

// Close the database connection
mysqli_close($connection);
?>
