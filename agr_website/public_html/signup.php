<?php
include('include/functions.php');
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $farmerID = $_POST['username'];
    $farmerName = $_POST['password'];

    // Check if the username is a number
    if (!is_numeric($farmerID)) {
        $errorMsg = "Username must be a number.";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $farmerName)) { // Check if password contains only letters and spaces
        $errorMsg = "Password can only contain letters and spaces.";
    } else {
        if (connectToDB()) {
            // Check if username already exists
            $checkUser = executePlainSQL("SELECT * FROM Farmer WHERE farmerID = '$farmerID'");
            if ($row = oci_fetch_assoc($checkUser)) {
                $errorMsg = "Username already exists.";
            } else {
                // Insert into Farmer table
                $tuple = array(
                    ":farmerID" => $farmerID,
                    ":farmerName" => $farmerName
                );
                $alltuples = array($tuple);
                $result = executeBoundSQL("INSERT INTO Farmer VALUES (:farmerID, :farmerName)", $alltuples);
                if ($result) {
                    oci_commit($db_conn);
                    header("Location: index.php?signup=success");
                    exit();
                } else {
                    $errorMsg = "Insert operation failed.";
                }
            }
            disconnectFromDB();
        } else {
            $errorMsg = "Error: Unable to connect to the database.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Signup</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background-image: url("farm-background.jpg");
            background-size: cover;
            background-repeat: no-repeat;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 5px;
            animation: slide-in 1s ease-in-out;
        }

        @keyframes slide-in {
            0% {
                transform: translateY(-100%);
            }

            100% {
                transform: translateY(0);
            }
        }

        .container h1 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #45a049;
        }

        .form-group p {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Signup</h1>
        <?php
        if (!empty($errorMsg)) {
            echo "<p style='color: red;'>" . $errorMsg . "</p>";
        }
        ?>
        <form action="signup.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="number" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Signup</button>
        </form>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>
</body>

</html>