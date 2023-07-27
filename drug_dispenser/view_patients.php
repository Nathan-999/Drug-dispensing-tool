<!DOCTYPE html>
<html>
<head>
  <title>View Patients</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #e9e9e9;
    }

    input[type="text"] {
      width: 100%;
      box-sizing: border-box;
      padding: 6px 10px;
      font-size: 14px;
    }

    button {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 8px 16px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 14px;
      cursor: pointer;
    }

    button:hover {
      background-color: #45a049;
    }

    form {
      display: inline;
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

  // Check if the form is submitted for updating an existing patient
  if (isset($_POST['submit'])) {
    // Get the updated values from the form
    $ssn = $_POST['ssn'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];

    // Prepare the update query
    $update_query = "UPDATE patient SET fname='$fname', lname='$lname', phone='$phone', email='$email', gender='$gender' WHERE ssn='$ssn'";

    // Execute the update query and check if it was successful
    if (mysqli_query($connection, $update_query)) {
      echo "Patient updated successfully.";
    } else {
      echo "Error updating patient: " . mysqli_error($connection);
    }
  }

  // Check if the delete button is clicked
  if (isset($_POST['delete'])) {
    // Get the SSN of the patient to delete
    $ssn = $_POST['ssn'];

    // Prepare the delete query
    $delete_query = "DELETE FROM patient WHERE ssn='$ssn'";

    // Execute the delete query and check if it was successful
    if (mysqli_query($connection, $delete_query)) {
      echo "Patient deleted successfully.";
    } else {
      echo "Error deleting patient: " . mysqli_error($connection);
    }
  }

  // SQL query to retrieve patients
  $sql = "SELECT * FROM patient";
  $result = mysqli_query($connection, $sql);

  // Check if the form is submitted for adding a new patient
  if (isset($_POST['add'])) {
    // Get the new patient's information from the form
    $new_ssn = $_POST['new_ssn'];
    $new_fname = $_POST['new_fname'];
    $new_lname = $_POST['new_lname'];
    $new_phone = $_POST['new_phone'];
    $new_email = $_POST['new_email'];
    $new_gender = $_POST['new_gender'];

    // Prepare the insert query
    $insert_query = "INSERT INTO patient (ssn, fname, lname, phone, email, gender) VALUES ('$new_ssn', '$new_fname', '$new_lname', '$new_phone', '$new_email', '$new_gender')";

    // Execute the insert query and check if it was successful
    if (mysqli_query($connection, $insert_query)) {
      echo "New patient added successfully.";
      header("Location: view_patients.php"); // Redirect to the same page to prevent duplicate submissions
      exit();
    } else {
      echo "Error adding new patient: " . mysqli_error($connection);
    }
  }
  ?>

  <h2>View Patients</h2>

  <table>
    <thead>
      <tr>
        <th>SSN</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
          <form method="post">
            <td><?php echo $row['ssn']; ?><input type="hidden" name="ssn" value="<?php echo $row['ssn']; ?>"></td>
            <td><input type="text" name="fname" value="<?php echo $row['fname']; ?>"></td>
            <td><input type="text" name="lname" value="<?php echo $row['lname']; ?>"></td>
            <td><input type="text" name="phone" value="<?php echo $row['phone']; ?>"></td>
            <td><input type="text" name="email" value="<?php echo $row['email']; ?>"></td>
            <td>
              <select name="gender">
                <option value="M" <?php if ($row['gender'] == 'M') echo 'selected'; ?>>M</option>
                <option value="F" <?php if ($row['gender'] == 'F') echo 'selected'; ?>>F</option>
              </select>
            </td>
            <td>
              <input type="submit" name="submit" value="Save">
              <button type="submit" name="delete">Delete</button>
            </td>
          </form>
        </tr>
      <?php endwhile; ?>
      <tr>
        <form method="post">
          <td><input type="text" name="new_ssn" placeholder="SSN"></td>
          <td><input type="text" name="new_fname" placeholder="First Name"></td>
          <td><input type="text" name="new_lname" placeholder="Last Name"></td>
          <td><input type="text" name="new_phone" placeholder="Phone"></td>
          <td><input type="text" name="new_email" placeholder="Email"></td>
          <td>
            <select name="new_gender">
              <option value="M">M</option>
              <option value="F">F</option>
            </select>
          </td>
          <td><button type="submit" name="add">Add</button></td>
        </form>
      </tr>
    </tbody>
  </table>

  <a href="administrator_page.php">Back</a>

</body>
</html>
