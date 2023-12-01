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
    <title>Water Log</title>
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
        <!-- Main content of the page -->
        <div class="flex-container">
            <div class="form-column">
                <p style="margin-bottom: 20px;">Welcome to the Water Log. Here, you can add, update, and delete your water log.</p>
                <h1 style="margin-bottom: 5px;">Water Log</h1>
                <!-- INSERT -->
                <form method="post" action="waterlog.php" onsubmit="return validateWaterLogForm()">
                    <h3>Add New Water Log</h3>
                    <input type="hidden" name="insertWaterLog" value="insertWaterLog">
                    Water Log ID: <input type="number" name="waterLogID" required><br>
                    Field ID: <input type="number" name="fieldID" required><br>
                    Crop ID: <input type="number" name="cropID" required><br>
                    Water Date: <input type="date" name="waterDate" required><br>
                    Water Quantity Used: <input type="number" name="waterQuantityUsed" required><br>
                    Frequency Per Day: <input type="number" name="frequencyPerDay" required><br>
                    <input type="submit" name="insertSubmit" value="Insert Water Log ID">
                    <div id="waterLogInsertMessage" style="color: red;"></div>
                </form>
                <!-- DELETE -->
                <form method="post" action="waterlog.php" onsubmit="return validateWaterLogForm()">
                    <h3>Delete Water Log</h3>
                    <input type="hidden" name="deleteWaterLog" value="deleteWaterLog">
                    Water Log ID: <input type="number" name="waterLogID" required><br>
                    <input type="submit" name="deleteSubmit" value="Delete Water Log">
                    <div id="waterLogDeleteMessage" style="color: red;"></div>
                </form>

                <!-- SELECT -->
                <form id="selectionForm" method="get" action="waterlog.php" onsubmit="return validateWaterLogForm()">
                    <h3>View Water Log</h3>
                    <!-- Attributes Selection -->
                    <p>Select Attributes to View:</p>
                    <input type="checkbox" name="attributesView[]" value="waterLogID"> Water Log ID<br>
                    <input type="checkbox" name="attributesView[]" value="fieldID"> Field ID<br>
                    <input type="checkbox" name="attributesView[]" value="cropID"> Crop ID<br>
                    <input type="checkbox" name="attributesView[]" value="waterDate"> Water Date<br>
                    <input type="checkbox" name="attributesView[]" value="waterQuantityUsed"> Water Quantity Used<br>
                    <input type="checkbox" name="attributesView[]" value="frequencyPerDay"> Frequency Per Day<br> <br>
                    <!-- Filter Conditions -->
                    <div id="conditionFields">
                        <div class="condition-row">
                            <select class="conjunction-select" name="conjunctions[]">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                            <select name="attributes[]" onchange="updateInputType(this)">
                                <option value="fieldID">Field ID</option>
                                <option value="cropID">Crop ID</option>
                                <option value="waterDate">Water Date</option>
                                <option value="waterQuantityUsed">Water Quantity Used</option>
                                <option value="frequencyPerDay">Frequency Per Day</option>
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
                            <input type="text" name="values[]" placeholder="Value">
                        </div>
                    </div>
                    <button type="button" onclick="addCondition()">Add Another Condition</button>
                    <br><br>
                    <input type="submit" name="selectWaterLog" value="View Water Log">
                    <div id="waterLogViewMessage" style="color: red;"></div>
                </form>

                <form method="get" action="waterlog.php" onsubmit="return validateWaterLogForm()">
                    <h3>Enter Average Water Usage for Comparison:</h3>
                    <div id="conditionFields">
                        <div class="condition-row">
                            <select name="operator">
                                <option value="="> = </option>
                                <option value="<">
                                    < </option>
                                <option value=">"> > </option>
                                <option value="<=">
                                    <= </option>
                                <option value=">="> >= </option>
                            </select>
                            <input type="number" name="userAvgWaterUsage" placeholder="Average Water Usage">
                        </div>
                    </div>
                    <input type="submit" name="nestedAgg" value="Find Fields">
                    <div id="waterLogFindMessage" style="color: red;"></div>
                </form>

            </div>


            <div class="table-column">
                <h2>Water Log Overview</h2>
                <div id="table-display">
                    <table>
                        <?php
                        if (connectToDB()) {
                            handleDisplayRequest();
                            disconnectFromDB();
                        }
                        ?>
                    </table>
                    <br>
                </div>

                <div id="table-display">
                    <?php
                    if (isset($_GET['selectWaterLog'])) {
                        echo "<h2>View Filtered Water Log</h2><br>";
                        handleGETRequest();
                    }
                    ?>
                </div>

                <div id="table-display">
                    <?php
                    if (isset($_GET['nestedAgg'])) {
                        echo "<h2>Identified fields with Avg Water Quantity more than " . $_GET['userAvgWaterUsage'] . "</h2><br>";
                        handleGETRequest();
                    }
                    ?>
                </div>
                <br>
                <button onclick="reloadPage()">Reload Table(s)</button>
            </div>

        </div>
    </main>

    <footer>
        <p>&copy; 2023 Farming and Agricultural Resource Management Application</p>
    </footer>

    <script>
        function validateWaterLogForm() {
            var fieldID = document.getElementById("fieldID").value;
            var cropID = document.getElementById("cropID").value;
            var waterDate = document.getElementById("waterDate").value;
            var waterQuantityUsed = document.getElementById("waterQuantityUsed").value;
            var frequencyPerDay = document.getElementById("frequencyPerDay").value;


            if (fieldID === "" || cropID === "" || frequencyPerDay === "" || waterDate === NULL || waterQuantityUsed === NULL) {
                alert("Please fill in all the fields.");
                return false;
            }

            if (fieldID < 0 || cropID < 0 || waterQuantityUsed < 0 || frequencyPerDay < 0) {
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
                    <option value="fieldID">Field ID</option>
                    <option value="cropID">Crop ID</option>
                    <option value="waterDate">Water Date</option>
                    <option value="waterQuantityUsed">Water Quantity Used</option>
                    <option value="frequencyPerDay">Frequency Per Day</option>
                </select>
                <select name="operators[]">
                    <option value="=">=</option>
                    <option value="<"><</option>
                    <option value=">">></option>
                    <option value="<="><=</option>
                    <option value=">=">>=</option>
                </select>
                <input type="text" name="values[]" placeholder="Value">`;


            // Show the conjunction selector for subsequent conditions
            if (container.children.length > 0) {
                newCondition.querySelector(".conjunction-select").style.display = "inline";
            }

            container.appendChild(newCondition);
        }

        <?php if (isset($_SESSION['waterLogDeleteMessage'])) : ?>
            var messageBox = document.getElementById('waterLogDeleteMessage');
            messageBox.textContent = '<?php echo $_SESSION['waterLogDeleteMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['waterLogDeleteMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['waterLogInsertMessage'])) : ?>
            var messageBox = document.getElementById('waterLogInsertMessage');
            messageBox.textContent = '<?php echo $_SESSION['waterLogInsertMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['waterLogInsertMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['waterLogViewMessage'])) : ?>
            var messageBox = document.getElementById('waterLogViewMessage');
            messageBox.textContent = '<?php echo $_SESSION['waterLogViewMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['waterLogViewMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['waterLogFindMessage'])) : ?>
            var messageBox = document.getElementById('waterLogFindMessage');
            messageBox.textContent = '<?php echo $_SESSION['waterLogFindMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['waterLogFindMessage']); // Clear the session variable 
        ?>

        function updateInputType(selectElement) {
            var valueInput = selectElement.parentElement.querySelector('input[type="text"]');
            if (selectElement.value === "waterDate") {
                valueInput.type = "date";
            } else {
                valueInput.type = "text";
            }
        }
    </script>



    <?php
    ////INSERT/////
    function handleInsertWaterLog()
    {
        global $db_conn;

        $water_Log_ID = $_POST['waterLogID'];
        $field_ID = $_POST['fieldID'];
        $crop_ID = $_POST['cropID'];
        $water_Date = $_POST['waterDate'];
        $water_Quantity_Used = $_POST['waterQuantityUsed'];
        $frequency_Per_Day = $_POST['frequencyPerDay'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (checkIfIdExists($db_conn, "WaterLog", "waterLogID", $water_Log_ID)) {
            $_SESSION['waterLogInsertMessage'] = "Water Log ID already exists. Please enter a unique ID.";
            return;
        }

        if (!checkIfIdExists($db_conn, "Field", "fieldID", $field_ID)) {
            $_SESSION['waterLogInsertMessage'] = "Field ID does not exists. Please valid a unique ID.";
            return;
        }

        if (!checkIfIdExists($db_conn, "Crop2", "cropID", $crop_ID)) {
            $_SESSION['waterLogInsertMessage'] = "Crop ID does not exists. Please valid a unique ID.";
            return;
        }

        $tuple = array(
            ":water_Log_ID" => $water_Log_ID,
            ":field_ID" => $field_ID,
            ":crop_ID" => $crop_ID,
            ":water_Date" => $water_Date,
            ":water_Quantity_Used" => $water_Quantity_Used,
            ":frequency_Per_Day" => $frequency_Per_Day,
            ":currentFarmerID" => $currentFarmerID
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("INSERT INTO WaterLog (waterLogID, fieldID, cropID, waterDate, waterQuantityUsed, frequencyPerDay, farmerID) VALUES (:water_Log_ID, :field_ID, :crop_ID, TO_DATE(:water_Date, 'YYYY-MM-DD'), :water_Quantity_Used, :frequency_Per_Day, :currentFarmerID)", $alltuples);

        if ($e = oci_error()) {  // Check for errors in SQL execution
            $_SESSION['waterLogInsertMessage'] = "Error: " . $e['message'];
            oci_rollback($db_conn); // Rollback transaction in case of error
        } else {
            oci_commit($db_conn);  // Commit if no errors
            $_SESSION['waterLogInsertMessage'] = "Water Log successfully inserted.";
        }
    }

    /////DELETE////
    function handleDeleteWaterLog()
    {
        global $db_conn;

        $water_Log_ID = $_POST['waterLogID'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "WaterLog", "waterLogID", $water_Log_ID)) {
            $_SESSION['waterLogDeleteMessage'] = "Error Water Log ID in Water Log does not exist. Please try again";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "WaterLog", "waterLogID", $water_Log_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['waterLogDeleteMessage'] = "You do not have permission to delete this water log record.";
            return;
        }

        executePlainSQL("DELETE FROM WaterLog WHERE waterLogID = " . $water_Log_ID . "");
        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['waterLogDeleteMessage'] = "Water Log successfully deleted.";
        } else {
            $_SESSION['waterLogDeleteMessage'] = "Error Water Log ID does not exist. Please try again.";
        }
    }

    ////SELECTION////
    function handleGetWaterLog()
    {
        global $db_conn;

        $selectedAttributes = isset($_GET['attributesView']) ? $_GET['attributesView'] : array();
        $selectClause = "SELECT ";

        // If no attributes are selected, default to '*'
        if (empty($selectedAttributes)) {
            $selectClause .= "*";
        } else {
            // Concatenate selected attributes for the SELECT clause
            $selectClause .= implode(", ", $selectedAttributes);
        }

        $conditions = [];

        for ($i = 0; $i < count($_GET['attributes']); $i++) {
            $attribute = isset($_GET['attributes'][$i]) ? $_GET['attributes'][$i] : '';
            $operator = isset($_GET['operators'][$i]) ? $_GET['operators'][$i] : '';
            $value = isset($_GET['values'][$i]) ? $_GET['values'][$i] : '';
            $conjunction = ($i > 0) ? isset($_GET['conjunctions'][$i - 1]) ? $_GET['conjunctions'][$i] : '' : "";

            // Determine the format based on the attribute
            if ($attribute == "waterDate" && !empty($value)) {
                $value = "TO_DATE('$value', 'YYYY-MM-DD')";
            } else if ($attribute == "growthDuration" || $attribute == "waterQuantityUsed" || $attribute == "frequencyPerDay") {
                if (is_numeric($value)) {
                    $value = intval($value);
                } else {
                    $_SESSION['harvestSelectMessage'] = "Error: Invalid Crop ID format. Please enter a numeric value.";
                    return;
                }
            } else {
                $value = "'$value'";
            }

            if (!empty($attribute) && !empty($operator) && ($value != '')) {
                $condition = "$conjunction $attribute $operator $value";
                $conditions[] = $condition;
            }
        }

        if (isset($_SESSION['farmerID']) && !empty($_SESSION['farmerID'])) {
            $farmerID = $_SESSION['farmerID'];
            $conditions[] = "AND farmerID = $farmerID";
        }

        $whereClause = implode(" ", $conditions);
        $query = $selectClause . " FROM WaterLog " . (!empty($whereClause) ? "WHERE $whereClause" : "");

        $result = executePlainSQL($query);
        if ($result) {
            $_SESSION['waterLogViewMessage'] = "View Water Log on the right!";
        } else {
            $_SESSION['waterLogViewMessage'] = "Please try again.";
        }
        printResult($result);
    }

    // finds fields where the average water usage is greater than the user input.
    ////Nested aggregation////
    function handleFindFieldsMoreWaterThanAverage()
    {
        global $db_conn;

        if (isset($_SESSION['farmerID'])) {
            $currentFarmerID = $_SESSION['farmerID'];
        } else {
            $_SESSION['waterLogFindMessage'] = "Error: Please log in to use this feature.";
            return;
        }

        // Get the operator and the user's average water usage input
        $operator = isset($_GET['operator']) ? $_GET['operator'] : "="; // Default to '=' if not set
        $userAvgWaterUsage = isset($_GET['userAvgWaterUsage']) ? $_GET['userAvgWaterUsage'] : 0;

        // Construct the query with the user's input
        $query = "
            SELECT F.fieldID, AVG(W.dailyWaterUsage) as averageDailyWaterUsage
            FROM Field F
            JOIN (SELECT fieldID, waterLogID, waterQuantityUsed * frequencyPerDay as dailyWaterUsage 
                  FROM WaterLog) W ON F.fieldID = W.fieldID
            WHERE F.farmerID = $currentFarmerID
            GROUP BY F.fieldID
            HAVING AVG(W.dailyWaterUsage) $operator $userAvgWaterUsage";

        $result = executePlainSQL($query);
        if ($result) {
            $_SESSION['waterLogFindMessage'] = "Fields with average daily water usage displayed on the right.";
        } else {
            $_SESSION['waterLogFindMessage'] = "Please try again.";
        }
        printResult($result);
    }

    function handleDisplayRequest()
    {
        global $db_conn;

        $result = executePlainSQL("SELECT waterLogID, fieldID, cropID, waterDate, waterQuantityUsed, frequencyPerDay FROM WaterLog WHERE farmerID = '" . $_SESSION['farmerID'] . "'");
        printResult($result);
    }

    ?>
</body>

</html>