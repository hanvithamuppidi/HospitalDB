<?php
    session_start(); // Start the session

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    // Check the role of the logged-in user
    if ($_SESSION['role'] != 'admin') {
        // User is not an admin, redirect to dashboard for non-admins
        header("Location: dashboard.php");
        exit();
    }

    // Database connection
    $conn = mysqli_connect('localhost:3399', 'test', 'test1234', 'hospitaldb');
    if (!$conn) {
        die('Connection Error'. mysqli_connect_error());
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate input
        $name = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
    
        if (empty($name) || empty($username) || empty($password)) {
            $error = "All fields are required.";
        } else {
            // Connect to the database
            $conn = mysqli_connect('localhost:3399', 'test', 'test1234', 'hospitaldb');
            if (!$conn) {
                die('Connection Error' . mysqli_connect_error());
            }
    
            // Check if the username already exists
            $check_username_query = "SELECT * FROM login WHERE username='$username'";
            $check_username_result = mysqli_query($conn, $check_username_query);
    
            if (mysqli_num_rows($check_username_result) > 0) {
                $error = "Username already exists. Please choose a different username.";
            } else {
                // Insert the new login details
                $insert_login_query = "INSERT INTO login (username, password) VALUES ('$username', '$password')";
                $insert_login_result = mysqli_query($conn, $insert_login_query);
    
                if ($insert_login_result) {
                    // Insert the new doctor details
                    $insert_doctor_query = "INSERT INTO doctor (name, username) VALUES ('$name', '$username')";
                    $insert_doctor_result = mysqli_query($conn, $insert_doctor_query);
    
                    if ($insert_doctor_result) {
                        // Redirect to the page where the new doctor's information can be edited
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $error = "Failed to create a new doctor. Please try again.";
                    }
                } else {
                    $error = "Failed to create a new login. Please try again.";
                }
            }
        }
    }
    // Fetch all doctors
    $doctor_query = "SELECT * FROM doctor";
    $doctor_result = mysqli_query($conn, $doctor_query);

    // Delete doctor
    if (isset($_POST['delete'])) {
        $username = $_POST['username'];

        // Delete from login table
        $delete_login_query = "DELETE FROM login WHERE username='$username'";
        mysqli_query($conn, $delete_login_query);

        // Delete from doctor table
        $delete_doctor_query = "DELETE FROM doctor WHERE username='$username'";
        mysqli_query($conn, $delete_doctor_query);

        // Refresh the page
        header("Location: admin_doctorView.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page - Patient List</title>
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
        .patient {
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .patient:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .patient h2 {
            margin-bottom: 5px;
        }
        .patient p {
            margin: 5px 0;
        }
        .edit-button {
            display: block;
            width: 100%;
            padding: 8px;
            text-align: center;
            background-color: #154734;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .edit-button:hover {
            background-color: #e87500;
        }
    </style>
</head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Doctors</title>
    <style>
        /* Your CSS styles */
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="edit_profile.php">Edit Profile</a></li>
            <!-- Add more navbar items as needed -->
        </ul>
    </nav>

    <div class="container">
        <h1>Admin - View Doctors</h1>
        
        <?php while ($row = mysqli_fetch_assoc($doctor_result)): ?>
            <div class="doctor">
                <h2><?php echo $row['name']; ?></h2>
                <p><strong>Username:</strong> <?php echo $row['username']; ?></p>
                <!-- Add more doctor information as needed -->
                <form action="" method="post">
                    <input type="hidden" name="username" value="<?php echo $row['username']; ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
            </div>
        <?php endwhile; ?>
        
    </div>
</body>
</html>