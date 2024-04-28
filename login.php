<?php
    session_start(); // Start the session

    $conn = mysqli_connect('localhost:3399', 'test', 'test1234', 'hospitaldb');
    if (!$conn) {
        die('Connection Error'. mysqli_connect_error());
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Sanitize inputs to prevent SQL injection
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        // Query to check if the username and password combination exists
        $query = "SELECT * FROM login WHERE username='$username' AND password='$password'";
        $result0 = mysqli_query($conn, $query);


        if (mysqli_num_rows($result0) == 1) {

            $query2 = "SELECT * FROM admin WHERE username='$username'";
            $result1 = mysqli_query($conn, $query2);
            $query3 = "SELECT * FROM doctor WHERE username='$username'";
            $result2 = mysqli_query($conn, $query3);
            if (mysqli_num_rows($result1) == 1) {
                // Authentication successful
                $user = mysqli_fetch_assoc($result1);
                $_SESSION['role'] = 'admin'; // Store the role in session
                $_SESSION['username'] = $username; // Store the username in session

                
            }
            else if (mysqli_num_rows($result2) == 1) {
                // Authentication successful
                $user = mysqli_fetch_assoc($result2);
                $_SESSION['role'] = 'doctor'; // Store the role in session
                $_SESSION['username'] = $username; // Store the username in session

                
            }

            // Redirect the user to a new page or display a success message
            header("Location: dashboard.php");
            exit();
        } else {
            // Authentication failed
            $error = "Invalid username or password. Please try again."; // Set error message
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UT Dallas Hospital Management Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #154734;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #e87500;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>UT Dallas Hospital Management Database</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div> <!-- Display error message -->
        <?php endif; ?>
        <form action="#" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
