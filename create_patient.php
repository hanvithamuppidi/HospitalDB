<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check the role of the logged-in user
if ($_SESSION['role'] != 'doctor') {
    // User is not an admin, redirect to dashboard for non-admins
    header("Location: dashboard.php");
    exit();
}

// Connect to the database
$conn = mysqli_connect('localhost:3399', 'test', 'test1234', 'hospitaldb');
if (!$conn) {
    die('Connection Error' . mysqli_connect_error());
}

// Fetch doctors for dropdown
$doctor_query = "SELECT * FROM doctor";
$doctor_result = mysqli_query($conn, $doctor_query);

// Fetch doctor names for validation
$doctor_names = [];
while ($row = mysqli_fetch_assoc($doctor_result)) {
    $doctor_names[] = $row['name'];
}

// Handle form submission to add a new patient
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $room_number = $_POST['room_number'];
    $doctor_name = $_POST['doctor'];
    $medication = $_POST['medications'];
    $medical_conditions = $_POST['conditions'];

    // Validate room number (should be a valid integer)
    if (!ctype_digit($room_number)) {
        $error = "Room number must be a valid integer.";
    }

    // Validate doctor (should be a doctor that exists)
    if (!in_array($doctor_name, $doctor_names)) {
        $error = "Invalid doctor name.";
    }

    // Validate age (should be a valid integer)
    if (!ctype_digit($age)) {
        $error = "Age must be a valid integer.";
    }

    // Validate medications and medical conditions (should not be empty)
    if (empty($medication) || empty($medical_conditions)) {
        $error = "Medications and medical conditions cannot be empty.";
    }

    // If no errors, proceed with insertion
    if (!isset($error)) {
        // Insert new patient information into the database
        $insert_query = "INSERT INTO patientinfo (name, age, gender, room_number, medication, medical_conditions) VALUES ('$name', '$age', '$gender', '$room_number', '$medication', '$medical_conditions')";
        $insert_result = mysqli_query($conn, $insert_query);

        // Retrieve the ID of the newly inserted patient
        $new_patient_id = mysqli_insert_id($conn);

        // Retrieve doctor ID
        $doctor_query = "SELECT id FROM doctor WHERE name = '$doctor_name'";
        $doctor_result = mysqli_query($conn, $doctor_query);
        $doctor_row = mysqli_fetch_assoc($doctor_result);
        $doctor_id = $doctor_row['id'];

        // Insert doctor-patient relationship into the patientcare table
        $insert_patientcare_query = "INSERT INTO patientcare (patient_id, doctor_id) VALUES ('$new_patient_id', '$doctor_id')";
        $insert_patientcare_result = mysqli_query($conn, $insert_patientcare_query);

        if ($insert_result && $insert_patientcare_result) {
            // Redirect to the patient list page after successful insertion
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Failed to add new patient. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Patient</title>
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
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="edit_profile.php">Edit Profile</a></li>
            <li><a href="login.php">Sign Out </a><li>
            <!-- Add more navbar items as needed -->
        </ul>
    </nav>
    <div class="container">
        <h1>Add New Patient</h1>
        <?php if (isset($error)) : ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="#" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="room_number">Room Number:</label>
                <input type="text" id="room_number" name="room_number" required>
            </div>
            <div class="form-group">
                <label for="doctor">Assign Doctor:</label>
                <input type="text" id="doctor" name="doctor" required autocomplete="off" list="doctors">
                <datalist id="doctors">
                    <?php foreach ($doctor_names as $doctor_name) : ?>
                        <option value="<?php echo $doctor_name; ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="form-group">
                <label for="medications">Medications:</label>
                <textarea id="medications" name="medications" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="conditions">Medical Conditions:</label>
                <textarea id="conditions" name="conditions" rows="4"></textarea>
            </div>
            <button type="submit">Add Patient</button>
        </form>
    </div>
</body>

</html>