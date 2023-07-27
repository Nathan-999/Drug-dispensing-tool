<!DOCTYPE html>
<html>
<head>
  <title>View Doctors</title>
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

    .form-row {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .form-row input[type="text"] {
      margin-right: 10px;
    }

    .form-row .form-actions {
      display: flex;
      gap: 10px;
    }

    .back-button {
      margin-top: 10px;
    }
  </style>
  <script>
    function incrementYearsOfExperience(button) {
      var inputField = button.parentNode.querySelector('.experience-input');
      inputField.value = parseInt(inputField.value) + 1;
    }

    function decrementYearsOfExperience(button) {
      var inputField = button.parentNode.querySelector('.experience-input');
      var value = parseInt(inputField.value);
      if (value > 0) {
        inputField.value = value - 1;
      }
    }
  </script>
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

  // Check if the form is submitted for adding a new doctor
  if (isset($_POST['add'])) {
    // Get the new doctor's information from the form
    $new_ssn = $_POST['new_ssn'];
    $new_fname = $_POST['new_fname'];
    $new_lname = $_POST['new_lname'];
    $new_specialty = $_POST['new_specialty'];
    $new_years_of_experience = $_POST['new_years_of_experience'];
    $new_phone = $_POST['new_phone'];
    $new_email = $_POST['new_email'];

    // Check if the SSN is already taken
    $check_query = "SELECT * FROM doctor WHERE SSN='$new_ssn'";
    $check_result = mysqli_query($connection, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
      echo "Error: SSN already exists!";
    } else {
      // Prepare the insert query
      $insert_query = "INSERT INTO doctor (SSN, fname, lname, specialty, years_of_experience, phone, email) VALUES ('$new_ssn', '$new_fname', '$new_lname', '$new_specialty', '$new_years_of_experience', '$new_phone', '$new_email')";

      // Execute the insert query
      mysqli_query($connection, $insert_query);

      // Redirect to the same page to clear the form
      header("Location: view_doctors.php");
      exit();
    }
  }

  // Check if the form is submitted for updating a doctor
  if (isset($_POST['submit'])) {
    // Get the updated doctor's information from the form
    $ssn = $_POST['ssn'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $specialty = $_POST['specialty'];
    $years_of_experience = $_POST['years_of_experience'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Prepare the update query
    $update_query = "UPDATE doctor SET fname='$fname', lname='$lname', specialty='$specialty', years_of_experience='$years_of_experience', phone='$phone', email='$email' WHERE SSN='$ssn'";

    // Execute the update query
    mysqli_query($connection, $update_query);

    // Redirect to the same page to update the table
    header("Location: view_doctors.php");
    exit();
  }

  // Check if the form is submitted for deleting a doctor
  if (isset($_POST['delete_ssn'])) {
    // Get the SSN of the doctor to be deleted
    $delete_ssn = $_POST['delete_ssn'];

    // Prepare the delete query
    $delete_query = "DELETE FROM doctor WHERE SSN='$delete_ssn'";

    // Execute the delete query
    mysqli_query($connection, $delete_query);

    // Redirect to the same page to update the table
    header("Location: view_doctors.php");
    exit();
  }

  // SQL query to retrieve doctors
  $sql = "SELECT * FROM doctor";
  $result = mysqli_query($connection, $sql);
  ?>

  <h2>View Doctors</h2>

  <table>
    <thead>
      <tr>
        <th>SSN</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Specialty</th>
        <th>Years of Experience</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
          <form method="post">
            <td><input type="text" name="ssn" value="<?php echo $row['SSN']; ?>" readonly></td>
            <td><input type="text" name="fname" value="<?php echo $row['fname']; ?>"></td>
            <td><input type="text" name="lname" value="<?php echo $row['lname']; ?>"></td>
            <td><input type="text" name="specialty" value="<?php echo $row['specialty']; ?>"></td>
            <td>
              <div class="form-row">
                <input type="button" class="increment-button" value="+" onclick="incrementYearsOfExperience(this)">
                <input type="text" name="years_of_experience" value="<?php echo $row['years_of_experience']; ?>" class="experience-input">
                <input type="button" class="decrement-button" value="-" onclick="decrementYearsOfExperience(this)">
              </div>
            </td>
            <td><input type="text" name="phone" value="<?php echo $row['phone']; ?>"></td>
            <td><input type="text" name="email" value="<?php echo $row['email']; ?>"></td>
            <td>
              <div class="form-actions">
                <input type="submit" name="submit" value="Save">
                <button type="submit" name="delete_ssn" value="<?php echo $row['SSN']; ?>">Delete</button>
              </div>
            </td>
          </form>
        </tr>
      <?php endwhile; ?>

      <!-- Add new doctor row -->
      <tr>
        <form method="post">
          <td><input type="text" name="new_ssn" placeholder="SSN"></td>
          <td><input type="text" name="new_fname" placeholder="First Name"></td>
          <td><input type="text" name="new_lname" placeholder="Last Name"></td>
          <td><input type="text" name="new_specialty" placeholder="Specialty"></td>
          <td>
            <div class="form-row">
              <input type="button" class="increment-button" value="+" onclick="incrementYearsOfExperience(this)">
              <input type="text" name="new_years_of_experience" placeholder="Years of Experience" class="experience-input">
              <input type="button" class="decrement-button" value="-" onclick="decrementYearsOfExperience(this)">
            </div>
          </td>
          <td><input type="text" name="new_phone" placeholder="Phone"></td>
          <td><input type="text" name="new_email" placeholder="Email"></td>
          <td>
            <div class="form-actions">
              <button type="submit" name="add">Add</button>
            </div>
          </td>
        </form>
      </tr>
    </tbody>
  </table>

  <form method="post" action="administrator_page.php" class="back-button">
    <button type="submit">Back</button>
  </form>

</body>
</html>
