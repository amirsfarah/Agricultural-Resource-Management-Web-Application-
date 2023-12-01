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

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Management</title>
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
        <!-- Space management queries -->

        <div class="flex-container">

            <div class="form-column">
                <p style="margin-bottom: 20px;">Welcome to the Space Management page. Here, you can add, update, and delete crops in your farming application.</p>
                <h1 style="margin-bottom: 5px;">Storage Unit</h1>
                <!-- INSERT -->
                <form method="post" action="space_management.php" onsubmit="return validateStorageUnitForm()">
                    <h3>Add New Storage Unit</h3>
                    <input type="hidden" name="insertStorageUnit" value="insertStorageUnit">
                    Storage Unit ID: <input type="number" name="storageUnitID" required><br>
                    Capacity: <input type="number" name="capacity" required><br>
                    <input type="submit" name="insertSubmit" value="Insert Storage Unit ID">
                    <div id="spaceInsertMessage" style="color: red;"></div>
                </form>
                <!-- UPDATE -->
                <form method="post" action="space_management.php" onsubmit="return validateUpdateStorageUnitForm()">
                    <h3>Update Storage Unit</h3>
                    <input type="hidden" name="updateStorageUnit" value="updateStorageUnit">
                    Storage Unit ID: <input type="number" name="storageUnitID" required><br>
                    Capacity: <input type="number" name="capacity" required><br>
                    <input type="submit" name="updateSubmit" value="Update Storage Unit">
                    <div id="spaceUpdateMessage" style="color: red;"></div>
                </form>
                <!-- DELETE -->
                <form method="post" action="space_management.php" onsubmit="return validateStorageUnitForm()">
                    <h3>Delete Storage Unit</h3>
                    <input type="hidden" name="deleteStorageUnit" value="deleteStorageUnit">
                    Storage Unit ID: <input type="number" name="storageUnitID" required><br>
                    <input type="submit" name="deleteSubmit" value="Delete Storage Unit">
                    <div id="spaceDeleteMessage" style="color: red;"></div>
                </form>
                <!-- JOINING TOOLS,SEEDS, CROPS AND STORAGEUNIT TO FIND ALL ELEMENTS INSIDE A STORAGE UNIT -->
                <form method="get" action="space_management.php" onsubmit="return validateStorageUnitForm()">
                    <h3>View Storage Unit's contents</h3>
                    <!-- <input type="hidden" name="joinStorageUnit" value="joinStorageUnit"> -->
                    Storage Unit ID: <input type="number" name="storageUnitID" required><br>
                    <input type="submit" name="joinSubmit" value="Join Storage Unit">
                    <div id="spaceJoinMessage" style="color: red;"></div>
                </form>
            </div>
            <div class="table-column">
                <h2>Storage Unit Overview</h2>
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
                    <!-- <table> -->
                    <?php
                    if (isset($_GET['joinSubmit'])) {
                        echo "<h2>Contents inside Storage Unit " . ($_GET['storageUnitID']) . "</h2><br>";
                        handleGETRequest();
                    }
                    ?>
                    <!-- </table> -->
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
        function validateStorageUnitForm() {
            var storageUnitID = document.getElementById("storageUnitID").value;
            var capacity = document.getElementById("capacity").value;

            if (storageUnitID === "" || capacity === "") {
                alert("Please fill in all the fields.");
                return false;
            }

            if (capacity < 0) {
                alert("Capacity must be a non-negative number");
                return false;
            }

            if (storageUnitID < 0) {
                alert("Storage Unit ID must be a non-negative number");
                return false;
            }

            return true;
        }

        function reloadPage() {
            location.reload();
        }

        <?php if (isset($_SESSION['spaceInsertMessage'])) : ?>
            var messageBox = document.getElementById('spaceInsertMessage');
            messageBox.textContent = '<?php echo $_SESSION['spaceInsertMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['spaceInsertMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['spaceUpdateMessage'])) : ?>
            var messageBox = document.getElementById('spaceUpdateMessage');
            messageBox.textContent = '<?php echo $_SESSION['spaceUpdateMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['spaceUpdateMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['spaceDeleteMessage'])) : ?>
            var messageBox = document.getElementById('spaceDeleteMessage');
            messageBox.textContent = '<?php echo $_SESSION['spaceDeleteMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['spaceDeleteMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['spaceJoinMessage'])) : ?>
            var messageBox = document.getElementById('spaceJoinMessage');
            messageBox.textContent = '<?php echo $_SESSION['spaceJoinMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['spaceJoinMessage']); // Clear the session variable 
        ?>
    </script>

    <?php
    ////INSERT/////
    function handleInsertStorageUnit()
    {
        global $db_conn;

        $storage_Unit_ID = $_POST['storageUnitID'];
        $capacity2 = $_POST['capacity'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (checkIfIdExists($db_conn, "StorageUnits", "storageUnitID", $storage_Unit_ID)) {
            $_SESSION['spaceInsertMessage'] = "Storage Unit ID already exists. Please enter a unique ID.";
            return;
        }

        $tuple = array(
            ":storage_Unit_ID" => $storage_Unit_ID,
            ":capacity2" => $capacity2,
            ":currentFarmerID" => $currentFarmerID
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (:storage_Unit_ID, :capacity2, :currentFarmerID)", $alltuples);
        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['spaceInsertMessage'] = "Storage Unit successfully inserted.";
        } else {
            $_SESSION['spaceInsertMessage'] = "Error inserting Storage Unit. Please try again.";
        }
    }

    /////UPDATE/////
    function handleUpdateStorageUnit()
    {
        global $db_conn;

        $storage_Unit_ID = $_POST['storageUnitID'];
        $capacity2 = $_POST['capacity'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "StorageUnits", "storageUnitID", $storage_Unit_ID)) {
            $_SESSION['spaceUpdateMessage'] = "No Storage Unit found with the specified ID. Please enter a valid ID.";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "StorageUnits", "storageUnitID", $storage_Unit_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['spaceUpdateMessage'] = "You do not have permission to update this Storage Unit record.";
            return;
        }

        $queryParts = array();

        $queryParts[] = "capacity = '" . $capacity2 . "'";

        // Combine all parts into a single SQL query
        $query = "UPDATE StorageUnits SET " . implode(", ", $queryParts) . " WHERE storageUnitID = '" . $storage_Unit_ID . "'";

        // Execute the query
        executePlainSQL($query);
        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['spaceUpdateMessage'] = "Storage Unit successfully updated.";
        } else {
            $_SESSION['spaceUpdateMessage'] = "Error updating Storage Unit. Please try again.";
        }
    }

    /////DELETE////
    function handleDeleteStorageUnit()
    {
        global $db_conn;

        $storage_Unit_ID = $_POST['storageUnitID'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "StorageUnits", "storageUnitID", $storage_Unit_ID)) {
            $_SESSION['spaceDeleteMessage'] = "No Storage Unit found with the specified ID. Please enter a valid ID.";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "StorageUnits", "storageUnitID", $storage_Unit_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['spaceDeleteMessage'] = "You do not have permission to update this Storage Unit record.";
            return;
        }

        executePlainSQL("DELETE FROM StorageUnits WHERE storageUnitID = '" . $storage_Unit_ID . "'");
        $result = oci_commit($db_conn);
        if ($result) {
            $_SESSION['spaceDeleteMessage'] = "Storage Unit successfully deleted.";
        } else {
            $_SESSION['spaceDeleteMessage'] = "Error deleting Storage Unit. Please try again.";
        }
    }

    ////JOINING WITH TOOL,SEED2, CONTAINS, AND CROP2/////
    function handleJoinStorageUnit()
    {
        global $db_conn;

        // Assuming the storage unit ID is passed from a form input with the name 'storageUnitID'
        $storage_Unit_ID = $_GET['storageUnitID'];
        $currentFarmerID = $_SESSION['farmerID'];

        if (!checkIfIdExists($db_conn, "StorageUnits", "storageUnitID", $storage_Unit_ID)) {
            $_SESSION['spaceJoinMessage'] = "No Storage Unit found with the specified ID. Please enter a valid ID.";
            return;
        }

        if (!checkIfTwoIdsExistOnSameRow($db_conn, "StorageUnits", "storageUnitID", $storage_Unit_ID, "farmerID", $currentFarmerID)) {
            $_SESSION['spaceJoinMessage'] = "You do not have permission to update this Storage Unit record.";
            return;
        }

        // Construct the SQL query string with UNIONs
        $query = "SELECT 'Tool' AS InventoryType, T.inventoryID, T.toolName AS ItemName
        FROM Tool T, StorageUnits S
        WHERE T.storageUnitID = S.storageUnitID
        AND S.storageUnitID = " . $storage_Unit_ID . "
        
        UNION

        SELECT 'Seed' AS InventoryType, Seed.inventoryID, Seed.seedType AS ItemName
        FROM Seed, StorageUnits
        WHERE Seed.storageUnitID = StorageUnits.storageUnitID
        AND StorageUnits.storageUnitID = " . $storage_Unit_ID . "

        UNION

        SELECT 'Harvested Crop' AS InventoryType, Contains.cropID AS inventoryID, Crop2.cropName AS ItemName
        FROM StorageUnits, Contains, Crop2
        WHERE StorageUnits.storageUnitID = Contains.storageUnitID
        AND Crop2.cropID = Contains.cropID
        AND StorageUnits.storageUnitID = " . $storage_Unit_ID;

        $result = executePlainSQL($query);
        if ($result === false) {
            $_SESSION['spaceJoinMessage'] = "There was an error. Please try again.";
        } else {
            $_SESSION['spaceJoinMessage'] = "View the Storage Unit on the right!";
        }
        printResult($result);
    }

    function handleDisplayRequest()
    {
        global $db_conn;
        $result = executePlainSQL("SELECT storageUnitID, capacity FROM StorageUnits WHERE farmerID = '" . $_SESSION['farmerID'] . "'");
        printResult($result);
    }

    ?>
</body>


</html>