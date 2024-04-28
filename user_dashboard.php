<?php
    session_start(); // Start the session

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    // Check the role of the logged-in user
    if ($_SESSION['role'] == 'doctor') {
        // User is an admin, display patient information
        $conn = mysqli_connect('localhost:3399', 'test', 'test1234', 'hospitaldb');
        if (!$conn) {
            die('Connection Error'. mysqli_connect_error());
        }

        // Fetch patient information
        $patient_query = "SELECT * FROM patientinfo";
        $patients_result = mysqli_query($conn, $patient_query);
    } 
    else {
        // User is not an admin, redirect to dashboard for non-admins
        header("Location: user_dashboard.php");
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
<body>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="edit_profile.php">Edit Profile</a></li>
            <li><a href="create_patient.php">Patient Intake</a></li>
            <li><a href="login.php">Sign Out </a><li>
            <!-- Add more navbar items as needed -->
        </ul>
    </nav>

    <div class="container">
        <h1>Doctor Page - Patient List</h1>
        
        <?php while ($row = mysqli_fetch_assoc($patients_result)): ?>
            <div class="patient">
                <h2><?php echo $row['name']; ?></h2>
                <p><strong>Age:</strong> <?php echo $row['age']; ?></p>
                <p><strong>Gender:</strong> <?php echo $row['gender']; ?></p>
                <p><strong>Room Number:</strong> <?php echo $row['room_number']; ?></p>
                <!-- Add more patient information as needed -->
                <p><strong>Medications:</strong> <?php echo $row['medication']; ?></p>
                <p><strong>Medical Conditions:</strong> <?php echo $row['medical_conditions']; ?></p>
                <!-- Add edit button with patient ID as parameter -->
                <a href="edit_patient.php?patient_id=<?php echo $row['patient_id']; ?>" class="edit-button">Edit Patient Info</a>
            </div>
        <?php endwhile; ?>
        
    </div>
</body>
</html>