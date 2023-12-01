<?php
    include('dbconnect.php');

    /////////////////////////////////////////////////////////////////////
    // executing simple SQL queries without variables.

    function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
        //echo "<br>running ".$cmdstr."<br>";
        global $db_conn, $success;

        $statement = OCIParse($db_conn, $cmdstr);
        //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
            echo htmlentities($e['message']);
            $success = False;
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            echo htmlentities($e['message']);
            $success = False;
        }

        return $statement;
    }

    
    // executing SQL queries with bound variables, which can protect against SQL injection attacks
    function executeBoundSQL($cmdstr, $list) {
        /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
    In this case you don't need to create the statement several times. Bound variables cause a statement to only be
    parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
    See the sample code below for how this function is used */

        global $db_conn, $success;
        $statement = OCIParse($db_conn, $cmdstr);

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn);
            echo htmlentities($e['message']);
            $success = False;
        }

        foreach ($list as $tuple) {
            foreach ($tuple as $bind => $val) {
                //echo $val;
                //echo "<br>".$bind."<br>";
                OCIBindByName($statement, $bind, $val);
                unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                echo htmlentities($e['message']);
                echo "<br>";
                $success = False;
            }
        }
    }


    /////////////////////////////////////////////////////////////////////
    // handlers
    /////////////////////////////////////////////////////////////////////
    function handleInsertRequests() {
        global $db_conn;
    
        if (isset($_POST['insertCrop'])) {
            // Retrieve form data and call insertCrop
            $cropID = $_POST['cropID'];
            $cropName = $_POST['cropName'];
            $growthDuration = $_POST['growthDuration'];
            $farmerID = $_POST['farmerID'];
            $fieldID = $_POST['fieldID'];
            $diseaseID = $_POST['diseaseID'];
        
            insertCrop($cropID, $cropName, $growthDuration, $farmerID, $fieldID, $diseaseID);
        }
    }
    
    function handleUpdateRequests() {
        global $db_conn;

        if (isset($_POST['updateCrop'])) {
            // Assuming your form has inputs with names like cropName, growthDuration, etc.
            $cropID = $_POST['cropID'];
            $cropName = $_POST['cropName'] ?? null; // if cropName is not set or is null, then $cropName is null.
            $growthDuration = $_POST['growthDuration'] ?? null;
            $farmerID = $_POST['farmerID'] ?? null;
            $fieldID = $_POST['fieldID'] ?? null;
            $diseaseID = $_POST['diseaseID'] ?? null;
    
            updateCrop($cropID, $cropName, $growthDuration, $farmerID, $fieldID, $diseaseID);
        }
    }
    

    function handleDeleteRequests() {
        global $db_conn;

        if (isset($_POST['deleteCrop'])) {
            // Assuming your form has inputs with names like cropName, growthDuration, etc.
            $cropID = $_POST['cropID'];

            deleteCrop($cropID);
        }
    }

    /////////////////////////////////////////////////////////////////////
    // functions
    /////////////////////////////////////////////////////////////////////
    function getCrops($connection) {
        $query = "SELECT * FROM Crop2"; // Adjust the query as needed
        $statement = oci_parse($connection, $query);
        oci_execute($statement);
        
        $crops = array();
        while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
            array_push($crops, $row);
        }
        return $crops;
    }

    // INSERT: cropID, cropName, growthDuration, farmerID, fieldID, diseaseID
    function insertCrop($cropID, $cropName, $growthDuration, $farmerID, $fieldID, $diseaseID, $growStartDate) {
        global $db_conn;

        $tuple = array (
            ":cropID" => $cropID,
            ":cropName" => $cropName,
            ":growthDuration" => $growthDuration,
            ":farmerID" => $farmerID,
            ":fieldID" => $fieldID,
            ":diseaseID" => $diseaseID
            ":growStartDate" => $growStartDate
        );

        $alltuples = array (
            $tuple
        );

        executeBoundSQL("INSERT INTO Crop2 VALUES (:cropID, :cropName, :growthDuration, :farmerID, :fieldID, :diseaseID, TO_DATE(:growStartDate, 'YYYY-MM-DD'))", $alltuples);
        OCICommit($db_conn);
    }

    function updateCrop($cropID, $cropName, $growthDuration, $farmerID, $fieldID, $diseaseID) {
        global $db_conn;

        $queryParts = array();

        // Check and append each field to the query if it's provided
        if ($cropName !== null) {
            $queryParts[] = "cropName = '" . $cropName . "'";
        }
        if ($growthDuration !== null) {
            $queryParts[] = "growthDuration = '" . $growthDuration . "'";
        }
        // if ($farmerID !== null) {
        //     $queryParts[] = "farmerID = '" . $farmerID . "'";
        // } this feels a bit weird
        if ($fieldID !== null) {
            $queryParts[] = "fieldID = '" . $fieldID . "'";
        }
        // if ($diseaseID !== null) {
        //     $queryParts[] = "diseaseID = '" . $diseaseID . "'";
        // } this too
            
        // Combine all parts into a single SQL query
        $query = "UPDATE Crop2 SET " . implode(", ", $queryParts) . " WHERE cropID = '" . $cropID . "'";

        // Execute the query
        executePlainSQL($query);
        OCICommit($db_conn);
    }

    // DELETE
    function deleteCrop($cropID) {
        global $db_conn;
        executePlainSQL("DELETE FROM Crop2 WHERE cropID = '". $cropID. "'");
        OCICommit($db_conn);
    }


    /////////////////////////////////////////////////////////////////////
    // handle routes
    /////////////////////////////////////////////////////////////////////

    // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handlePOSTRequest() {
        if (connectToDB()) {
            // if (array_key_exists('resetTablesRequest', $_POST)) {
            //     handleResetRequest();
            if (array_key_exists('updateCrop', $_POST)) {
                handleUpdateRequest();
            } else if (array_key_exists('insertCrop', $_POST)) {
                handleInsertRequest();
            } else if (array_key_exists('deleteCrop', $_POST)){
                handleDeleteRequests();
            }

            disconnectFromDB();
        }
    }

    // HANDLE ALL GET ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handleGETRequest() {
        if (connectToDB()) {
            if (array_key_exists('countTuples', $_GET)) {
                handleCountRequest();
            }

            disconnectFromDB();
        }
    }

    if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
        handlePOSTRequest();
    } else if (isset($_GET['countTupleRequest'])) {
        handleGETRequest();
    }
    
?>
