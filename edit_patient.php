<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check the role of the logged-in user
if ($_SESSION['role'] != ('admin' || 'doctor'))  {
    // User is not an admin, redirect to dashboard for non-admins
    header("Location: dashboard.php");
    exit();
}

// Check if the patient ID is provided
if (!isset($_GET['patient_id'])) {
    header("Location: dashboard.php"); // Redirect if patient ID is not provided
    exit();
}

$patient_id = $_GET['patient_id'];

// Connect to the database
$conn = mysqli_connect('localhost:3399', 'test', 'test1234', 'hospitaldb');
if (!$conn) {
    die('Connection Error' . mysqli_connect_error());
}

// Fetch patient information based on the provided ID
$query = "SELECT * FROM patientinfo WHERE patient_id = '$patient_id'";
$result = mysqli_query($conn, $query);
$patient = mysqli_fetch_assoc($result);

// Fetch doctors for dropdown
$doctor_query = "SELECT * FROM doctor";
$doctor_result = mysqli_query($conn, $doctor_query);

// Fetch current assigned doctor
$assigned_doctor_query = "SELECT doctor.* FROM doctor INNER JOIN patientcare ON doctor.id = patientcare.doctor_id WHERE patientcare.patient_id = '$patient_id'";
$assigned_doctor_result = mysqli_query($conn, $assigned_doctor_query);
$assigned_doctor = mysqli_fetch_assoc($assigned_doctor_result);

// Handle form submission to update patient information
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $room_number = $_POST['room_number'];
    $doctor_id = $_POST['doctor'];
    $medication = $_POST['medications'];
    $medical_conditions = $_POST['conditions'];

    // Update patient information in the database
    $update_query = "UPDATE patientinfo SET name='$name', age='$age', gender='$gender', room_number='$room_number', medication='$medication', medical_conditions='$medical_conditions' WHERE patient_id='$patient_id'";
    $update_result = mysqli_query($conn, $update_query);

    // Update doctor-patient relationship in the patientcare table
    $update_patientcare_query = "UPDATE patientcare SET doctor_id='$doctor_id' WHERE patient_id='$patient_id'";
    $update_patientcare_result = mysqli_query($conn, $update_patientcare_query);

    if ($update_result && $update_patientcare_result) {
        // Redirect to the patient list page after successful update
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Failed to update patient information. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient Information</title>
    <style>
        /* Add your CSS styles here */
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
        input[type="number"],
        select,
        textarea {
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit Patient Information</h1>
        <?php if (isset($error)) : ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="#" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $patient['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo $patient['age']; ?>" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="M" <?php if ($patient['gender'] == 'M') echo 'selected'; ?>>Male</option>
                    <option value="F" <?php if ($patient['gender'] == 'F') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="room_number">Room Number:</label>
                <input type="text" id="room_number" name="room_number" value="<?php echo $patient['room_number']; ?>" required>
            </div>
            <div class="form-group">
                <label for="doctor">Current Doctor:</label>
                <select id="doctor" name="doctor" required>
                    <option value="">None</option> <!-- Option for None -->
                    <?php while ($row = mysqli_fetch_assoc($doctor_result)) : ?>
                        <option value="<?php echo $row['id']; ?>" <?php if ($assigned_doctor && $assigned_doctor['id'] == $row['id']) echo 'selected'; ?>><?php echo $row['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="medications">Medications:</label>
                <textarea id="medications" name="medications" rows="4"><?php echo $patient['medication']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="conditions">Medical Conditions:</label>
                <textarea id="conditions" name="conditions" rows="4"><?php echo $patient['medical_conditions']; ?></textarea>
            </div>
            <button type="submit">Save</button>
        </form>
    </div>
</body>

</html>