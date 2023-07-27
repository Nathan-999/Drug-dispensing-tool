<!DOCTYPE html>
<html>
<head>
  <title>Prescription Management</title>
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

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f0f0f0;
    }

    .edit-input {
      width: 100%;
    }

    .save-button {
      display: none;
    }

    .edit-button,
    .save-button {
      padding: 8px 12px;
      background-color: #337ab7;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .edit-button:hover,
    .save-button:hover {
      background-color: #286090;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Prescription Management</h1>

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

    // Get the prescription information from the database
    $sql = "SELECT prescription.prescription_id, prescription.patient_id, patient.fname, patient.lname, prescription.drug_id, drug.drug_name, prescription.dosage_form, prescription.frequency, prescription.amount
            FROM prescription
            INNER JOIN patient ON prescription.patient_id = patient.patient_id
            INNER JOIN drug ON prescription.drug_id = drug.drug_id";
    $result = mysqli_query($connection, $sql);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $prescriptionId = $_POST['prescription_id'];
      $drugId = $_POST['drug_id'];
      $frequency = $_POST['frequency'];
      $amount = $_POST['amount'];

      // Update the prescription with the new drug, frequency, and amount
      $updateSql = "UPDATE prescription SET drug_id = '$drugId', frequency = '$frequency', amount = '$amount' WHERE prescription_id = '$prescriptionId'";
      mysqli_query($connection, $updateSql);
    }
    ?>

    <table>
      <tr>
        <th>Prescription ID</th>
        <th>Patient ID</th>
        <th>Patient Name</th>
        <th>Drug</th>
        <th>Dosage Form</th>
        <th>Frequency</th>
        <th>Amount</th>
        <th>Action</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?php echo $row['prescription_id']; ?></td>
          <td><?php echo $row['patient_id']; ?></td>
          <td><?php echo $row['fname'] . ' ' . $row['lname']; ?></td>
          <td>
            <span class="display-text"><?php echo $row['drug_name']; ?></span>
            <select class="edit-input" style="display: none;">
              <?php
              // Get the list of drugs from the drug table
              $drugSql = "SELECT * FROM drug";
              $drugResult = mysqli_query($connection, $drugSql);

              // Generate the options for the dropdown
              while ($drug = mysqli_fetch_assoc($drugResult)) {
                $selected = ($drug['drug_id'] === $row['drug_id']) ? 'selected' : '';
                echo "<option value='{$drug['drug_id']}' {$selected}>{$drug['drug_name']}</option>";
              }
              ?>
            </select>
          </td>
          <td><?php echo $row['dosage_form']; ?></td>
          <td>
            <span class="display-text"><?php echo $row['frequency']; ?></span>
            <input class="edit-input" type="text" value="<?php echo $row['frequency']; ?>" style="display: none;">
          </td>
          <td>
            <span class="display-text"><?php echo $row['amount']; ?></span>
            <input class="edit-input" type="text" value="<?php echo $row['amount']; ?>" style="display: none;">
          </td>
          <td>
            <button class="edit-button">Edit</button>
            <button class="save-button" style="display: none;">Save</button>
          </td>
        </tr>
      <?php } ?>
    </table>

    <script>
      // Function to toggle between display and edit mode
      function toggleEditMode(row) {
        const displayTexts = row.querySelectorAll('.display-text');
        const editInputs = row.querySelectorAll('.edit-input');
        const editButton = row.querySelector('.edit-button');
        const saveButton = row.querySelector('.save-button');

        if (displayTexts[0].style.display === 'none') {
          // Switch to display mode
          displayTexts.forEach((text) => (text.style.display = 'inline'));
          editInputs.forEach((input) => (input.style.display = 'none'));
          editButton.style.display = 'inline';
          saveButton.style.display = 'none';
        } else {
          // Switch to edit mode
          displayTexts.forEach((text) => (text.style.display = 'none'));
          editInputs.forEach((input) => (input.style.display = 'inline'));
          editButton.style.display = 'none';
          saveButton.style.display = 'inline';

          // Auto-focus on the input field
          editInputs[0].focus();
        }
      }

      // Add event listeners to the edit buttons
      const editButtons = document.querySelectorAll('.edit-button');
      editButtons.forEach((button) => {
        button.addEventListener('click', () => {
          const row = button.parentNode.parentNode;
          toggleEditMode(row);
        });
      });

      // Add event listeners to the save buttons
      const saveButtons = document.querySelectorAll('.save-button');
      saveButtons.forEach((button) => {
        button.addEventListener('click', () => {
          const row = button.parentNode.parentNode;
          const prescriptionId = row.cells[0].textContent;
          const drugId = row.querySelector('.edit-input').value;
          const frequency = row.querySelectorAll('.edit-input')[1].value;
          const amount = row.querySelectorAll('.edit-input')[2].value;

          // Send the prescription ID, updated drug ID, frequency, and amount to the server
          const formData = new FormData();
          formData.append('prescription_id', prescriptionId);
          formData.append('drug_id', drugId);
          formData.append('frequency', frequency);
          formData.append('amount', amount);

          // Send a POST request to update the prescription
          fetch(window.location.href, {
            method: 'POST',
            body: formData
          })
            .then((response) => response.text())
            .then((data) => {
              // Toggle back to display mode and update the display text
              toggleEditMode(row);
              row.querySelectorAll('.display-text')[0].textContent = drugId;
              row.querySelectorAll('.display-text')[1].textContent = frequency;
              row.querySelectorAll('.display-text')[2].textContent = amount;
            })
            .catch((error) => {
              console.error('Error:', error);
            });
        });
      });
    </script>
  </div>
</body>
</html>
