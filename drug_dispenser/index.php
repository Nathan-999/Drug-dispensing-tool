<!DOCTYPE html>
<html>
<head>
  <title>Home</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
      margin: 0;
      padding: 0;
    }

    .header {
      background-color: #337ab7;
      padding: 10px;
      text-align: center;
    }

    .header a {
      display: inline-block;
      margin: 0 10px;
      color: #fff;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .header a:hover {
      background-color: #286090;
    }

    .content {
      width: 400px;
      margin: 0 auto;
      margin-top: 100px;
      padding: 30px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
      text-align: center;
    }

    .content h2 {
      margin-top: 0;
      padding: 20px;
      background-color: #337ab7;
      color: #fff;
      border-top-left-radius: 5px;
      border-top-right-radius: 5px;
    }

    .content p {
      margin-top: 30px;
      color: #333;
      font-size: 16px;
    }
  </style>
</head>
<body>
  <div class="header">
    <a href="login.php">Home</a>
    <a href="about_us.php">About Us</a>
    <a href="testimonies.php">Testimonies</a>
    <a href="services.php">Services</a>
  </div>
  <div class="content">
    <h2>Welcome to Our Healthcare Facility</h2>
    <p>Get access to quality healthcare services by logging in to your account.</p>
    <a href="login.php">Login</a>
  </div>
</body>
</html>
