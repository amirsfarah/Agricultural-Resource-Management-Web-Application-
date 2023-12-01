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
    <title>Harvest Day</title>
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
                <p style="margin-bottom: 20px;">Welcome to the Harvest Log. Here, you can add, update, delete and select harvest data into your farming application.</p>
                <h1 style="margin-bottom: 5px;">Harvest Log</h1>
                <!-- INSERT AND ADD TO STORAGE UNIT -->
                <form method="post" action="harvestday.php" onsubmit="return validateHarvestForm()">
                    <h3>Add New Harvest to Storage Unit</h3>
                    <input type="hidden" name="insertHarvestDay" value="insertHarvestDay">
                    Crop ID: <input type="number" name="cropID" required><br>
                    Storage Unit ID: <input type="number" name="storageUnitID" required><br>
                    Harvest Date: <input type="date" name="harvestDate" required><br>
                    Harvest Weight: <input type="number" name="harvestWeight" required><br>
                    <input type="submit" name="insertSubmit" value="insert Harvest for Today">
                    <div id="harvestInsertMessage" style="color: red;"></div>
                </form>
                <!-- UPDATE -->
                <form method="post" action="harvestday.php" onsubmit="return validateHarvestForm()">
                    <h3>Update Harvest</h3>
                    <input type="hidden" name="updateHarvestDay" value="updateHarvestDay">
                    Crop ID: <input type="number" name="cropID" required><br>
                    Harvest Weight: <input type="number" name="harvestWeight"><br>
                    <input type="submit" name="updateSubmit" value="Update Harvest">
                    <div id="harvestUpdateMessage" style="color: red;"></div>
                </form>
                <!-- DELETE -->
                <form method="post" action="harvestday.php" onsubmit="return validateHarvestForm()">
                    <h3>Delete Harvest</h3>
                    <input type="hidden" name="deleteHarvestDay" value="deleteHarvestDay">
                    Crop ID: <input type="number" name="cropID" required><br>
                    <input type="submit" name="deleteSubmit" value="Delete Harvest">
                    <div id="harvestDeleteMessage" style="color: red;"></div>
                </form>
                <!-- SELECT -->
                <form id="selectionForm" method="get" action="harvestday.php" onsubmit="return validateHarvestForm()">
                    <h3>View Harvest</h3>
                    <!-- <input type="hidden" name="selectHarvestDay" value="selectHarvestDay"> -->
                    <!-- Attributes Selection -->
                    <p>Filter Your Conditions</p>
                    <!-- Filter Conditions -->
                    <div id="conditionFields">
                        <div class="condition-row">
                            <select class="conjunction-select" name="conjunctions[]">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                            <select name="attributes[]" onchange="updateInputType(this)">
                                <option value="cropID">Crop ID</option>
                                <option value="harvestDate">Harvest Date</option>
                                <option value="harvestWeight">Harvest Weight</option>
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
                    <br>
                    <input type="submit" name="selectHarvest" value="View Harvest">
                    <div id="harvestSelectMessage" style="color: red;"></div>
                </form>

                <!-- VIEW MONTHLY HARVEST -->
                <form method="get" action="harvestday.php" onsubmit="return validateHarvestForm()">
                    <h3>View Total Monthly Harvest</h3>
                    <!-- <input type="hidden" name="joinStorageUnit" value="joinStorageUnit"> -->
                    Select Month and Year:
                    <input type="month" id="monthYear" name="monthyear" required> <br><br>
                    <input type="submit" name="aggGroupByHarvest" value="View Harvest">
                    <div id="harvestMonthMessage" style="color: red;"></div>
                </form>
            </div>

            <div class="table-column">
                <h2>Total Harvest Overview</h2> <br>
                <div id="table-display">
                    <table>
                        <?php
                        if (connectToDB()) {
                            handleDisplayRequestHarvestDay();
                            disconnectFromDB();
                        }
                        ?>
                    </table>
                    <br>
                </div>
                <div id="table-display">
                    <?php
                    if (isset($_GET['selectHarvest'])) {
                        echo "<h2>Filtered Harvest</h2><br>";
                        handleGETRequest();
                    }
                    ?>
                </div>
                <div id="table-display">
                    <?php
                    if (isset($_GET['aggGroupByHarvest'])) {
                        $monthYear = $_GET['monthyear'];
                        $year = substr($monthYear, 0, 4); // Extracts the year
                        $month = substr($monthYear, 5, 2); // Extracts the month
                        echo "<h2>" . $month . "/" . $year . " Harvest Overview</h2><br>";
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
        function validateHarvestForm() {
            var cropID = document.getElementById("cropID").value;
            var harvestDate = document.getElementById("harvestDate").value;
            var harvestWeight = document.getElementById("harvestWeight").value;

            if (cropID === "" || harvestDate === "" || harvestWeight === "") {
                alert("Please fill in all the fields.");
                return false;
            }

            if (harvestWeight < 0 || cropID < 0) {
                alert("Must be a non-negative number");
                return false;
            }

            return true;
        }

        function addCondition() {
            var container = document.getElementById("conditionFields");
            var newCondition = document.createElement("div");
            newCondition.className = "condition-row";
            newCondition.innerHTML = `
                <select class="conjunction-select" name="conjunctions[]">
                    <option value="AND">AND</option>
                    <option value="OR">OR</option>
                </select>
                <select name="attributes[]">
                    <option value="cropID">Crop ID</option>
                    <option value="harvestDate">Harvest Date</option>
                    <option value="harvestWeight">Harvest Weight</option>
                </select>
                <select name="operators[]">
                        <option value="="> = </option>
                        <option value="<"> < </option>
                        <option value=">"> > </option>
                        <option value="<="> <= </option>
                        <option value=">="> >= </option>
                </select>
                <input type="text" name="values[]" placeholder="Value">`;


            // Show the conjunction selector for subsequent conditions
            if (container.children.length > 0) {
                newCondition.querySelector(".conjunction-select").style.display = "inline";
            }

            container.appendChild(newCondition);
        }

        function updateInputType(selectElement) {
            var valueInput = selectElement.parentElement.querySelector('input[type="text"]');
            if (selectElement.value === "harvestDate") {
                valueInput.type = "date";
            } else {
                valueInput.type = "text";
            }
        }

        <?php if (isset($_SESSION['harvestDeleteMessage'])) : ?>
            var messageBox = document.getElementById('harvestDeleteMessage');
            messageBox.textContent = '<?php echo $_SESSION['harvestDeleteMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['harvestDeleteMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['harvestInsertMessage'])) : ?>
            var messageBox = document.getElementById('harvestInsertMessage');
            messageBox.textContent = '<?php echo $_SESSION['harvestInsertMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['harvestInsertMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['harvestUpdateMessage'])) : ?>
            var messageBox = document.getElementById('harvestUpdateMessage');
            messageBox.textContent = '<?php echo $_SESSION['harvestUpdateMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['harvestUpdateMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['harvestSelectMessage'])) : ?>
            var messageBox = document.getElementById('harvestSelectMessage');
            messageBox.textContent = '<?php echo $_SESSION['harvestSelectMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['harvestSelectMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['harvestMonthMessage'])) : ?>
            var messageBox = document.getElementById('harvestMonthMessage');
            messageBox.textContent = '<?php echo $_SESSION['harvestMonthMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['harvestMonthMessage']); // Clear the session variable 
        ?>
    </script>
    <?php

    // VIEW HARVEST AND WHERE IT IS STORED
    function handleDisplayRequestHarvestDay()
    {
        global $db_conn;
        $currentFarmerID = $_SESSION['farmerID'];

        $result = executePlainSQL("SELECT HarvestDay.cropID, HarvestDay.harvestDate, HarvestDay.harvestWeight, Contains.storageUnitID
        FROM HarvestDay
        INNER JOIN Contains ON HarvestDay.cropID = Contains.cropID 
        WHERE HarvestDay.farmerID = $currentFarmerID");

        printResult($result);
    }

    /////INSERT
    function handleInsertHarvestDay()
    {
        global $db_conn;

        $crop_ID = $_POST['cropID'];
        $storage_Unit_ID = $_POST['storageUnitID'];
        $harvest_Date = $_POST['harvestDate'];
        $harvest_Weight = $_POST['harvestWeight'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "Crop2", "cropID", $crop_ID)) {
            $_SESSION['harvestInsertMessage'] = "Error Crop ID does not exist. Please try again";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "Crop2", "cropID", $crop_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['harvestInsertMessage'] = "You do not have permission to update this Harvest record.";
            return;
        }

        if (!checkIfIdExists($db_conn, "StorageUnits", "storageUnitID", $storage_Unit_ID)) {
            $_SESSION['harvestInsertMessage'] = "Error Storage Unit ID does not exist. Please try again";
            return;
        }
        if (!checkIfTwoIdsExistOnSameRow($db_conn, "StorageUnits", "storageUnitID", $storage_Unit_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['harvestInsertMessage'] = "You do not own this Storage Unit ID. Please try again.";
            return;
        }

        $tuple = array(
            ":crop_ID" => $crop_ID,
            ":harvest_Date" => $harvest_Date,
            ":harvest_Weight" => $harvest_Weight,
            ":currentFarmerID" => $currentFarmerID
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("INSERT INTO HarvestDay (cropID, harvestDate, harvestWeight, farmerID) VALUES (:crop_ID, TO_DATE(:harvest_Date, 'YYYY-MM-DD'), :harvest_Weight, :currentFarmerID)", $alltuples);
        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['harvestInsertMessage'] = "Harvest successfully inserted.";
        } else {
            $_SESSION['harvestInsertMessage'] = "Error Crop ID does not exist. Please try again.";
        }

        $query = "INSERT INTO Contains (storageUnitID, cropID) VALUES (" . $storage_Unit_ID . "," . $crop_ID . ")";

        // Execute the query
        executePlainSQL($query);
        $result2 = oci_commit($db_conn);
        if ($result) {
            $_SESSION['harvestInsertMessage'] = "Contains successfully inserted.";
        } else {
            $_SESSION['harvestInsertMessage'] = "Please try again.";
        }
    }

    ////UPDATE/////
    function handleUpdateHarvestDay()
    {
        global $db_conn;

        $crop_ID = $_POST['cropID'];
        $harvest_Weight = $_POST['harvestWeight'] ?? null;
        $currentFarmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "HarvestDay", "cropID", $crop_ID)) {
            $_SESSION['harvestUpdateMessage'] = "Error Crop ID in Harvest does not exist. Please try again";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "HarvestDay", "cropID", $crop_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['harvestUpdateMessage'] = "You do not have permission to update this Harvest record.";
            return;
        }

        $queryParts = array();

        if (!is_null($harvest_Weight) && $harvest_Weight !== '') {
            $queryParts[] = "harvestWeight = '" . $harvest_Weight . "'";
        }

        if (empty($queryParts)) {
            echo "No fields to update.";
            return;
        }

        // Combine all parts into a single SQL query
        $query = "UPDATE HarvestDay SET " . implode(", ", $queryParts) . " WHERE cropID = '" . $crop_ID . "'";

        // Execute the query
        executePlainSQL($query);
        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['harvestUpdateMessage'] = "Harvest successfully updated.";
        } else {
            $_SESSION['harvestUpdateMessage'] = "Error Crop ID does not exist. Please try again.";
        }
    }

    ////DELETE////
    function handleDeleteHarvestDay()
    {
        global $db_conn;

        $crop_ID = $_POST['cropID'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "HarvestDay", "cropID", $crop_ID)) {
            $_SESSION['harvestDeleteMessage'] = "Error Crop ID in Harvest does not exist. Please try again.";
            return;
        }

        if (!checkIfIdExists($db_conn, "Contains", "cropID", $crop_ID)) {
            $_SESSION['harvestDeleteMessage'] = "Error Crop ID in Storage Units does not exist. Please try again.";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "HarvestDay", "cropID", $crop_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['harvestDeleteMessage'] = "You do not have permission to update this Harvest record.";
            return;
        }

        executePlainSQL("DELETE FROM HarvestDay WHERE cropID = '" . $crop_ID . "'");

        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['harvestDeleteMessage'] = "Harvest successfully deleted.";
        } else {
            $_SESSION['harvestDeleteMessage'] = "Error Crop ID does not exist. Please try again.";
        }

        executePlainSQL("DELETE FROM Contains WHERE cropID = '" . $crop_ID . "'");

        $result2 = oci_commit($db_conn);
        if ($result2) {
            $_SESSION['harvestDeleteMessage'] = "Crops inside Storage Unit successfully deleted.";
        } else {
            $_SESSION['harvestDeleteMessage'] = "Error Crop ID does not exist. Please try again.";
        }
    }

    ////SELECTION////
    function handleGetHarvestDay()
    {
        global $db_conn;

        $conditions = [];

        for ($i = 0; $i < count($_GET['attributes']); $i++) {
            $attribute = isset($_GET['attributes'][$i]) ? $_GET['attributes'][$i] : '';
            $operator = isset($_GET['operators'][$i]) ? $_GET['operators'][$i] : '';
            $value = isset($_GET['values'][$i]) ? $_GET['values'][$i] : '';
            $conjunction = ($i > 0) ? isset($_GET['conjunctions'][$i - 1]) ? $_GET['conjunctions'][$i] : '' : "";

            // Determine the format based on the attribute
            if ($attribute == "harvestDate") {
                // Assuming the date is in 'YYYY-MM-DD' format
                $value = "TO_DATE('$value', 'YYYY-MM-DD')";
            } elseif ($attribute == "cropID") {
                if (is_numeric($value)) {
                    $value = intval($value);
                    if (!checkIfIdExists($db_conn, "HarvestDay", "cropID", $value)) {
                        $_SESSION['harvestSelectMessage'] = "Error: Crop ID does not exist. Please try again";
                        return;
                    }
                } else {
                    $_SESSION['harvestSelectMessage'] = "Error: Invalid Crop ID format. Please enter a numeric value.";
                    return;
                }
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
        $query = "SELECT * FROM HarvestDay WHERE $whereClause";

        $result = executePlainSQL($query);
        if ($result) {
            $_SESSION['harvestSelectMessage'] = "View Harvest on the right!";
        } else {
            $_SESSION['harvestSelectMessage'] = "Please try again.";
        }
        printResult($result);
    }

    //// GROUP BY
    function handleViewMonthlyHarvest()
    {
        global $db_conn;

        $monthYear = $_GET['monthyear'];
        $year = substr($monthYear, 0, 4); // Extracts the year
        $month = substr($monthYear, 5, 2); // Extracts the month

        // Construct the SQL query using the extracted year and month
        $query = "
                SELECT 
                    Crop1.cropName,
                    SUM(HarvestDay.harvestWeight) AS TotalHarvestWeight
                FROM 
                    Crop2, Crop1, HarvestDay
                WHERE 
                    Crop2.cropName = Crop1.cropName AND
                    Crop2.cropID = HarvestDay.cropID AND
                    Crop2.farmerID = " . $_SESSION['farmerID'] . " AND
                    EXTRACT(MONTH FROM HarvestDay.harvestDate) = " . $month . "AND
                    EXTRACT(YEAR FROM HarvestDay.harvestDate) = " . $year . "
                GROUP BY 
                    Crop1.cropName
                ORDER BY 
                    Crop1.cropName
            ";

        // Execute the query
        $result = executePlainSQL($query);
        if ($result) {
            $_SESSION['harvestMonthMessage'] = "View Monthly Harvest on the right!";
        } else {
            $_SESSION['harvestMonthMessage'] = "Please try again.";
        }
        printResult($result);
    }

    ?>

</body>
<footer>
    <p>&copy; 2023 Farming and Agricultural Resource Management Application</p>
</footer>

</html>