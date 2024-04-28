<?php
    session_start(); // Start the session

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    // Check the role of the logged-in user
    if ($_SESSION['role'] == 'admin') {
        // User is an admin, fetch patient information
        $conn = mysqli_connect('localhost:3399', 'test', 'test1234', 'hospitaldb');
        if (!$conn) {
            die('Connection Error'. mysqli_connect_error());
        }
        
        // Fetch lab results information
        $lab_results_query = "SELECT * FROM lab";
        $lab_results_result = mysqli_query($conn, $lab_results_query);

        // Insert new lab result
        if(isset($_POST['add_lab_result'])) {
            $patient_id = $_POST['patient_id'];
            $lab_name = $_POST['lab_name'];
            $test_results = $_POST['test_results'];

            // Check if the patient ID exists in the patientinfo table
            $check_patient_query = "SELECT * FROM patientinfo WHERE patient_id = $patient_id";
            $result = mysqli_query($conn, $check_patient_query);

            if (mysqli_num_rows($result) > 0) {
                // Patient ID exists, proceed with insertion
                $insert_query = "INSERT INTO lab (patient_id, lab_name, test_results) VALUES ('$patient_id', '$lab_name', '$test_results')";
                if(mysqli_query($conn, $insert_query)) {
                    echo "Lab result added successfully!";
                } else {
                    echo "Error: " . $insert_query . "<br>" . mysqli_error($conn);
                }
            } else {
                // Patient ID does not exist, display error message
                echo "Error: Patient ID $patient_id does not exist.";
            }
        }
    } else {
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
    <title>Admin - Lab Results</title>
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
        form {
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="number"],
        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #154734;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #e87500;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #154734;
            color: white;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="edit_profile.php">Edit Profile</a></li>
            <li><a href="admin_billing.php">Patient Billing</a></li>
            <li><a href="lab_results.php">Labs</a></li> 
            <li><a href="login.php">Sign Out </a></li>
            <!-- Add more navbar items as needed -->
        </ul>
    </nav>

    <div class="container">
        <h1>Admin - Lab Results</h1>
        
        <h2>Add New Lab Result</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="patient_id">Patient ID:</label>
                <input type="number" id="patient_id" name="patient_id" required>
            </div>
            <div class="form-group">
                <label for="lab_name">Lab Name:</label>
                <input type="text" id="lab_name" name="lab_name" required>
            </div>
            <div class="form-group">
                <label for="test_results">Test Results:</label>
                <textarea id="test_results" name="test_results" rows="4" required></textarea>
            </div>
            <button type="submit" name="add_lab_result">Add Lab Result</button>
        </form>

        <h2>All Lab Results</h2>
        <table>
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Lab Name</th>
                    <th>Test Results</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($lab_results_result)): ?>
                    <tr>
                        <td><?php echo $row['patient_id']; ?></td>
                        <td><?php echo $row['lab_name']; ?></td>
                        <td><?php echo $row['test_results']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
