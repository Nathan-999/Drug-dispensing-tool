<!DOCTYPE html>
<html>
<head>
  <title>View Pharmacists</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin-bottom: 20px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #f5f5f5;
    }

    form {
      display: inline;
    }

    input[type="text"], select {
      width: 100%;
      padding: 6px 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    input[type="submit"], button {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 8px 16px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 14px;
      cursor: pointer;
      border-radius: 4px;
    }

    input[type="submit"]:hover, button:hover {
      background-color: #45a049;
    }

    .add-button {
      margin-top: 10px;
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

  // Check if the form is submitted for updating a pharmacist
  if (isset($_POST['submit'])) {
    // Get the updated values from the form
    $ssn = $_POST['ssn'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Prepare the update query
    $update_query = "UPDATE pharmacist SET fname='$fname', lname='$lname', phone='$phone', email='$email' WHERE SSN='$ssn'";

    // Execute the update query
    if (mysqli_query($connection, $update_query)) {
      echo "Record updated successfully";
    } else {
      echo "Error updating record: " . mysqli_error($connection);
    }
  }

  // Check if the form is submitted for deleting a pharmacist
  if (isset($_POST['delete'])) {
    // Get the SSN of the pharmacist to delete
    $ssn = $_POST['ssn'];

    // Prepare the delete query
    $delete_query = "DELETE FROM pharmacist WHERE SSN='$ssn'";

    // Execute the delete query
    if (mysqli_query($connection, $delete_query)) {
      echo "Record deleted successfully";
    } else {
      echo "Error deleting record: " . mysqli_error($connection);
    }
  }

  // Check if the form is submitted for adding a new pharmacist
  if (isset($_POST['add'])) {
    // Get the new pharmacist's information from the form
    $new_ssn = $_POST['new_ssn'];
    $new_fname = $_POST['new_fname'];
    $new_lname = $_POST['new_lname'];
    $new_phone = $_POST['new_phone'];
    $new_email = $_POST['new_email'];

    // Prepare the insert query
    $insert_query = "INSERT INTO pharmacist (SSN, fname, lname, phone, email) VALUES ('$new_ssn', '$new_fname', '$new_lname', '$new_phone', '$new_email')";

    // Execute the insert query
    if (mysqli_query($connection, $insert_query)) {
      echo "New pharmacist added successfully";
      header("Location: view_pharmacists.php"); // Redirect to the same page to prevent duplicate submissions
      exit();
    } else {
      echo "Error adding new pharmacist: " . mysqli_error($connection);
    }
  }

  // SQL query to retrieve pharmacists
  $sql = "SELECT * FROM pharmacist";
  $result = mysqli_query($connection, $sql);
  ?>

  <h2>View Pharmacists</h2>

  <table>
    <thead>
      <tr>
        <th>SSN</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
          <form method="post">
            <td><?php echo isset($row['SSN']) ? $row['SSN'] : 'N/A'; ?><input type="hidden" name="ssn" value="<?php echo $row['SSN']; ?>"></td>
            <td><input type="text" name="fname" value="<?php echo isset($row['fname']) ? $row['fname'] : ''; ?>"></td>
            <td><input type="text" name="lname" value="<?php echo isset($row['lname']) ? $row['lname'] : ''; ?>"></td>
            <td><input type="text" name="phone" value="<?php echo isset($row['phone']) ? $row['phone'] : ''; ?>"></td>
            <td><input type="text" name="email" value="<?php echo isset($row['email']) ? $row['email'] : ''; ?>"></td>
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
          <td><button type="submit" name="add">Add</button></td>
        </form>
      </tr>
    </tbody>
  </table>

  <!-- Back Button -->
  <button onclick="window.location.href='administrator_page.php'" class="add-button">Back</button>

</body>
</html>
