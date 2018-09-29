<?php
	session_start();
	date_default_timezone_set('Asia/Manila');
//-----------------------CORS CONFIGURATION-------------------------
	if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:*");

        exit(0);
    }
//-----------------------DB CONFIGURATION-------------------------

	$db_host = "localhost";
	$db_username = "root";
	$db_password = "";
	$db_name = "oversight_prd";

	$con = mysqli_connect($db_host, $db_username, $db_password);
	if (!$con) {
	    die('Could not connect: ' . mysqli_connect_error());
	}
	mysqli_select_db($con,$db_name) or die("Connection failed");

//----------------------------------------------------------------

	function processQuery($Columns,$table,$where){
		if($where==""){
			$whereclause = "";
		} else {
			$whereclause = " WHERE " . $where;
		}
		$con = $GLOBALS['con'];
		$query = "SELECT " .$Columns. " FROM " .$table. $whereclause;
		$result = $con->query($query);
		return $result;
	}

?>
