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
    <title>Farming Application - Inventory Management</title>
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
        <div class="flex-container">
            <div class="form-column">
                <p style="margin-bottom: 20px;">Welcome to the Inventory Management page. Here, you can add, update, and delete tools and seeds in your farming application.</p>
                <h1 style="margin-bottom: 5px;">Inventory Management</h1>

                <!-- Tool Management Section -->
                <h2>Tool Management</h2>
                <!-- INSERT for Tool -->
                <form method="post" action="inventory.php">
                    <h3>Add New Tool</h3>
                    <input type="hidden" name="insertTool" value="insertTool">
                    Inventory ID: <input type="number" name="inventoryID" required><br>
                    Storage Unit ID: <input type="number" name="storageUnitID" required><br>
                    Condition: <input type="number" name="condition" min="1" max="5" required><br>
                    Tool Name: <input type="text" name="toolName" required><br>
                    <input type="submit" name="insertSubmit" value="Insert Tool">
                    <div id="toolInsertMessage" style="color: red;"></div>
                </form>

                <!-- DELETE for Tool -->
                <form method="post" action="inventory.php">
                    <h3>Delete Tool</h3>
                    <input type="hidden" name="deleteTool" value="deleteTool">
                    Inventory ID: <input type="number" name="inventoryID" required><br>
                    <input type="submit" name="deleteSubmit" value="Delete Tool">
                    <div id="toolDeletionMessage" style="color: red;"></div>
                </form>


                <!-- UPDATE for Tool -->
                <form method="post" action="inventory.php">
                    <h3>Update Tool</h3>
                    <input type="hidden" name="updateTool" value="updateTool">
                    Inventory ID: <input type="number" name="inventoryID" required><br>
                    Storage Unit ID: <input type="number" name="storageUnitID"><br>
                    Condition: <input type="number" name="condition" min="1" max="5"><br>
                    Tool Name: <input type="text" name="toolName"><br>
                    <input type="submit" name="updateSubmit" Insertvalue="Update Tool">
                    <div id="toolUpdateMessage" style="color: red;"></div>
                </form>



                <!-- Seed Management Section -->
                <h2>Seed Management</h2>
                <!-- INSERT for seed -->
                <form method="post" action="inventory.php">
                    <h3>Add New Seed</h3>
                    <input type="hidden" name="insertSeed" value="insertSeed">
                    Inventory ID: <input type="number" name="inventoryID" required><br>
                    Storage Unit ID: <input type="number" name="storageUnitID" required><br>
                    Seed Quantity: <input type="number" name="seedQuantity" required><br>
                    Seed Type: <input type="text" name="seedType" required><br>
                    Purchase Date: <input type="date" name="purchaseDate" required pattern="\d{4}-\d{2}-\d{2}" title="Please enter a valid date (YYYY-MM-DD)"><br>
                    Quantity Purchased: <input type="number" name="quantityPurchased" required><br>
                    <input type="submit" name="insertSubmit" value="Insert Seed">
                    <div id="seedInsertMessage" style="color: red;"></div>
                </form>

                <!-- DELETE for seed -->
                <form method="post" action="inventory.php">
                    <h3>Delete Seed</h3>
                    <input type="hidden" name="deleteSeed" value="deleteSeed">
                    Inventory ID: <input type="number" name="inventoryID" required><br>
                    <input type="submit" name="deleteSubmit" value="Delete Seed">
                    <div id="seedDeleteMessage" style="color: red;"></div>
                </form>

                <!-- UPDATE for seed -->
                <form method="post" action="inventory.php">
                    <h3>Update Seed</h3>
                    <input type="hidden" name="updateSeed" value="updateSeed">
                    Inventory ID: <input type="number" name="inventoryID" required><br>
                    Storage Unit ID: <input type="number" name="storageUnitID"><br>
                    Seed Quantity: <input type="number" name="seedQuantity"><br>
                    Quantity Purchased: <input type="number" name="quantityPurchased"><br>
                    Seed Type: <input type="text" name="seedType"><br>
                    <input type="submit" name="updateSubmit" value="Update Seed">
                    <div id="seedUpdateMessage" style="color: red;"></div>
                </form>




            </div>

            <!-- Display Results Section -->
            <div class="table-column">
                <h2>Tools Overview</h2> <br>
                <div id="table-display">
                    <table>
                        <?php
                        if (connectToDB()) {
                            handleDisplayRequest_tool();
                            disconnectFromDB();
                        }
                        ?>
                    </table>
                </div>
                <br>
                <h2>Seeds Overview</h2> <br>
                <div id="table-display">
                    <table>
                        <?php
                        if (connectToDB()) {
                            handleDisplayRequest_seed();
                            disconnectFromDB();
                        }
                        ?>
                    </table>
                </div>
                <br>
                <button onclick="reloadPage()">Reload Tables</button>
            </div>


        </div>


    



    </main>
    <footer>
        <p>&copy; 2023 Farming and Agricultural Resource Management Application</p>
    </footer>

    <script>
        <?php if (isset($_SESSION['toolDeletionMessage'])) : ?>
            var messageBox = document.getElementById('toolDeletionMessage');
            messageBox.textContent = '<?php echo $_SESSION['toolDeletionMessage']; ?>';
            messageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['toolDeletionMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['toolInsertMessage'])) : ?>
            var insertMessageBox = document.getElementById('toolInsertMessage');
            insertMessageBox.textContent = '<?php echo $_SESSION['toolInsertMessage']; ?>';
            insertMessageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['toolInsertMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['toolUpdateMessage'])) : ?>
            var updateMessageBox = document.getElementById('toolUpdateMessage');
            updateMessageBox.textContent = '<?php echo $_SESSION['toolUpdateMessage']; ?>';
            updateMessageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['toolUpdateMessage']); // Clear the session variable 
        ?>

        <?php if (isset($_SESSION['seedInsertMessage'])) : ?>
            var insertMessageBox = document.getElementById('seedInsertMessage');
            insertMessageBox.textContent = '<?php echo $_SESSION['seedInsertMessage']; ?>';
            insertMessageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['seedInsertMessage']); // Clear the session variable 
        ?>
        <?php if (isset($_SESSION['seedDeleteMessage'])) : ?>
            var insertMessageBox = document.getElementById('seedDeleteMessage');
            insertMessageBox.textContent = '<?php echo $_SESSION['seedDeleteMessage']; ?>';
            insertMessageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['seedDeleteMessage']); // Clear the session variable 
        ?>
        <?php if (isset($_SESSION['seedUpdateMessage'])) : ?>
            var insertMessageBox = document.getElementById('seedUpdateMessage');
            insertMessageBox.textContent = '<?php echo $_SESSION['seedUpdateMessage']; ?>';
            insertMessageBox.style.display = 'block';
        <?php endif; ?>
        <?php unset($_SESSION['seedUpdateMessage']); // Clear the session variable
        ?>
    </script>

    <?php
    function reloadPage()
    {
        location . reload();
    }
    // Table for displaying Tools
    function handleDisplayRequest_tool()
    {
        global $db_conn;
        $farmerID = $_SESSION['farmerID'];
        //filters only the current farmer's tools
        $result = executePlainSQL("SELECT inventoryID, storageUnitID, condition, toolName FROM Tool WHERE farmerID = $farmerID");
        printResult($result);
    }
    // Table for displaying Seeds
    function handleDisplayRequest_seed()
    {
        global $db_conn;
        $farmerID = $_SESSION['farmerID'];
        //filters only the current farmer's seeds 
        $result = executePlainSQL("SELECT inventoryID, storageUnitID, SeedQuantity, seedType, PurchaseDate, expiryDate, quantityPurchased  FROM Seed WHERE farmerID = $farmerID");
        printResult($result);
    }

    // TOOL FUNCTIONS

    function insertTool()
    {
        global $db_conn;
        $inventoryID = $_POST['inventoryID'];
        $storageUnitID = $_POST['storageUnitID'];
        $condition = $_POST['condition'];
        $toolName = $_POST['toolName'];
        $farmerID = $_SESSION['farmerID'];

        // First, check if inventoryID already exists
        $checkSql = "SELECT * FROM Inventory WHERE inventoryID = :bind1";
        $checkStmt = oci_parse($db_conn, $checkSql);
        oci_bind_by_name($checkStmt, ":bind1", $inventoryID);
        oci_execute($checkStmt);
        if (oci_fetch_all($checkStmt, $res) > 0) {
            $_SESSION['toolInsertMessage'] = "Inventory ID already used. Please enter another ID.";
            return;
        }
        // Check if storageUnitID exists and belongs to the current farmer
        if (!checkIfTwoIdsExistOnSameRow($db_conn, "StorageUnits", "storageUnitID", $storageUnitID, "farmerID", $farmerID)) {
            $_SESSION['toolInsertMessage'] = "Error: You do not own this storage unit.";
            return;
        }
        // Insert into Inventory
        $sql = "INSERT INTO Inventory (inventoryID, inventoryType) VALUES (:bind1, 'Tool')";
        $statement = oci_parse($db_conn, $sql);
        oci_bind_by_name($statement, ":bind1", $inventoryID);
        if (!oci_execute($statement)) {
            $_SESSION['toolInsertMessage'] = "Error inserting tool. Please try again.";
            return;
        }

        // Insert into Tool
        $sql = "INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (:bind1, :bind2, :bind3, :bind4, :bind5)";
        $statement = oci_parse($db_conn, $sql);
        oci_bind_by_name($statement, ":bind1", $inventoryID);
        oci_bind_by_name($statement, ":bind2", $storageUnitID);
        oci_bind_by_name($statement, ":bind3", $condition);
        oci_bind_by_name($statement, ":bind4", $toolName);
        oci_bind_by_name($statement, ":bind5", $farmerID);

        if (oci_execute($statement)) {
            $_SESSION['toolInsertMessage'] = "Tool successfully inserted.";
        } else {
            $_SESSION['toolInsertMessage'] = "Error Storage Unit ID does not exist. Please try again.";
        }
    }

    function deleteTool()
    {
        global $db_conn;
        $farmerID = $_SESSION['farmerID'];
        $inventoryID = $_POST['inventoryID'];


        // Check if the inventory ID is associated with a tool
        $checkToolSql = "SELECT * FROM Tool WHERE inventoryID = :bind1";
        $checkToolStmt = oci_parse($db_conn, $checkToolSql);
        oci_bind_by_name($checkToolStmt, ":bind1", $inventoryID);
        oci_execute($checkToolStmt);

        if (oci_fetch_all($checkToolStmt, $res) > 0) {
            // Check if the farmer owns the tool
            if (!checkIfTwoIdsExistOnSameRow($db_conn, "Tool", "inventoryID", $inventoryID, "farmerID", $farmerID)) {
                $_SESSION['toolDeletionMessage'] = "Error: You do not own this item.";
                return;
            }
            // Now delete the tool
            $deleteToolSql = "DELETE FROM Tool WHERE inventoryID = :bind1";
            $deleteToolStmt = oci_parse($db_conn, $deleteToolSql);
            oci_bind_by_name($deleteToolStmt, ":bind1", $inventoryID);
            oci_execute($deleteToolStmt);

            $deleteInventorySql = "DELETE FROM Inventory WHERE inventoryID = :bind1";
            $deleteInventoryStmt = oci_parse($db_conn, $deleteInventorySql);
            oci_bind_by_name($deleteInventoryStmt, ":bind1", $inventoryID);
            oci_execute($deleteInventoryStmt);

            oci_commit($db_conn);
            $_SESSION['toolDeletionMessage'] = "Tool successfully deleted.";
        } else {
            // Check if the ID belongs to a seed
            $checkSeedSql = "SELECT * FROM seed WHERE inventoryID = :bind1";
            $checkSeedStmt = oci_parse($db_conn, $checkSeedSql);
            oci_bind_by_name($checkSeedStmt, ":bind1", $inventoryID);
            oci_execute($checkSeedStmt);

            if (oci_fetch_all($checkSeedStmt, $res) > 0) {
                if (!checkIfTwoIdsExistOnSameRow($db_conn, "Seed", "inventoryID", $inventoryID, "farmerID", $farmerID)) {
                    $_SESSION['toolDeletionMessage'] = "Error: You do not own this item.";
                    return;
                }
                $_SESSION['toolDeletionMessage'] = "Error: The provided ID belongs to a seed, not a tool.";
            } else {
                $_SESSION['toolDeletionMessage'] = "No tool or seed found with the specified ID.";
            }
        }
    }

    function updateTool()
    {
        global $db_conn;
        $inventoryID = $_POST['inventoryID'];
        $toolName = $_POST['toolName'];
        $condition = $_POST['condition'];
        $storageUnitID = $_POST['storageUnitID'];
        $farmerID = $_SESSION['farmerID'];

        // Check if tool exists
        $checkSql = "SELECT * FROM Tool WHERE inventoryID = :bind1";
        $checkStmt = oci_parse($db_conn, $checkSql);
        oci_bind_by_name($checkStmt, ":bind1", $inventoryID);
        oci_execute($checkStmt);

        if (oci_fetch_all($checkStmt, $res) == 0) {
            $_SESSION['toolUpdateMessage'] = "No tool found with the specified ID. Please enter a valid ID.";
            return;
        }
        // Check if the farmer owns the tool
        if (!checkIfTwoIdsExistOnSameRow($db_conn, "Tool", "inventoryID", $inventoryID, "farmerID", $farmerID)) {
            $_SESSION['toolUpdateMessage'] = "Error: You do not own this Item.";
            return;
        }

        $updateParts = [];
        // Check if the user entered a new value for each field and add it to the update statement
        if (!empty($toolName)) {
            $updateParts[] = "toolName = '$toolName'";
        }
        if (!empty($condition)) {
            $updateParts[] = "condition = $condition";
        }
        if (!empty($storageUnitID)) {
            // Check if the storage unit exists and belongs to the current farmer
            if (!checkIfIdExists($db_conn, "StorageUnits", "storageUnitID", $storageUnitID)) {
                $_SESSION['toolUpdateMessage'] = "Error Storage Unit ID does not exist. Please try again.";
                return;
            }
            if (!checkIfTwoIdsExistOnSameRow($db_conn, "StorageUnits", "storageUnitID", $storageUnitID, "farmerID", $farmerID)) {
                $_SESSION['toolUpdateMessage'] = "Error: You do not own this Storage Unit.";
                return;
            }
            $updateParts[] = "storageUnitID = $storageUnitID";
        }


        $updateParts[] = "farmerID = $farmerID";
        // If there are any changes, update the tool
        if (count($updateParts) > 0) {
            $updateSql = "UPDATE Tool SET " . implode(", ", $updateParts) . " WHERE inventoryID = :bind1";
            $updateStmt = oci_parse($db_conn, $updateSql);
            oci_bind_by_name($updateStmt, ":bind1", $inventoryID);
            oci_execute($updateStmt);

            $_SESSION['toolUpdateMessage'] = "Tool updated successfully.";
        } else {
            $_SESSION['toolUpdateMessage'] = "No changes made to the tool.";
        }
    }



    // SEED FUNCTIONS

    function insertSeed()
    {
        global $db_conn;
        $inventoryID = $_POST['inventoryID'];
        $storageUnitID = $_POST['storageUnitID'];
        $seedQuantity = $_POST['seedQuantity'];
        $seedType = $_POST['seedType'];
        $farmerID = $_SESSION['farmerID'];
        $purchaseDate = $_POST['purchaseDate'];
        $quantityPurchased = $_POST['quantityPurchased'];
        $expiryDate = date('Y-m-d', strtotime($purchaseDate . ' + 1 year'));


        // First, check if inventoryID already exists
        if (checkIfIdExists($db_conn, "Inventory", "inventoryID", $_POST['inventoryID']) && checkIfIdExists($db_conn, "Seed", "inventoryID", $_POST['inventoryID'])) {
            $_SESSION['seedInsertMessage'] = "Inventory ID is used by a Exsistant Seed. Please enter a unique ID.";
            return;
        } else if (checkIfIdExists($db_conn, "Inventory", "inventoryID", $_POST['inventoryID']) && checkIfIdExists($db_conn, "Tool", "inventoryID", $_POST['inventoryID'])) {
            $_SESSION['seedInsertMessage'] = "Inventory ID is used by a Exsistant Tool. Please enter a unique ID.";
            return;
        } else if (checkIfIdExists($db_conn, "Inventory", "inventoryID", $_POST['inventoryID'])) {
            $_SESSION['seedInsertMessage'] = "Inventory ID is used by a Exsistant Inventory. Please enter a unique ID.";
            return;
        }
        // Check if storageUnitID exists and belongs to the current farmer
        if (!checkIfIdExists($db_conn, "StorageUnits", "storageUnitID", $storageUnitID)) {
            $_SESSION['seedInsertMessage'] = "Error Storage Unit ID does not exist. Please try again.";
            return;
        }
        // Check if the storage unit exists and belongs to the current farmer
        if (!checkIfTwoIdsExistOnSameRow($db_conn, "StorageUnits", "storageUnitID", $storageUnitID, "farmerID", $farmerID)) {
            $_SESSION['seedInsertMessage'] = "Error: You do not own this Storage Unit.";
            return;
        }

        // Insert into Inventory
        $sqlInventory = "INSERT INTO Inventory (inventoryID, inventoryType) VALUES (:bind1, 'Seed')";
        $statementInventory = oci_parse($db_conn, $sqlInventory);
        oci_bind_by_name($statementInventory, ":bind1", $inventoryID);
        if (!oci_execute($statementInventory)) {
            $_SESSION['seedInsertMessage'] = "Error inserting seed into Inventory. Please try again.";
            return;
        }


        // Insert into Seed
        $sqlSeed = "INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (:bind1, :bind2, :bind3, :bind4, :bind5, TO_DATE(:bind6, 'YYYY-MM-DD'), TO_DATE(:bind7, 'YYYY-MM-DD'), :bind8)";
        $statementSeed = oci_parse($db_conn, $sqlSeed);
        oci_bind_by_name($statementSeed, ":bind1", $inventoryID);
        oci_bind_by_name($statementSeed, ":bind2", $storageUnitID);
        oci_bind_by_name($statementSeed, ":bind3", $seedQuantity);
        oci_bind_by_name($statementSeed, ":bind4", $seedType);
        oci_bind_by_name($statementSeed, ":bind5", $farmerID);
        oci_bind_by_name($statementSeed, ":bind6", $purchaseDate);
        oci_bind_by_name($statementSeed, ":bind7", $expiryDate);
        oci_bind_by_name($statementSeed, ":bind8", $quantityPurchased);


        if (oci_execute($statementSeed)) {
            $_SESSION['seedInsertMessage'] = "Seed successfully inserted.";
        } else {
            $_SESSION['seedInsertMessage'] = "Error inserting seed. Please try again.";
        }
    }


    function deleteSeed()
    {
        global $db_conn;

        $inventoryID = $_POST['inventoryID'];

        // Check if the inventory ID is associated with a tool
        $checkToolSql = "SELECT * FROM Seed WHERE inventoryID = :bind1";
        $checkToolStmt = oci_parse($db_conn, $checkToolSql);
        oci_bind_by_name($checkToolStmt, ":bind1", $inventoryID);
        oci_execute($checkToolStmt);

        if (oci_fetch_all($checkToolStmt, $res) > 0) {
            // If it's a Seed, delete it
            $deleteToolSql = "DELETE FROM Seed WHERE inventoryID = :bind1";
            $deleteToolStmt = oci_parse($db_conn, $deleteToolSql);
            oci_bind_by_name($deleteToolStmt, ":bind1", $inventoryID);
            oci_execute($deleteToolStmt);

            $deleteInventorySql = "DELETE FROM Inventory WHERE inventoryID = :bind1";
            $deleteInventoryStmt = oci_parse($db_conn, $deleteInventorySql);
            oci_bind_by_name($deleteInventoryStmt, ":bind1", $inventoryID);
            oci_execute($deleteInventoryStmt);

            if (oci_commit($db_conn)) {
                $_SESSION['seedDeleteMessage'] = "Seed successfully deleted.";
            } else {
                $_SESSION['seedDeleteMessage'] = "Error: could not delete seed.";
            }
        } else {
            // Check if the ID belongs to a seed
            $checkSeedSql = "SELECT * FROM Tool WHERE inventoryID = :bind1";
            $checkSeedStmt = oci_parse($db_conn, $checkSeedSql);
            oci_bind_by_name($checkSeedStmt, ":bind1", $inventoryID);
            oci_execute($checkSeedStmt);

            if (oci_fetch_all($checkSeedStmt, $res) > 0) {
                $_SESSION['seedDeleteMessage'] = "Error: The provided ID belongs to a tool, not a seed.";
            } else {
                $_SESSION['seedDeleteMessage'] = "No tool or seed found with the specified ID.";
            }
        }
    }

    function updateSeed()
    {
        global $db_conn;
        $farmerID = $_SESSION['farmerID'];
        $inventoryID = $_POST['inventoryID'];
        $seedType = $_POST['seedType'];
        $quantityPurchased = $_POST['quantityPurchased'];
        $seedQuantity = $_POST['seedQuantity'];
        $storageUnitID = $_POST['storageUnitID'];


        // Check if seed exists
        $checkSql = "SELECT * FROM Seed WHERE inventoryID = :bind1";
        $checkStmt = oci_parse($db_conn, $checkSql);
        oci_bind_by_name($checkStmt, ":bind1", $inventoryID);
        oci_execute($checkStmt);

        if (oci_fetch_all($checkStmt, $res) == 0) {
            $_SESSION['seedUpdateMessage'] = "No seed found with the specified ID. Please enter a valid ID.";
            return;
        }


        if (!checkIfTwoIdsExistOnSameRow($db_conn, "Seed", "inventoryID", $inventoryID, "farmerID", $farmerID)) {
            $_SESSION['seedUpdateMessage'] = "Error: You do not own this seed.";
            return;
        }

        $updateParts = [];
        if (!empty($seedType)) {
            $updateParts[] = "seedType = '$seedType'";
        }
        if (!empty($seedQuantity)) {
            $updateParts[] = "seedQuantity = $seedQuantity";
        }
        if (!empty($storageUnitID)) {
            if (!checkIfTwoIdsExistOnSameRow($db_conn, "StorageUnits", "storageUnitID", $storageUnitID, "farmerID", $farmerID)) {
                $_SESSION['seedUpdateMessage'] = "Error: You do not own this Storage Unit.";
                return;
            }
            $updateParts[] = "storageUnitID = $storageUnitID";
        }
        if (!empty($quantityPurchased)) {
            $updateParts[] = "quantityPurchased = $quantityPurchased";
        }

        if (count($updateParts) > 0) {
            $updateSql = "UPDATE Seed SET " . implode(", ", $updateParts) . " WHERE inventoryID = :bind1";
            $updateStmt = oci_parse($db_conn, $updateSql);
            oci_bind_by_name($updateStmt, ":bind1", $inventoryID);
            oci_execute($updateStmt);

            $_SESSION['seedUpdateMessage'] = "Seed updated successfully.";
        } else {
            $_SESSION['seedUpdateMessage'] = "No changes made to the seed.";
        }
    }
    ?>

</body>

</html>