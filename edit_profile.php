<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Connect to the database
$conn = mysqli_connect('localhost:3399', 'test', 'test1234', 'hospitaldb');
if (!$conn) {
    die('Connection Error' . mysqli_connect_error());
}

// Fetch the user's profile information based on their role
$username = $_SESSION['username'];
$query = "";
if ($_SESSION['role'] === 'admin') {
    $query = "SELECT * FROM admin WHERE username='$username'";
} elseif ($_SESSION['role'] === 'doctor') {
    $query = "SELECT * FROM doctor WHERE username='$username'";
}
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("User profile not found.");
}

$row = mysqli_fetch_assoc($result);

// Update profile if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($name)) {
        // Update name
        $update_query = "";
        if ($_SESSION['role'] === 'admin') {
            $update_query = "UPDATE admin SET name='$name' WHERE username='$username'";
            $query = "SELECT * FROM admin WHERE username='$username'";
            

        } elseif ($_SESSION['role'] === 'doctor') {
            $update_query = "UPDATE doctor SET name='$name' WHERE username='$username'";
            $query = "SELECT * FROM doctor WHERE username='$username'";
        }
        $update_result = mysqli_query($conn, $update_query);
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        
        if (!$update_result) {
            $error = "Failed to update profile. Please try again.";
        } else {
            // Update name in the session
            $_SESSION['name'] = $name;
            $success = "Profile updated successfully.";
        }
    }

    if (!empty($password)) {
        // Update password
        $update_password_query = "UPDATE login SET password='$password' WHERE username='$username'";
        $update_password_result = mysqli_query($conn, $update_password_query);

        if (!$update_password_result) {
            $error = "Failed to update password. Please try again.";
        } else {
            $success = "Password updated successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        nav {
            background-color: #154734;
            color: #fff;
            padding: 10px 20px;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        nav ul li {
            
            display: inline;
            margin-right: 20px;
        }
        nav ul li a {
            text-decoration: none;
            color: #fff;
        }
        nav ul li a:hover {
            color: #e87500;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            display: block;
            margin: 0 auto;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            background-color: #154734;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #e87500;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="edit_profile.php">Edit Profile</a></li>
            <li><a href="login.php">Sign Out </a><li>
            <!-- Add more navbar items as needed -->
        </ul>
    </nav>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if (isset($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } elseif (isset($success)) { ?>
            <p class="success"><?php echo $success; ?></p>
        <?php } ?>
        <form action="#" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password">
            </div>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>