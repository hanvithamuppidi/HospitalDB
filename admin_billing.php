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

    // Fetch all billing information
    $billing_query = "SELECT * FROM billing";
    $billing_result = mysqli_query($conn, $billing_query);

    // Add new billing tuple
    if (isset($_POST['add_billing'])) {
        $patient_id = $_POST['patient_id'];
        $insurance_provider = $_POST['insurance_provider'];
        $billing_amount = $_POST['billing_amount'];
        $admit_date = $_POST['admit_date'];
        $discharge_date = $_POST['discharge_date'];
        $hospital_name = $_POST['hospital_name'];

        // Check if a billing record already exists for the patient
        $check_billing_query = "SELECT * FROM billing WHERE patient_id = $patient_id";
        $check_billing_result = mysqli_query($conn, $check_billing_query);

        if(mysqli_num_rows($check_billing_result) > 0){
            // Display an error message if a billing record already exists
            echo "Error: A billing record already exists for patient ID $patient_id.";
        } else {
            // Insert new billing tuple if no billing record exists
            $insert_query = "INSERT INTO billing (patient_id, insurance_provider, billing_amount, admit_date, discharge_date, hospital_name) VALUES ('$patient_id', '$insurance_provider', '$billing_amount', '$admit_date', '$discharge_date', '$hospital_name')";
            mysqli_query($conn, $insert_query);

            // Refresh the page
            header("Location: admin_billing.php");
            exit();
        }
    }
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Billing </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
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

        nav {
            background-color: #154734;
            color: #fff;
            padding: 10px 0;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            
        }

        nav ul li {
            display: inline;
            margin: 0 10px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
        }

        nav ul li a:hover {
            color: #e87500;
        }

        form {
            margin-bottom: 20px;
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
        input[type="number"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            display: block;
            width: 100%;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Billing</title>
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
        <?php
            if (isset($error_message)) {
                echo '<div class="error">' . $error_message . '</div>';
            }
        ?>
        <h1>Admin - View Billing</h1>
        
        <h2>Add New Billing</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="patient_id">Patient ID:</label>
                <input type="number" id="patient_id" name="patient_id" required>
            </div>
            <div class="form-group">
                <label for="insurance_provider">Insurance Provider:</label>
                <input type="text" id="insurance_provider" name="insurance_provider" required>
            </div>
            <div class="form-group">
                <label for="billing_amount">Billing Amount:</label>
                <input type="number" id="billing_amount" name="billing_amount" min="0" required>
            </div>
            <div class="form-group">
                <label for="admit_date">Admit Date:</label>
                <input type="date" id="admit_date" name="admit_date" required>
            </div>
            <div class="form-group">
                <label for="discharge_date">Discharge Date:</label>
                <input type="date" id="discharge_date" name="discharge_date" required>
            </div>
            <div class="form-group">
                <label for="hospital_name">Hospital Name:</label>
                <input type="text" id="hospital_name" name="hospital_name" required>
            </div>
            <button type="submit" name="add_billing">Add Billing</button>
        </form>

        <h2>All Billing Information</h2>
        <table>
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Insurance Provider</th>
                    <th>Billing Amount</th>
                    <th>Admit Date</th>
                    <th>Discharge Date</th>
                    <th>Hospital Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($billing_result)): ?>
                    <tr>
                        <td><?php echo $row['patient_id']; ?></td>
                        <td><?php echo $row['insurance_provider']; ?></td>
                        <td><?php echo $row['billing_amount']; ?></td>
                        <td><?php echo $row['admit_date']; ?></td>
                        <td><?php echo $row['discharge_date']; ?></td>
                        <td><?php echo $row['hospital_name']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>