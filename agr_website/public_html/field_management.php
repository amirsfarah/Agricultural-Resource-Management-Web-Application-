<?php
session_start();
include('include/functions.php');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (!isset($_SESSION['farmerID'])) {
    header("Location: index.php"); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Management</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .condition-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .condition-row select,
        .condition-row input[type="text"] {
            margin-right: 10px;
        }

        /* Style for conjunction (AND/OR) dropdown */
        .conjunction-select {
            margin-right: 10px;
            display: none;
            /* Initially hidden, will be shown for subsequent conditions */
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
        <!-- Harvest Day queries -->

        <div class="flex-container">
            <div class="form-column">
                <p style="margin-bottom: 20px;">Welcome to the Field Management page. Here, you can add, update, delete and select field data into your farming application.</p>
                <h1 style="margin-bottom: 5px;">Field Management</h1>
                <!-- INSERT -->
                <form method="post" action="field_management.php" onsubmit="return validateFieldForm()">
                    <h3>Add New Field</h3>
                    <input type="hidden" name="insertField" value="insertField">
                    Field ID: <input type="number" name="fieldID" required><br>
                    Planted?:
                    <input type="checkbox" name="isPlanted" value="1"> Yes
                    <input type="checkbox" name="isPlanted" value="0"> No<br>
                    Soil Type: <input type="text" name="soilType" pattern="[A-Za-z]+" title="Soil Type must contain only letters." required><br>
                    Field Size: <input type="number" step="0.01" name="fieldSize" min="0" required><br>
                    <input type="submit" name="insertSubmit" value="Insert Field"> <br><br>
                    <div id="fieldInsertMessage" style="color: red;"></div>
                </form>
                <!-- UPDATE -->
                <form method="post" action="field_management.php" onsubmit="return validateUpdateFieldForm()">
                    <h3>Update Field</h3>
                    <input type="hidden" name="updateField" value="updateField">
                    Field ID: <input type="number" name="fieldID" required><br>
                    Planted?:
                    <input type="checkbox" name="isPlanted" value="1"> Yes
                    <input type="checkbox" name="isPlanted" value="0"> No<br>
                    Soil Type: <input type="text" name="soilType" pattern="[A-Za-z]+" title="Soil Type must contain only letters."><br>
                    Field Size: <input type="number" step="0.01" name="fieldSize" min="0"><br>
                    <input type="submit" name="updateSubmit" value="Update Field"><br><br>
                    <div id="fieldUpdateMessage" style="color: red;"></div>
                </form>
                <!-- DELETE -->
                <form method="post" action="field_management.php" onsubmit="return validateFieldForm()">
                    <h3>Delete Field</h3>
                    <input type="hidden" name="deleteField" value="deleteField">
                    Field ID: <input type="number" name="fieldID" required><br>
                    <input type="submit" name="deleteSubmit" value="Delete Field"><br><br>
                    <div id="fieldDeleteMessage" style="color: red;"></div>
                </form>
                <!-- DIVISON -->
                <form method="get" action="field_management.php" onsubmit="return validateUpdateFieldForm()">
                    <h3>View Field with All Diseases</h3>
                    <p>Filter Your Conditions</p>
                    <!-- Filter Conditions -->
                    <div id="conditionFields">
                        <div class="condition-row">
                            <select class="conjunction-select" name="conjunctions[]">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                            <select name="attributes[]">
                                <option value="soilType">Soil Type</option>
                                <option value="fieldSize">Field Size</option>
                            </select>
                            <select name="operators[]">
                                <option value="="> = </option>
                                <option value="<">
                                    < </option>
                                <option value=">"> > </option>
                                <option value="<=">
                                    <= </option>
                                <option value=">="> >= </option>
                            </select>
                            <input type="text" name="values[]" placeholder="Value" required>
                        </div>
                    </div>
                    <button type="button" onclick="addCondition()">Add Another Condition</button>
                    <input type="submit" name="divisionField" value="View Field"><br>
                    <div id="fieldDivMessage" style="color: red;"></div>
                </form>

            </div>

            <div class="table-column">
                <h2>Field Overview</h2> <br>
                <div id="table-display">
                    <table>
                        <?php
                        if (connectToDB()) {
                            handleDisplayRequestField();
                            disconnectFromDB();
                        }
                        ?>
                    </table>
                    <br>
                </div>
                <div id="table-display">
                    <?php
                    if (isset($_GET['divisionField'])) {
                        echo "<h2>View Field with All Diseases</h2><br>";
                        handleGETRequest();
                    }
                    ?>
                </div>
                <br>
                <button onclick="reloadPage()">Reload Table</button>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2023 Farming and Agricultural Resource Management Application</p>
    </footer>

    <script>
        function validateFieldForm() {
            var fieldID = document.getElementById("fieldID").value;
            var isPlanted = document.getElementById("isPlanted").value;
            var soilType = document.getElementById("soilType").value;
            var fieldSize = document.getElementById("fieldSize").value;

            var letters = /^[A-Za-z]+$/;

            if (soilType !== "" && !soilType.match(letters)) {
                alert("Soil Type must contain only letters.");
                return false;
            }

            if (fieldID === "" || soilType === "" || fieldSize === "" || isPlanted === NULL) {
                alert("Please fill in all the fields.");
                return false;
            }

            if (fieldID < 0 || fieldSize < 0) {
                alert("Must be a non-negative number");
                return false;
            }

            return true;
        }

        function validateUpdateFieldForm() {
            var fieldID = document.getElementById("fieldID").value;
            var isPlanted = document.getElementById("isPlanted").value;
            var soilType = document.getElementById("soilType").value;
            var fieldSize = document.getElementById("fieldSize").value;

            var letters = /^[A-Za-z]+$/;

            if (fieldID === "") {
                alert("Please fill in at least one field to update.");
                return false;
            }

            if (soilType === "" && fieldSize === "" && isPlanted === NULL) {
                alert("Please fill in at least one field to update.");
                return false;
            }

            if (fieldID < 0 || fieldSize < 0) {
                alert("Please enter a positive number for fields.");
                return false;
            }

            if (soilType !== "" && !soilType.match(letters)) {
                alert("Soil Type must contain only letters.");
                return false;
            }
            return true;
        }

        <?php if (isset($_SESSION['fieldDeleteMessage'])) : ?>
            var messageBox = document.getElementById('fieldDeleteMessage');
            messageBox.textContent = '<?php echo $_SESSION['fieldDeleteMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['fieldDeleteMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['fieldInsertMessage'])) : ?>
            var insertMessageBox = document.getElementById('fieldInsertMessage');
            insertMessageBox.textContent = '<?php echo $_SESSION['fieldInsertMessage']; ?>';
            insertMessageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['fieldInsertMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['fieldUpdateMessage'])) : ?>
            var updateMessageBox = document.getElementById('fieldUpdateMessage');
            updateMessageBox.textContent = '<?php echo $_SESSION['fieldUpdateMessage']; ?>';
            updateMessageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['fieldUpdateMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['fieldDivMessage'])) : ?>
            var updateMessageBox = document.getElementById('fieldDivMessage');
            updateMessageBox.textContent = '<?php echo $_SESSION['fieldDivMessage']; ?>';
            updateMessageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['fieldDivMessage']); // Clear the session variable 
        ?>
    </script>
    <?php


    //SELECT * FROM HARVEST DAY////////////
    function handleDisplayRequestField()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT fieldID, isPlanted, soilType, fieldSize FROM Field WHERE farmerID = '" . $_SESSION['farmerID'] . "'");
        printResult($result);
    }

    /////INSERT
    function handleInsertField()
    {
        global $db_conn;

        $field_ID = $_POST['fieldID'];
        $is_Planted = isset($_POST['isPlanted']) ? (int)$_POST['isPlanted'] : 0;
        $soil_Type = $_POST['soilType'];
        $field_Size = $_POST['fieldSize'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (checkIfIdExists($db_conn, "Field", "fieldID", $field_ID)) {
            $_SESSION['fieldInsertMessage'] = "Field ID already exists. Please enter a unique ID.";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "Field", "fieldID", $field_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['fieldInsertMessage'] = "You do not have permission to update this water log record.";
            return;
        }

        if (isset($_SESSION['farmerID'])) {
            $tuple = array(
                ":field_ID" => $field_ID,
                ":is_Planted" => $is_Planted,
                ":soil_Type" => $soil_Type,
                ":field_Size" => $field_Size,
                ":currentFarmerID" => $currentFarmerID
            );

            $alltuples = array(
                $tuple
            );

            executeBoundSQL("INSERT INTO Field (fieldID, isPlanted, soilType, fieldSize, farmerID) VALUES (:field_ID, :is_Planted, :soil_Type, :field_Size, :currentFarmerID)", $alltuples);
            $result = oci_commit($db_conn);
            if ($result) {
                $_SESSION['fieldInsertMessage'] = "Field successfully inserted.";
            } else {
                $_SESSION['fieldInsertMessage'] = "Error inserting Field. Please try again.";
            }
        }
    }


    ////UPDATE/////
    function handleUpdateField()
    {
        global $db_conn;

        $field_ID = $_POST['fieldID'];
        $is_Planted = isset($_POST['isPlanted']) ? (int)$_POST['isPlanted'] : 0;
        $soil_Type = $_POST['soilType'] ?? null;
        $field_Size = $_POST['fieldSize'] ?? null;
        $currentFarmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "Field", "fieldID", $field_ID)) {
            $_SESSION['fieldUpdateMessage'] = "No field found with the specified ID. Please enter a valid ID.";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "Field", "fieldID", $field_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['fieldUpdateMessage'] = "You do not have permission to update this water log record.";
            return;
        }

        $queryParts = array();

        if (!is_null($soil_Type) && $soil_Type !== '') {
            $queryParts[] = "soilType = '" . $soil_Type . "'";
        }
        if ($is_Planted !== null) {
            $queryParts[] = "isPlanted = '" . $is_Planted . "'";
        }
        if (!is_null($field_Size) && $field_Size !== '') {
            $queryParts[] = "fieldSize = '" . $field_Size . "'";
        }

        // If no fields are set to be updated, return to avoid running an empty UPDATE statement
        if (empty($queryParts)) {
            echo "No fields to update.";
            return;
        }

        $query = "UPDATE Field SET " . implode(", ", $queryParts) . " WHERE fieldID = '" . $field_ID . "'";

        // Execute the query
        executePlainSQL($query);
        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['fieldUpdateMessage'] = "Field successfully updated.";
        } else {
            $_SESSION['fieldUpdateMessage'] = "Error updating Field. Please try again.";
        }
    }

    ////DELETE////
    function handleDeleteField()
    {
        global $db_conn;

        $field_ID = $_POST['fieldID'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "Field", "fieldID", $field_ID)) {
            $_SESSION['fieldDeleteMessage'] = "Field ID does not exist. Please enter a valid ID.";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "Field", "fieldID", $field_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['fieldDeleteMessage'] = "You do not have permission to update this water log record.";
            return;
        }

        executePlainSQL("DELETE FROM Field WHERE fieldID = '" . $field_ID . "'");
        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['fieldDeleteMessage'] = "Field successfully deleted.";
        } else {
            $_SESSION['fieldDeleteMessage'] = "Error deleting Field. Please try again.";
        }
    }

    /////DIVISION//////
    function handleGetAllDiseaseField()
    {
        global $db_conn;

        $conditions = [];

        for ($i = 0; $i < count($_GET['attributes']); $i++) {
            $attribute = isset($_GET['attributes'][$i]) ? $_GET['attributes'][$i] : '';
            $operator = isset($_GET['operators'][$i]) ? $_GET['operators'][$i] : '';
            $value = isset($_GET['values'][$i]) ? $_GET['values'][$i] : '';
            $conjunction = ($i > 0) ? isset($_GET['conjunctions'][$i - 1]) ? $_GET['conjunctions'][$i] : '' : "";

            if ($attribute == "fieldSize") {
                // For integer and float, we assume the input is correctly formatted
            } else {
                $value = "'$value'";
            }

            $condition = "$conjunction $attribute $operator $value";
            $conditions[] = $condition;
        }

        if (isset($_SESSION['farmerID']) && !empty($_SESSION['farmerID'])) {
            $farmerID = $_SESSION['farmerID'];
            $conditions[] = "AND farmerID = $farmerID";
        }

        $whereClause = implode(" ", $conditions);
        $query = "SELECT *
                        FROM Field F
                        WHERE NOT EXISTS (
                            SELECT D.diseaseName
                            FROM PlantDisease3 D
                            WHERE NOT EXISTS (
                                SELECT PD4.diseaseID
                                FROM PlantDisease4 PD4 JOIN Crop2 C2 ON PD4.cropID = C2.cropID
                                WHERE C2.fieldID = F.fieldID AND PD4.diseaseName = D.diseaseName
                            )
                        ) AND $whereClause";

        $result = executePlainSQL($query);
        echo "<table>";

        $columnsToDisplay = ['FIELDID', 'ISPLANTED', 'SOILTYPE', 'FIELDSIZE']; // Specify the columns to display

        // Generate table headers for specified columns
        echo "<tr>";
        foreach ($columnsToDisplay as $column_name) {
            echo "<th>" . htmlspecialchars($column_name) . "</th>";
        }
        echo "</tr>";

        // Fetch and display each row of the result for specified columns
        while ($row = oci_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($columnsToDisplay as $column) {
                $display = isset($row[$column]) ? (is_null($row[$column]) ? "N/A" : htmlspecialchars($row[$column])) : "N/A";
                echo "<td>" . $display . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
    }

    ?>

</body>

</html>