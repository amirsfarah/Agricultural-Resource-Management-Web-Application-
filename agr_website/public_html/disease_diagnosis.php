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
        <!-- Main content of the page -->
        <div class="flex-container">
            <div class="form-column">
                <p style="margin-bottom: 20px;">Welcome to the Disease Diagnosis page. Here, you can add, update, and delete plant diseases in your farming application.</p>
                <h1 style="margin-bottom: 5px;">Disease Diagnosis</h1>

                <!-- INSERT -->
                <form method="post" action="disease_diagnosis.php" onsubmit="return validateInsertDiseaseForm()">
                    <h3>Add New Disease Record</h3>
                    <input type="hidden" name="insertDisease" value="insertDisease">
                    Disease ID: <input type="number" name="diseaseID" required><br>
                    Crop ID: <input type="number" name="cropID" required><br>
                    Disease Name: <input type="text" name="diseaseName" required><br>
                    Disease Start Date: <input type="date" name="diseaseStartDate" required><br>
                    Disease End Date:<br> (leave empty if disease ongoing)<input type="date" name="diseaseEndDate"><br>
                    <input type="submit" name="insertSubmit" value="Add Disease Record">
                    <div id="diseaseInsertMessage" style="color: red;"></div>
                </form>

                <!-- UPDATE -->
                <form method="post" action="disease_diagnosis.php" onsubmit="return validateUpdateDiseaseForm()">
                    <h3>Update Disease Record End Date</h3>
                    <input type="hidden" name="updateDisease" value="updateDisease">
                    Disease ID (to update): <input type="number" name="diseaseID" required><br>
                    New Disease End Date: <input type="date" name="diseaseEndDate"><br>
                    <input type="submit" name="updateSubmit" value="Update Disease Record">
                    <div id="diseaseUpdateMessage" style="color: red;"></div>
                </form>

                <!-- DELETE -->
                <form method="post" action="disease_diagnosis.php">
                    <h3>Delete Disease Record</h3>
                    <input type="hidden" name="deleteDisease" value="deleteDisease">
                    Disease ID: <input type="number" name="diseaseID" required><br>
                    <input type="submit" name="deleteSubmit" value="Delete Disease Record">
                    <div id="diseaseDeleteMessage" style="color: red;"></div>
                </form>

                <!-- SELECT -->
                <form id="selectionForm" method="get" action="disease_diagnosis.php" onsubmit="return validateDiseaseForm()">
                    <h3>View Disease Records</h3>
                    <p>Select Attributes to View:</p>
                    <input type="checkbox" name="attributesView[]" value="diseaseID"> Disease ID<br>
                    <input type="checkbox" name="attributesView[]" value="cropID"> Crop ID<br>
                    <input type="checkbox" name="attributesView[]" value="diseaseName"> Disease Name<br>
                    <input type="checkbox" name="attributesView[]" value="diseaseStartDate"> Disease Start Date<br>
                    <input type="checkbox" name="attributesView[]" value="diseaseEndDate"> Disease End Date<br> <br>

                    <!-- Filter Conditions -->
                    <div id="conditionFields">
                        <div class="condition-row">
                            <select class="conjunction-select" name="conjunctions[]">
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                            <select name="attributes[]" onchange="updateInputType(this)">
                                <option value="diseaseID">Disease ID</option>
                                <option value="cropID">Crop ID</option>
                                <option value="diseaseName">Disease Name</option>
                                <option value="diseaseStartDate">Disease Start Date</option>
                                <option value="diseaseEndDate">Disease End Date</option>
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
                    <input type="submit" name="selectDisease" value="View Disease Records">
                    <div id="diseaseSelectMessage" style="color: red;"></div>
                </form>

                <!-- JOINING PLANTDISEASE TABLES TO VIEW DISEASE INFORMATION -->
                <form method="get" action="disease_diagnosis.php">
                    <h3>View Disease Information for a Crop</h3>
                    Crop ID: <input type="number" name="cropID" required><br>
                    <input type="submit" name="joinDiseaseSubmit" value="View Disease Information">
                    <div id="diseaseJoinMessage" style="color: red;"></div>
                </form>

                <!-- FIND FIELDS WITH DISEASES ABOVE A CERTAIN THRESHOLD -->
                <form method="get" action="disease_diagnosis.php">
                    <h3>Find Fields with High Disease Occurrence</h3>
                    <p>Enter a number to specify the minimum count of disease occurrences. The system will identify fields and their soil types where the number of recorded diseases exceeds this threshold.</p>
                    <input type="number" name="diseaseThreshold" placeholder="Minimum Disease Count" required>
                    <input type="submit" name="aggDiseaseSubmit" value="Find Fields">
                    <div id="diseaseAggMessage" style="color: red;"></div>
                </form>



            </div>

            <div class="table-column">

                <h2>Disease Names and Symptoms</h2> <br>
                <div id="table-display">
                    <table>
                        <?php
                        if (connectToDB()) {
                            handleDisplayPlantDisease3();
                            disconnectFromDB();
                        }
                        ?>
                    </table>
                </div>

                <h2>Disease Record Overview</h2> <br>
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

                <br>

                <div id="table-display">
                    <?php
                    if (isset($_GET['selectDisease'])) {
                        echo "<h2>View Filtered Disease Record</h2><br>";
                        handleGETRequest();
                    }
                    ?>
                </div>

                <div>
                    <?php
                    if (isset($_GET['joinDiseaseSubmit'])) {
                        echo "<h2>View Disease Information for a Crop</h2><br>";
                        handleJoinDiseaseInfo();
                    }
                    ?>
                </div>

                <div>
                    <?php
                    if (isset($_GET['aggDiseaseSubmit'])) {
                        echo "<h2>Fields with Disease Count Above Threshold</h2><br>";
                        handleAggregationWithHaving();
                    }
                    ?>
                </div>

                <button onclick="reloadPage()">Reload Table(s)</button>
            </div>
    </main>

    <footer>
        <p>&copy; 2023 Farming and Agricultural Resource Management Application</p>
    </footer>

    <script>
        function validateInsertDiseaseForm() {
            // Perform validation checks for the insert form
            var diseaseID = document.getElementById("diseaseID").value;
            var cropID = document.getElementById("cropID").value;

            if (diseaseID < 0) {
                alert("Disease ID must be a positive integer.");
                return false;
            }

            if (cropID < 0) {
                alert("Crop ID must be a positive integer.");
                return false;
            }

            return true;
        }

        function validateUpdateDiseaseForm() {
            var diseaseID = document.getElementById("diseaseID").value;
            var diseaseEndDate = document.getElementById("diseaseEndDate").value;

            if (diseaseID < 0) {
                alert("Disease ID must be a positive integer.");
                return false;
            }

            if (diseaseEndDate == "" || diseaseEndDate == null) {
                alert("Please enter a disease end date to update.");
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
            if (selectElement.value === "diseaseStartDate" || selectElement.value === "diseaseEndDate") {
                valueInput.type = "date";
            } else {
                valueInput.type = "text";
            }
        }

        function reloadPage() {
            location.reload();
        }

        <?php if (isset($_SESSION['diseaseInsertMessage'])) : ?>
            var messageBox = document.getElementById('diseaseInsertMessage');
            messageBox.textContent = '<?php echo $_SESSION['diseaseInsertMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['diseaseInsertMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['diseaseUpdateMessage'])) : ?>
            var messageBox = document.getElementById('diseaseUpdateMessage');
            messageBox.textContent = '<?php echo $_SESSION['diseaseUpdateMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['diseaseUpdateMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['diseaseDeleteMessage'])) : ?>
            var messageBox = document.getElementById('diseaseDeleteMessage');
            messageBox.textContent = '<?php echo $_SESSION['diseaseDeleteMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['diseaseDeleteMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['diseaseJoinMessage'])) : ?>
            var messageBox = document.getElementById('diseaseJoinMessage');
            messageBox.textContent = '<?php echo $_SESSION['diseaseJoinMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['diseaseJoinMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['diseaseAggMessage'])) : ?>
            var messageBox = document.getElementById('diseaseAggMessage');
            messageBox.textContent = '<?php echo $_SESSION['diseaseAggMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['diseaseAggMessage']); // Clear the session variable 
        ?>
    </script>

    <?php
    ////INSERT////
    function handleInsertDisease()
    {
        global $db_conn;

        $diseaseID = $_POST['diseaseID'];
        $cropID = $_POST['cropID'];
        $diseaseName = $_POST['diseaseName'];
        $diseaseStartDate = $_POST['diseaseStartDate'];
        $diseaseEndDate = $_POST['diseaseEndDate'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (checkIfIdExists($db_conn, "PlantDisease4", "diseaseID", $diseaseID)) {
            $_SESSION['diseaseInsertMessage'] = "Disease ID already exists. Please enter a unique ID.";
            return;
        }

        if (!checkIfIdExists($db_conn, "Crop2", "cropID", $cropID)) {
            $_SESSION['diseaseInsertMessage'] = "Crop ID does not exist. Please enter a valid ID.";
            return;
        }

        if(!checkIfTwoIdsExistOnSameRow($db_conn, "Crop2", "cropID", $cropID, "farmerID", $currentFarmerID)) {
            $_SESSION['diseaseInsertMessage'] = "You do not have permission to insert a disease record for this crop.";
            return;
        }

        $startDateObj = new DateTime($diseaseStartDate);
        $endDateObj = new DateTime($diseaseEndDate);

        // Check if end date is after start date
        if (!empty($diseaseEndDate) && $endDateObj < $startDateObj) {
            $_SESSION['diseaseUpdateMessage'] = "Disease end date cannot be before the disease start date. Please enter a valid date.";
            return;
        }

        if (isset($_SESSION['farmerID'])) {
            $tuple = array(
                ":diseaseID" => $diseaseID,
                ":cropID" => $cropID,
                ":diseaseName" => $diseaseName,
                ":diseaseStartDate" => $diseaseStartDate,
                ":diseaseEndDate" => $diseaseEndDate,
                ":farmerID" => $currentFarmerID
            );

            $alltuples = array($tuple);

            executeBoundSQL("INSERT INTO PlantDisease4 VALUES (:diseaseID, :cropID, :diseaseName, TO_DATE(:diseaseStartDate, 'YYYY-MM-DD'), TO_DATE(:diseaseEndDate, 'YYYY-MM-DD'), :farmerID)", $alltuples);
            $result = oci_commit($db_conn);
            if ($result) {
                $_SESSION['diseaseInsertMessage'] = "Disease record successfully inserted.";
            } else {
                $_SESSION['diseaseInsertMessage'] = "Error inserting disease record. Please try again.";
            }
        } else {
            $_SESSION['diseaseInsertMessage'] = "Error inserting disease record. Please try again.";
        }
    }

    ////UPDATE////
    function handleUpdateDisease()
    {
        global $db_conn;

        $diseaseID = $_POST['diseaseID'];
        $diseaseEndDate = $_POST['diseaseEndDate'];

        if (!checkIfIdExists($db_conn, "PlantDisease4", "diseaseID", $diseaseID)) {
            $_SESSION['diseaseUpdateMessage'] = "Disease ID does not exist. Please enter a valid ID.";
            return;
        }

        $farmerID = $_SESSION['farmerID'];

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "PlantDisease4", "diseaseID", $diseaseID, "farmerID", $farmerID)) {
            $_SESSION['diseaseUpdateMessage'] = "You do not have permission to update this disease record.";
            return;
        }

        //check if enddate is after start date else return error
        $query = "SELECT diseaseStartDate FROM PlantDisease4 WHERE diseaseID = :diseaseID";
        $statement = oci_parse($db_conn, $query);
        oci_bind_by_name($statement, ":diseaseID", $diseaseID);
        oci_execute($statement);
        $row = oci_fetch_array($statement, OCI_BOTH);
        $diseaseStartDate = $row[0];
        $startDateObj = new DateTime($diseaseStartDate);
        $endDateObj = new DateTime($diseaseEndDate);

        // Check if end date is after start date
        if (!empty($diseaseEndDate) && $endDateObj < $startDateObj) {
            $_SESSION['diseaseUpdateMessage'] = "Disease end date cannot be before the disease start date. Please enter a valid date.";
            return;
        }

        if (!empty($diseaseEndDate)) {
            $query = "UPDATE PlantDisease4 SET diseaseEndDate = TO_DATE(:diseaseEndDate, 'YYYY-MM-DD') WHERE diseaseID = :diseaseID";

            $tuple = array(
                ":diseaseID" => $diseaseID,
                ":diseaseEndDate" => $diseaseEndDate
            );

            executeBoundSQL($query, array($tuple));
            $result = oci_commit($db_conn);
            if ($result) {
                $_SESSION['diseaseUpdateMessage'] = "Disease record successfully updated.";
            } else {
                $_SESSION['diseaseUpdateMessage'] = "Error updating disease record. Please try again.";
            }
        } else {
            echo "Disease end date is empty, update aborted.";
        }
    }

    ////DELETE////
    function handleDeleteDisease()
    {
        global $db_conn;

        $diseaseID = $_POST['diseaseID'];
        if (!checkIfIdExists($db_conn, "PlantDisease4", "diseaseID", $diseaseID)) {
            $_SESSION['diseaseDeleteMessage'] = "Disease ID does not exist. Please enter a valid ID.";
            return;
        }

        $farmerID = $_SESSION['farmerID'];
        if (!checkIfTwoIdsExistOnSameRow($db_conn, "PlantDisease4", "diseaseID", $diseaseID, "farmerID", $farmerID)) {
            $_SESSION['diseaseDeleteMessage'] = "You do not have permission to delete this disease record.";
            return;
        }

        executePlainSQL("DELETE FROM PlantDisease4 WHERE diseaseID = '$diseaseID'");
        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['diseaseDeleteMessage'] = "Disease record successfully deleted.";
        } else {
            $_SESSION['diseaseDeleteMessage'] = "Error deleting disease record. Please try again.";
        }
    }

    ////SELECT////
    function handleSelectDisease()
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

        $farmerID = $_SESSION['farmerID'];

        $conditions = [];

        for ($i = 0; $i < count($_GET['attributes']); $i++) {
            $attribute = isset($_GET['attributes'][$i]) ? $_GET['attributes'][$i] : '';
            $operator = isset($_GET['operators'][$i]) ? $_GET['operators'][$i] : '';
            $value = isset($_GET['values'][$i]) ? $_GET['values'][$i] : '';
            $conjunction = ($i > 0) ? isset($_GET['conjunctions'][$i - 1]) ? $_GET['conjunctions'][$i] : '' : "";

            // Handle date formatting for diseaseStartDate and diseaseEndDate
            if (($attribute == "diseaseStartDate" || $attribute == "diseaseEndDate") && !empty($value)) {
                $value = "TO_DATE('$value', 'YYYY-MM-DD')";
            }

            if ($attribute == "diseaseName" && !empty($value)) {
                $value = "'$value'";
            }

            if (!empty($attribute) && !empty($operator) && ($value != '')) {
                $condition = "$conjunction $attribute $operator $value";
                $conditions[] = $condition;
            }
        }

        $whereClause = implode(" ", $conditions);
        $query = $selectClause . " FROM PlantDisease4 WHERE farmerID = '$farmerID'" . (!empty($whereClause) ? " AND $whereClause" : "");

        $result = executePlainSQL($query);
        if ($result === false) {
            $_SESSION['diseaseSelectMessage'] = "Error retrieving disease records. Please try again.";
        } else {
            $_SESSION['diseaseSelectMessage'] = "Disease records successfully retrieved. View it on the right!";
        }
        printResult($result);
    }

    ////JOIN////
    function handleJoinDiseaseInfo()
    {
        global $db_conn;

        if (connectToDB()) {
            $cropID = $_GET['cropID'];  // Get the user-entered cropID
            if (!checkIfIdExists($db_conn, "Crop2", "cropID", $cropID)) {
                $_SESSION['diseaseJoinMessage'] = "Crop ID does not exist. Please enter a valid ID.";
                return;
            }

            $query = "
                SELECT 
                    PlantDisease4.diseaseID,
                    PlantDisease3.diseaseName,
                    PlantDisease1.symptoms,
                    PlantDisease1.treatment,
                    PlantDisease4.diseaseStartDate,
                    PlantDisease4.diseaseEndDate
                FROM 
                    PlantDisease4, PlantDisease3, PlantDisease1
                WHERE 
                    PlantDisease4.cropID = " . $cropID . " AND
                    PlantDisease4.diseaseName = PlantDisease3.diseaseName AND
                    PlantDisease3.symptoms = PlantDisease1.symptoms AND
                    PlantDisease4.farmerID = " . $_SESSION['farmerID'] . "
            ";

            $result = executePlainSQL($query);
            if ($result === false) {
                $_SESSION['diseaseJoinMessage'] = "Error retrieving disease information. Please try again.";
            } else {
                $_SESSION['diseaseJoinMessage'] = "Disease information successfully retrieved. View it on the right!";
            }
            printResult($result);
            disconnectFromDB();
        }
    }

    ////AGGREGATION////
    function handleAggregationWithHaving()
    {
        global $db_conn;

        $diseaseThreshold = $_GET['diseaseThreshold'];
        if ($diseaseThreshold < 0) {
            $_SESSION['diseaseAggMessage'] = "Disease threshold must be a positive integer.";
            return;
        }

        if (connectToDB()) {
            $query = "SELECT 
                        Field.fieldID,
                        Field.soilType,
                        COUNT(PlantDisease4.diseaseID) AS DiseaseCount
                      FROM 
                        Field, Crop2, PlantDisease4
                      WHERE 
                        Crop2.fieldID = Field.fieldID AND
                        Crop2.cropID = PlantDisease4.cropID AND
                        PlantDisease4.farmerID = " . $_SESSION['farmerID'] . "
                      GROUP BY 
                        Field.fieldID, Field.soilType
                      HAVING 
                        COUNT(PlantDisease4.diseaseID) > :diseaseThreshold";

            $statement = oci_parse($db_conn, $query);
            oci_bind_by_name($statement, ":diseaseThreshold", $diseaseThreshold);

            $result = oci_execute($statement);
            if ($result === false) {
                $_SESSION['diseaseAggMessage'] = "Error retrieving disease information. Please try again.";
            } else {
                $_SESSION['diseaseAggMessage'] = "Disease information successfully retrieved. View it on the right!";
            }

            printResult($statement);
            disconnectFromDB();
        }
    }

    function handleDisplayRequest()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT diseaseID,cropID,diseaseName,diseaseStartDate,diseaseEndDate FROM PlantDisease4 WHERE farmerID = " . $_SESSION['farmerID']);
        printResult($result);
    }

    function handleDisplayPlantDisease3()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT diseaseName, symptoms FROM PlantDisease3");
        printResult($result);
    }

    ?>

</body>

</html>