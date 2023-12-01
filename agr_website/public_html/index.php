<?php
session_start();
include('include/functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $farmerID = $_POST['username'];
    $farmerName = $_POST['password'];

    if (connectToDB()) {
        $result = executePlainSQL("SELECT * FROM Farmer WHERE farmerID = '$farmerID' AND farmerName = '$farmerName'");
        if ($row = oci_fetch_assoc($result)) {
            $_SESSION['farmerID'] = $farmerID;
            header("Location: dashboard.php");
            exit();
        } else {
            // Set an error message in the session
            $_SESSION['error'] = "Login failed. Invalid ID or name.";
            header("Location: index.php"); // Redirect back to the login page
            exit();
        }
        disconnectFromDB();
    } else {
        echo "Error: Unable to connect to the database.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farming Application - [Page Name]</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        html {
            height: 100%;
            position: relative;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            /* Ensures that main content takes up the available space */
        }

        footer {
            width: 100%;
            position: absolute;
            bottom: 0;
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>

<body>
    <header>
        <!-- Navigation Bar -->
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="crop_management.php">Crop Management</a></li>
                <li><a href="harvestday.php">Harvest Log</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="space_management.php">Space Management</a></li>
                <li><a href="disease_diagnosis.php">Disease Diagnosis</a></li>
                <li><a href="waterlog.php">Water Log</a></li>
                <li><a href="field_management.php">Field Management</a></li>
            </ul>
        </nav>
    </header>


    <main>
        <!-- Main content of the page -->
        <div class="welcome-section">
            <h1>Welcome to the Farming and Agricultural Resource Management Application</h1>
            <p>Optimize your cultivation process with our easy-to-use resource management tools.</p>
        </div>

        <div class="login-container">
            <form action="index.php" method="POST">
                <?php
                if (isset($_SESSION['error'])) {
                    echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
                    unset($_SESSION['error']); // Clear the error message after displaying it
                }
                ?>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="number" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Login">
                </div>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up now</a>.</p>
        </div>
    </main>

    <footer>
        <p>&copy; 2023 Farming and Agricultural Resource Management Application</p>
    </footer>
</body>

</html>