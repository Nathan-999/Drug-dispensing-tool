<!DOCTYPE html>
<html>
<head>
  <title>View Administrators</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    h2 {
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
    }

    form {
      display: inline;
    }

    input[type="text"],
    input[type="submit"] {
      padding: 4px;
    }

    input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
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

  // Check if the form is submitted for updating an administrator
  if (isset($_POST['submit'])) {
    // Get the updated values from the form
    $ssn = $_POST['ssn'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Prepare the update query
    $update_query = "UPDATE administrator SET fname='$fname', lname='$lname', phone='$phone', email='$email' WHERE SSN='$ssn'";

    // Execute the update query
    mysqli_query($connection, $update_query);
  }

  // Check if the delete button is clicked
  if (isset($_POST['delete'])) {
    // Get the SSN of the administrator to delete
    $ssn = $_POST['ssn'];

    // Prepare the delete query
    $delete_query = "DELETE FROM administrator WHERE SSN='$ssn'";

    // Execute the delete query
    mysqli_query($connection, $delete_query);
  }

  // Check if the form is submitted for adding a new administrator
  if (isset($_POST['add'])) {
    // Get the new administrator's information from the form
    $new_ssn = $_POST['new_ssn'];
    $new_fname = $_POST['new_fname'];
    $new_lname = $_POST['new_lname'];
    $new_phone = $_POST['new_phone'];
    $new_email = $_POST['new_email'];

    // Prepare the insert query
    $insert_query = "INSERT INTO administrator (SSN, fname, lname, phone, email) VALUES ('$new_ssn', '$new_fname', '$new_lname', '$new_phone', '$new_email')";

    // Execute the insert query
    if (mysqli_query($connection, $insert_query)) {
      echo "New administrator added successfully";
      header("Location: view_administrators.php"); // Redirect to the same page to prevent duplicate submissions
      exit();
    } else {
      echo "Error adding new administrator: " . mysqli_error($connection);
    }
  }

  // SQL query to retrieve administrators
  $sql = "SELECT * FROM administrator";
  $result = mysqli_query($connection, $sql);
  ?>

  <h2>View Administrators</h2>

  <div class="table-container">
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
              <td><?php echo $row['SSN']; ?><input type="hidden" name="ssn" value="<?php echo $row['SSN']; ?>"></td>
              <td><input type="text" name="fname" value="<?php echo $row['fname']; ?>"></td>
              <td><input type="text" name="lname" value="<?php echo $row['lname']; ?>"></td>
              <td><input type="text" name="phone" value="<?php echo $row['phone']; ?>"></td>
              <td><input type="text" name="email" value="<?php echo $row['email']; ?>"></td>
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
  </div>

  <a href="administrator_page.php">Back</a>

</body>
</html>
