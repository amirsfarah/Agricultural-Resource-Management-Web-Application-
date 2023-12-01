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
    <title>Farming Application - [Page Name]</title>
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
        <div class="flex-container">

            <div class="form-column">
                <p style="margin-bottom: 20px;">Welcome to the Crop Management page. Here, you can add, update, and delete crops in your farming application.</p>
                <h1 style="margin-bottom: 5px;">Crops</h1>

                <!-- INSERT -->
                <form method="post" action="crop_management.php" onsubmit="return validateCropForm()">
                    <h3>Add New Crop</h3>
                    <input type="hidden" name="insertCrop" value="insertCrop">
                    Crop ID: <input type="number" name="cropID" required><br>
                    Crop Name: <input type="text" name="cropName" required><br>
                    Growth Duration: <input type="number" name="growthDuration" required><br>
                    Field ID: <input type="number" name="fieldID" required><br>
                    Grow Start Date: <input style="margin-bottom: 20px;" type="date" name="growStartDate" required><br>
                    <input type="submit" name="insertSubmit" value="Add Crop">
                    <div id="cropInsertMessage" style="color: red;"></div>
                </form>

                <!-- UPDATE -->
                <form method="post" action="crop_management.php" onsubmit="return validateUpdateCropForm()">
                    <h3>Update Crop</h3>
                    <input type="hidden" name="updateCrop" value="updateCrop">
                    Crop ID to update: <input type="number" name="cropID" required><br>
                    New Crop Name: <input type="text" name="newCropName"><br>
                    New Growth Duration: <input type="number" name="newGrowthDuration"><br>
                    New Field ID: <input type="number" name="newFieldID"><br>
                    New Grow Start Date: <input style="margin-bottom: 20px;" type="date" name="newGrowStartDate"><br>
                    <input type="submit" name="updateSubmit" value="Update Crop">
                    <div id="cropUpdateMessage" style="color: red;"></div>
                </form>

                <!-- DELETE -->
                <form method="post" action="crop_management.php" onsubmit="return validateCropForm()">
                    <h3>Delete Crop</h3>
                    <input type="hidden" name="deleteCrop" value="deleteCrop">
                    <label for="cropID">Crop ID:</label>
                    <input type="number" id="cropID" name="cropID" required>
                    <input type="submit" name="deleteSubmit" value="Delete Crop">
                    <div id="cropDeleteMessage" style="color: red;"></div>
                </form>

                <!-- SELECT -->
                <form id="selectionForm" method="get" action="crop_management.php" onsubmit="return validateCropForm()">
                    <h3>View Crops</h3>
                    <p>Select Attributes to View:</p>
                    <input type="checkbox" name="attributesView[]" value="cropID"> Crop ID<br>
                    <input type="checkbox" name="attributesView[]" value="cropName"> Crop Name<br>
                    <input type="checkbox" name="attributesView[]" value="growthDuration"> Growth Duration<br>
                    <input type="checkbox" name="attributesView[]" value="fieldID"> Field ID<br>
                    <input type="checkbox" name="attributesView[]" value="growStartDate"> Grow Start Date<br> <br>
                    <!-- Filter Conditions -->
                    <div id="conditionFields">
                        <div class="condition-row">
                            <select class="conjunction-select" name="conjunctions[]">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                            <select name="attributes[]" onchange="updateInputType(this)">
                                <option value="cropID">Crop ID</option>
                                <option value="cropName">Crop Name</option>
                                <option value="growthDuration">Growth Duration</option>
                                <option value="fieldID">Field ID</option>
                                <option value="growStartDate">Grow Start Date</option>
                            </select>
                            <select name="operators[]">
                                <option value="="> = </option>
                                <option value="<"> < </option>
                                <option value=">"> > </option>
                                <option value="<="> <= </option>
                                <option value=">="> >= </option>
                            </select>
                            <input type="text" name="values[]" placeholder="Value">
                        </div>
                    </div>
                    <button type="button" onclick="addCondition()">Add Another Condition</button>
                    <br><br>
                    <input type="submit" name="selectCrop" value="View Crops">
                    <div id="cropSelectMessage" style="color: red;"></div>
                </form>
            </div>


            <div class="table-column">
                <h2>Crops Overview</h2> <br>
                <div id="table-display">
                    <table>
                        <?php
                        if (connectToDB()) {
                            handleDisplayRequest();
                            disconnectFromDB();
                        }
                        ?>
                    </table>
                </div>

                <div id="table-display">
                    <?php
                    if (isset($_GET['selectCrop'])) {
                        echo "<h2>View Filtered Crops</h2><br>";
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
        function validateCropForm() {
            var cropID = document.getElementById("cropID").value;
            var cropName = document.getElementById("cropName").value;
            var growthDuration = document.getElementById("growthDuration").value;
            // var farmerID = document.getElementById("farmerID").value;
            var fieldID = document.getElementById("fieldID").value;
            var growStartDate = document.getElementById("growStartDate").value;

            if (cropID === "" || cropName === "" || growthDuration === "" || fieldID === "" || growStartDate === "") {
                alert("Please fill in all the fields.");
                return false;
            }

            if (cropID < 0 || growthDuration < 0 || fieldID < 0) {
                alert("Please enter a positive number for all fields.");
                return false;
            }

            return true;
        }

        function validateUpdateCropForm() {
            var cropID = document.getElementById("cropID").value;
            var cropName = document.getElementById("cropName").value;
            var growthDuration = document.getElementById("growthDuration").value;
            var farmerID = document.getElementById("farmerID").value;
            var fieldID = document.getElementById("fieldID").value;
            var growStartDate = document.getElementById("growStartDate").value;

            if (cropID === "" && cropName === "" && growthDuration === "" && farmerID === "" && fieldID === "" && growStartDate === "") {
                alert("Please fill in at least one field to update.");
                return false;
            }

            if (cropID < 0 || growthDuration < 0 || farmerID < 0 || fieldID < 0) {
                alert("Please enter a positive number for fields.");
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
                <select name="attributes[]" onchange="updateInputType(this)">
                    <option value="cropID">Crop ID</option>
                    <option value="cropName">Crop Name</option>
                    <option value="growthDuration">Growth Duration</option>
                    <option value="fieldID">Field ID</option>
                    <option value="growStartDate">Grow Start Date</option>
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

        function updateInputType(selectElement) {
            var valueInput = selectElement.parentElement.querySelector('input[type="text"]');
            if (selectElement.value === "growStartDate") {
                valueInput.type = "date";
            } else {
                valueInput.type = "text";
            }
        }


        function reloadPage() {
            location.reload();
        }

        <?php if (isset($_SESSION['cropInsertMessage'])) : ?>
            var messageBox = document.getElementById('cropInsertMessage');
            messageBox.textContent = '<?php echo $_SESSION['cropInsertMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['cropInsertMessage']); ?>

        <?php if (isset($_SESSION['cropUpdateMessage'])) : ?>
            var messageBox = document.getElementById('cropUpdateMessage');
            messageBox.textContent = '<?php echo $_SESSION['cropUpdateMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['cropUpdateMessage']); ?>

        <?php if (isset($_SESSION['cropDeleteMessage'])) : ?>
            var messageBox = document.getElementById('cropDeleteMessage');
            messageBox.textContent = '<?php echo $_SESSION['cropDeleteMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['cropDeleteMessage']); ?>

        <?php if (isset($_SESSION['cropSelectMessage'])) : ?>
            var messageBox = document.getElementById('cropSelectMessage');
            messageBox.textContent = '<?php echo $_SESSION['cropSelectMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['cropSelectMessage']); ?>
    </script>


    <?php
    ////INSERT////
    function handleInsertCrop()
    {
        global $db_conn;
        global $_SESSION;

        $cropID = $_POST['cropID'];
        $cropName = $_POST['cropName'];
        $growthDuration = $_POST['growthDuration'];
        $fieldID = $_POST['fieldID'];
        $growStartDate = $_POST['growStartDate'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (checkIfIdExists($db_conn, "Crop2", "cropID", $cropID)) {
            $_SESSION['cropInsertMessage'] = "Error: Crop ID already exists. Please try again.";
            return;
        }
        if (!checkIfIdExists($db_conn, "Field", "fieldID", $fieldID)) {
            $_SESSION['cropInsertMessage'] = "Error: Field ID does not exist. Please try again.";
            return;
        }
        if (!checkIfTwoIdsExistOnSameRow($db_conn, "field", "fieldID", $fieldID, "farmerID", $currentFarmerID)) {
            $_SESSION['cropInsertMessage'] = "Error: You do not own this field. Please try again.";
            return;
        }

        //check if cropName exists in Crop1
        $result = executePlainSQL("SELECT * FROM Crop1 WHERE cropName = '" . $cropName . "'");
        $row = OCI_Fetch_Array($result, OCI_BOTH);
        if (!$row) {
            echo "Error: The Crop Name you entered does not exist in our database.";
            return;
        }


        if (isset($_SESSION['farmerID'])) {
            $tuple = array(
                ":cropID" => $cropID,
                ":cropName" => $cropName,
                ":growthDuration" => $growthDuration,
                ":farmerID" => $currentFarmerID,
                ":fieldID" => $fieldID,
                ":growStartDate" => $growStartDate
            );

            $alltuples = array(
                $tuple
            );

            executeBoundSQL("INSERT INTO Crop2 VALUES (:cropID, :cropName, :growthDuration, :farmerID, :fieldID, TO_DATE(:growStartDate, 'YYYY-MM-DD'))", $alltuples);
            $result = oci_commit($db_conn);
            if (!$result) {
                $_SESSION['cropInsertMessage'] = "Error: Cannot add crop. Please try again.";
            } else {
                $_SESSION['cropInsertMessage'] = "Crop added successfully.";
            }
        }
    }

    ////UPDATE////
    function handleUpdateCrop()
    {
        global $db_conn;

        $cropID = $_POST['cropID'];
        $cropName = $_POST['newCropName'] ?? null;
        $growthDuration = $_POST['newGrowthDuration'] ?? null;
        $fieldID = $_POST['newFieldID'] ?? null;
        $growStartDate = $_POST['newGrowStartDate'] ?? null;

        if (!checkIfIdExists($db_conn, "Crop2", "cropID", $cropID)) {
            $_SESSION['cropUpdateMessage'] = "Error: Crop ID does not exist. Please try again.";
            return;
        }
        if (!checkIfIdExists($db_conn, "Crop1", "cropName", $cropName) && !empty($cropName)) {
            $_SESSION['cropUpdateMessage'] = "Error: Crop Name does not exist. Please try again.";
            return;
        }
        if (!checkIfIdExists($db_conn, "Field", "fieldID", $fieldID) && !empty($fieldID)) {
            $_SESSION['cropUpdateMessage'] = "Error: Field ID does not exist. Please try again.";
            return;
        }

        if ($cropID < 0) {
            $_SESSION['cropUpdateMessage'] = "Error: Crop ID must be a positive number. Please try again.";
            return;
        }

        if ($growthDuration < 0 && !empty($growthDuration)) {
            $_SESSION['cropUpdateMessage'] = "Error: Growth Duration must be a positive number. Please try again.";
            return;
        }

        $farmerID = $_SESSION['farmerID'];
        if (!checkIfTwoIdsExistOnSameRow($db_conn, "Crop2", "cropID", $cropID, "farmerID", $farmerID)) {
            $_SESSION['cropUpdateMessage'] = "Error: You do not have permission to update this crop. Please try again.";
            return;
        }
        if (!checkIfTwoIdsExistOnSameRow($db_conn, "Field", "fieldID", $fieldID, "farmerID", $farmerID)) {
            $_SESSION['cropUpdateMessage'] = "Error: You do not own this feild. Please try again.";
            return;
        }

        $queryParts = array();

        if (!is_null($cropName) && $cropName !== '') {
            $queryParts[] = "cropName = '" . $cropName . "'";
        }
        if (!is_null($growthDuration) && $growthDuration !== '') {
            $queryParts[] = "growthDuration = '" . $growthDuration . "'";
        }
        if (!is_null($fieldID) && $fieldID !== '') {
            $queryParts[] = "fieldID = '" . $fieldID . "'";
        }
        if (!is_null($growStartDate) && $growStartDate !== '') {
            $queryParts[] = "growStartDate = TO_DATE('" . $growStartDate . "', 'YYYY-MM-DD')";
        }

        // If no fields are set to be updated, return to avoid running an empty UPDATE statement
        if (empty($queryParts)) {
            $_SESSION['cropUpdateMessage'] = "Error: Please fill in at least one field to update.";
            return;
        }

        $query = "UPDATE Crop2 SET " . implode(", ", $queryParts) . " WHERE cropID = '" . $cropID . "'";

        // Execute the query
        executePlainSQL($query);
        $result = oci_commit($db_conn);
        if (!$result) {
            $_SESSION['cropUpdateMessage'] = "Error: Cannot update crop. Please try again.";
        } else {
            $_SESSION['cropUpdateMessage'] = "Crop updated successfully.";
        }
    }

    ////DELETE////
    function handleDeleteCrop()
    {
        global $db_conn;
        $cropID = $_POST['cropID'];
        $farmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "Crop2", "cropID", $cropID)) {
            $_SESSION['cropDeleteMessage'] = "Error: Crop ID does not exist. Please try again.";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "Crop2", "cropID", $cropID, "farmerID", $farmerID)) {
            $_SESSION['cropDeleteMessage'] = "Error: You do not have permission to delete this crop. Please try again.";
            return;
        }

        executePlainSQL("DELETE FROM Crop2 WHERE cropID = '" . $cropID . "'");
        $result = oci_commit($db_conn);
        if (!$result) {
            $_SESSION['cropDeleteMessage'] = "Error: Cannot delete crop. Please try again.";
        } else {
            $_SESSION['cropDeleteMessage'] = "Crop deleted successfully.";
        }
    }

    ////SELECTION////
    function handleGetCrops()
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
            if ($attribute == "growStartDate" && !empty($value)) {
                $value = "TO_DATE('$value', 'YYYY-MM-DD')";
            } else if ($attribute == "cropName" && !empty($value)) {
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
        $query = $selectClause . " FROM Crop2 " . (!empty($whereClause) ? "WHERE $whereClause" : "");

        $result = executePlainSQL($query);
        if ($result) {
            $_SESSION['cropSelectMessage'] = "Crops retrieved successfully. View on the right!";
        } else {
            $_SESSION['cropSelectMessage'] = "Error: Cannot retrieve crops. Please try again.";
        }

        printResult($result);
    }

    ////FUNCTION TO DISPLAY////
    function handleDisplayRequest()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT cropID,cropName,growthDuration,fieldID,growStartDate FROM Crop2 WHERE farmerID = '" . $_SESSION['farmerID'] . "'");
        printResult($result);
    }

    ?>

</body>

</html>