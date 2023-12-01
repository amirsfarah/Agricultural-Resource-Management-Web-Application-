<!-- Test Oracle file for UBC CPSC304
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  Modified by Jason Hall (23-09-20)
  This file shows the very basics of how to execute PHP commands on Oracle.
  Specifically, it will drop a table, create a table, insert values update
  values, and then query for values
  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up All OCI commands are
  commands to the Oracle libraries. To get the file to work, you must place it
  somewhere where your Apache server can run it, and you must rename it to have
  a ".php" extension. You must also change the username and password on the
  oci_connect below to be your ORACLE username and password
-->

<?php

// The preceding tag tells the web server to parse the following text as PHP
// rather than HTML (the default)

// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Set some parameters

// Database access configuration
$config["dbuser"] = "PUT LOG IN HERE";            // change "cwl" to your own CWL
$config["dbpassword"] = "PUT PASSWORD HERE";    // change to 'a' + your student number
$config["dbserver"] = "PUT SERVER HERE";        // change to 
$db_conn = NULL;    // login credentials are used in connectToDB()

$success = true;    // keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

function debugAlertMessage($message)
{
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        // echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

/////////////////////////////////////////////////////////////////////
// executing simple SQL queries without variables.

function executePlainSQL($cmdstr)
{ //takes a plain (no bound variables) SQL command and executes it
    // echo "<br>running " . $cmdstr . "<br>";
    global $db_conn, $success;

    $statement = oci_parse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        // echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
        // echo htmlentities($e['message']);
        $success = False;
        return false; // Return false on failure
    }

    $r = oci_execute($statement, OCI_DEFAULT);
    if (!$r) {
        // echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For oci_execute errors pass the statementhandle
        // echo htmlentities($e['message']);
        $success = False;
        return false; // Return false on failure
    }

    return $statement;
}

// executing SQL queries with bound variables, which can protect against SQL injection attacks
function executeBoundSQL($cmdstr, $list)
{
    /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

    // echo "<br>running " . $cmdstr . "<br>";
    global $db_conn, $success;
    $statement = oci_parse($db_conn, $cmdstr);

    if (!$statement) {
        // echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        // echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            oci_bind_by_name($statement, $bind, $val);
            unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = oci_execute($statement, OCI_DEFAULT);
        if (!$r) {
            // echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
            // echo htmlentities($e['message']);
            // echo "<br>";
            $success = False;
            return false;
        }
        return true;
    }
}

function checkIfIdExists($db_conn, $table, $idColumnName, $idValue)
{ // checks if there is an existing ID
    $sql = "SELECT * FROM " . htmlspecialchars($table) . " WHERE " . htmlspecialchars($idColumnName) . " = :idValue";
    $stmt = oci_parse($db_conn, $sql);
    oci_bind_by_name($stmt, ":idValue", $idValue);
    oci_execute($stmt);

    return oci_fetch_all($stmt, $res) > 0;
}

function checkIfTwoIdsExistOnSameRow($db_conn, $table, $idColumnName1, $idValue1, $idColumnName2, $idValue2)
{
    // Construct the SQL query with two conditions
    $sql = "SELECT * FROM " . htmlspecialchars($table) .
        " WHERE " . htmlspecialchars($idColumnName1) . " = :idValue1" .
        " AND " . htmlspecialchars($idColumnName2) . " = :idValue2";

    $stmt = oci_parse($db_conn, $sql);

    // Bind the values to the query parameters
    oci_bind_by_name($stmt, ":idValue1", $idValue1);
    oci_bind_by_name($stmt, ":idValue2", $idValue2);

    oci_execute($stmt);

    // Check if any row matches both conditions
    return oci_fetch_all($stmt, $res) > 0;
}

function checkIfDateExists($db_conn, $table, $dateColumnName, $dateValue)
{
    // Format the date as a string in 'YYYY-MM-DD' format
    $formattedDateValue = date('Y-m-d', strtotime($dateValue));

    // Prepare the SQL statement with date format matching
    $sql = "SELECT * FROM " . htmlspecialchars($table) . " WHERE TO_CHAR(" . htmlspecialchars($dateColumnName) . ", 'YYYY-MM-DD') = :dateValue";
    $stmt = oci_parse($db_conn, $sql);

    // Bind the formatted date string to the placeholder
    oci_bind_by_name($stmt, ":dateValue", $formattedDateValue);

    oci_execute($stmt);

    oci_fetch_all($stmt, $res);

    return count($res) > 0;
}


function connectToDB()
{
    global $db_conn;
    global $config;

    // echo "trying Successfully connected to Oracle.\n";
    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    // $db_conn = oci_connect("ora_zoeyuen", "a99569139", "dbhost.students.cs.ubc.ca:1522/stu");
    $db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        debugAlertMessage ("Successfully connected to Oracle.\n");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For oci_connect errors pass no handle
        // echo htmlentities($e['message']);
        // echo "Oracle Connect Error " . $err['message'];
        return false;
    }
}

function disconnectFromDB()
{
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    oci_close($db_conn);
}

function printResult($result)
{
    echo "<table>";

    // Fetch the number of columns in the result
    $ncols = oci_num_fields($result);

    // Generate table headers
    echo "<tr>";
    for ($i = 1; $i <= $ncols; $i++) {
        $column_name = oci_field_name($result, $i);
        echo "<th>" . htmlspecialchars($column_name) . "</th>";
    }
    echo "</tr>";

    // Fetch and display each row of the result
    while ($row = oci_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $item) {
            $display = is_null($item) ? "N/A" : htmlspecialchars($item);
            echo "<td>" . $display . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
}


/////////////////////////////////////////////////////////////////////
// handlers
/////////////////////////////////////////////////////////////////////

function handleResetRequest()
{
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE demoTable");

    // Create new table
    // echo "<br> creating new table <br>";
    executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
    OCICommit($db_conn);
}


/////////////////////////////////////////////////////////////////////
// handle routes
/////////////////////////////////////////////////////////////////////

// HANDLE ALL POST ROUTES
function handlePOSTRequest()
{
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('updateCrop', $_POST)) { // CROP
            handleUpdateCrop();
        } else if (array_key_exists('insertCrop', $_POST)) {
            handleInsertCrop();
        } else if (array_key_exists('deleteCrop', $_POST)) {
            handleDeleteCrop();
        } else if (array_key_exists('insertStorageUnit', $_POST)) { // STORAGEUNIT
            handleInsertStorageUnit();
        } else if (array_key_exists('updateStorageUnit', $_POST)) {
            handleUpdateStorageUnit();
        } else if (array_key_exists('deleteStorageUnit', $_POST)) {
            handleDeleteStorageUnit();
        } else if (array_key_exists('insertHarvestDay', $_POST)) { // HARVEST DAY
            handleInsertHarvestDay();
        } else if (array_key_exists('updateHarvestDay', $_POST)) {
            handleUpdateHarvestDay();
        } else if (array_key_exists('deleteHarvestDay', $_POST)) {
            handleDeleteHarvestDay();
        } else if (array_key_exists('insertTool', $_POST)) { //TOOL
            insertTool();
        } else if (array_key_exists('deleteTool', $_POST)) {
            deleteTool();
        } else if (array_key_exists('updateTool', $_POST)) {
            updateTool();
        } else if (array_key_exists('insertSeed', $_POST)) {
            insertSeed();
        } else if (array_key_exists('deleteSeed', $_POST)) {
            deleteSeed();
        } else if (array_key_exists('updateSeed', $_POST)) {
            updateSeed();
        } else if (array_key_exists('insertWaterLog', $_POST)) { //WATERLOG
            handleInsertWaterLog();
        } else if (array_key_exists('deleteWaterLog', $_POST)) {
            handleDeleteWaterLog();
        } else if (array_key_exists('insertField', $_POST)) { //FIELD
            handleInsertField();
        } else if (array_key_exists('updateField', $_POST)) {
            handleUpdateField();
        } else if (array_key_exists('deleteField', $_POST)) {
            handleDeleteField();
        } else if (array_key_exists('insertDisease', $_POST)) { //DISEASE
            handleInsertDisease();
        } else if (array_key_exists('updateDisease', $_POST)) {
            handleUpdateDisease();
        } else if (array_key_exists('deleteDisease', $_POST)) {
            handleDeleteDisease();
        }

        disconnectFromDB();
    }
}


// HANDLE ALL GET ROUTES
function handleGETRequest()
{
    if (connectToDB()) {
        if (array_key_exists('countTuples', $_GET)) {
            handleCountRequest();
        } else if (array_key_exists('joinSubmit', $_GET)) {
            handleJoinStorageUnit();
        } else if (array_key_exists('joinDiseaseSubmit', $_GET)) {
            handleJoinDiseaseInfo();
        } else if (array_key_exists('aggDiseaseSubmit', $_GET)) {
            handleAggregationWithHaving();
        } else if (array_key_exists('selectHarvest', $_GET)) {
            handleGetHarvestDay();
        } else if (array_key_exists('aggGroupByHarvest', $_GET)) {
            handleViewMonthlyHarvest();
        } else if (array_key_exists('selectWaterLog', $_GET)) {
            handleGetWaterLog();
        } else if (array_key_exists('selectCrop', $_GET)) {
            handleGetCrops();
        } else if (array_key_exists('nestedAgg', $_GET)) {
            handleFindFieldsMoreWaterThanAverage();
        } else if (array_key_exists('divisionField', $_GET)) {
            handleGetAllDiseaseField();
        } else if (array_key_exists('selectDisease', $_GET)) {
            handleSelectDisease();
        }
        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
    handleGETRequest();
    // echo "success get";
}
