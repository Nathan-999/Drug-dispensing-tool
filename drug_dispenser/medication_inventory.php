<!DOCTYPE html>
<html>
<head>
  <title>Medication Inventory</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 20px;
    }

    h2 {
      color: #333;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #4CAF50;
      color: white;
    }

    td input[type="text"],
    td input[type="number"] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    td input[type="submit"],
    td a {
      padding: 8px 12px;
      background-color: #337ab7;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      font-size: 14px;
      transition: background-color 0.3s ease;
    }

    td input[type="submit"]:hover,
    td a:hover {
      background-color: #286090;
    }

    .stock-actions {
      display: flex;
      align-items: center;
    }

    .stock-actions button {
      margin-right: 5px;
      padding: 5px;
      background-color: #ddd;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    a.back-link {
      display: inline-block;
      margin-top: 20px;
      color: #333;
      font-size: 14px;
      text-decoration: none;
    }

    a.back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <?php
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

  // Update medication information if the form is submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
      $name = $_POST['name'];
      $price = $_POST['price'];
      $dosageForm = $_POST['dosage_form'];
      $stockAmount = $_POST['stock_amount'];

      // Insert the new drug into the drug table
      $insertSql = "INSERT INTO drug (drug_name, price, dosage_form, stock_amount) VALUES ('$name', '$price', '$dosageForm', '$stockAmount')";
      mysqli_query($connection, $insertSql);

      // Redirect to the same page to prevent form resubmission
      header("Location: ".$_SERVER['PHP_SELF']);
      exit();
    } else {
      $drugId = $_POST['drug_id'];
      $name = $_POST['name'];
      $price = $_POST['price'];
      $dosageForm = $_POST['dosage_form'];
      $stockAmount = $_POST['stock_amount'];

      // Update the drug table
      $updateSql = "UPDATE drug SET drug_name='$name', price='$price', dosage_form='$dosageForm', stock_amount='$stockAmount' WHERE drug_id='$drugId'";
      mysqli_query($connection, $updateSql);

      // Redirect to the same page to prevent form resubmission
      header("Location: ".$_SERVER['PHP_SELF']);
      exit();
    }
  }

  // Delete medication information if the delete button is clicked
  if (isset($_GET['delete'])) {
    $drugId = $_GET['delete'];

    // Delete the drug from the drug table
    $deleteSql = "DELETE FROM drug WHERE drug_id='$drugId'";
    mysqli_query($connection, $deleteSql);

    // Redirect to the same page after deleting the drug
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
  }

  // Fetch the medication inventory from the drug table
  $sql = "SELECT * FROM drug";
  $result = mysqli_query($connection, $sql);

  // Check if any records are found
  if (mysqli_num_rows($result) > 0) {
    echo "<h2>Medication Inventory</h2>";
    echo "<table>";
    echo "<tr>
            <th>Name</th>
            <th>Price</th>
            <th>Dosage Form</th>
            <th>Stock Amount</th>
            <th>Action</th>
          </tr>";

    // Output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
      echo "<input type='hidden' name='drug_id' value='" . $row['drug_id'] . "'>";
      echo "<td><input type='text' name='name' value='" . $row['drug_name'] . "'></td>";
      echo "<td><input type='text' name='price' value='" . $row['price'] . "'></td>";
      echo "<td><input type='text' name='dosage_form' value='" . $row['dosage_form'] . "'></td>";
      echo "<td>
              <div class='stock-actions'>
                <button type='button' onclick='decreaseStock(this)'>-</button>
                <input class='stock-amount' type='number' name='stock_amount' value='" . $row['stock_amount'] . "' min='0'>
                <button type='button' onclick='increaseStock(this)'>+</button>
              </div>
            </td>";
      echo "<td>
              <input type='submit' name='update' value='Update'>
              <a href='" . $_SERVER['PHP_SELF'] . "?delete=" . $row['drug_id'] . "'>Delete</a>
            </td>";
      echo "</form>";
      echo "</tr>";
    }

    echo "<tr>";
    echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
    echo "<input type='hidden' name='add' value='true'>";
    echo "<td><input type='text' name='name' placeholder='Name' required></td>";
    echo "<td><input type='text' name='price' placeholder='Price' required></td>";
    echo "<td><input type='text' name='dosage_form' placeholder='Dosage Form' required></td>";
    echo "<td><input type='number' name='stock_amount' placeholder='Stock Amount' min='0' required></td>";
    echo "<td><input type='submit' value='Add'></td>";
    echo "</form>";
    echo "</tr>";

    echo "</table>";
  } else {
    echo "No records found in the medication inventory.";
  }

  // Close the database connection
  mysqli_close($connection);
  ?>

  <a href="pharmacist_page.php" class="back-link">Back to Pharmacist Page</a>

  <script>
    function increaseStock(button) {
      var inputElement = button.parentNode.querySelector('.stock-amount');
      inputElement.stepUp();
    }

    function decreaseStock(button) {
      var inputElement = button.parentNode.querySelector('.stock-amount');
      if (inputElement.value > 0) {
        inputElement.stepDown();
      }
    }
  </script>
</body>
</html>
