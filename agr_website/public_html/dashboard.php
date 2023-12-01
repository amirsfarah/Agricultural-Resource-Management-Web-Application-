<?php

include('include/functions.php');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$show_debug_alert_messages = False;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farming Application - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
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
        <div class="flex-container">
            <div class="form-column">
                <p style="margin-bottom: 20px;">Welcome to the Dashboard page. Here, you can view any attributes from any table in your farming application.</p>
                <h1 style="margin-bottom: 5px;">Choose the table and attributes you want to view!</h1>

                <?php
                connectToDB();
                $tables = getTables(); // Function to fetch table names

                foreach ($tables as $table) {
                    echo "<form method='POST' action='dashboard.php'>";
                    $table = ucfirst(strtolower($table));

                    echo "<h3>Choose attributes to view from table " . $table . "</h3>";

                    // Fetch and display table attributes as checkboxes
                    $attributes = getTableAttributes($table);
                    foreach ($attributes as $attribute) {
                        echo "<input type='checkbox' name='attributes[]' value='" . ucfirst(strtolower($attribute)) . "'>" . ucfirst(strtolower($attribute)) . "<br>";
                    }

                    echo "<input type='hidden' name='tableName' value='" . $table . "'>";
                    echo "<input type='submit' name='viewTable' value='View " . $table . "'>";
                    echo "</form>";
                }

                function getTables()
                {
                    //get all the tables from the database
                    global $db_conn;
                    $query = "SELECT table_name FROM user_tables";
                    $result = oci_parse($db_conn, $query);
                    oci_execute($result);
                    //return the tables in an array
                    $tables = [];
                    while ($row = oci_fetch_array($result, OCI_ASSOC + OCI_RETURN_NULLS)) {
                        foreach ($row as $item) {
                            array_push($tables, $item);
                        }
                    }
                    return $tables;
                }

                function getTableAttributes($table)
                {
                    global $db_conn;
                    $query = "SELECT column_name FROM user_tab_columns WHERE table_name = '" . strtoupper($table) . "'";
                    $result = oci_parse($db_conn, $query);
                    oci_execute($result);

                    $attributes = [];
                    while ($row = oci_fetch_array($result, OCI_ASSOC + OCI_RETURN_NULLS)) {
                        array_push($attributes, $row['COLUMN_NAME']);
                    }
                    return $attributes;
                }

                ?>
            </div>

            <div class="table-column">
                <?php
                if (isset($_POST['viewTable'])) {
                    $tableName = $_POST['tableName'];
                    $attributes = $_POST['attributes'];
                    $attrString = implode(', ', $attributes);
                    $query = "SELECT " . $attrString . " FROM " . $tableName;
                    $result = oci_parse($db_conn, $query);
                    oci_execute($result);

                    // Display the result as a table
                    echo "<table>";
                    echo "<tr>";
                    foreach ($attributes as $attribute) {
                        echo "<th>" . $attribute . "</th>";
                    }
                    echo "</tr>";

                    while ($row = oci_fetch_array($result, OCI_ASSOC + OCI_RETURN_NULLS)) {
                        echo "<tr>";
                        foreach ($row as $item) {
                            echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2023 Farming and Agricultural Resource Management Application</p>
    </footer>
</body>

</html>