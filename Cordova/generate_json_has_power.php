<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once 'database.php';
include_once 'appliance.php';

// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

// initialize object
$appliance = new Appliance($db);

// get keywords
$appl_uid=isset($_GET["UID"]) ? $_GET["UID"] : "";

// query products
$stmt = $appliance->search($appl_uid);
$num = $stmt->rowCount();
//echo $aDevice."\r\n";
// check if more than 0 record found

$appliance_pluggedStatus= new stdClass();
$appliance_pluggedStatus->plugged = $aDevice;
$appliance_pluggedStatus->uid = "";
$appliance_pluggedStatus->registered = false;

if($aDevice=="1" and $num>0){
	$appliance_pluggedStatus->uid = $appl_uid;
	$appliance_pluggedStatus->registered = true;
} else if ($aDevice=="1" and $num<=0){
	$appliance_pluggedStatus->uid = $appl_uid;
}

$json_has_power_data = json_encode($appliance_pluggedStatus, JSON_PRETTY_PRINT);
//create json file
file_put_contents('plugged.json',  $json_has_power_data);

if($num>0 && $appl_uid !="NO_UID"){

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

		//values get from database
        $appliance_arr=array(
            "uid" => $UID,
			//"appl_name" => $appl_name,
			"has_power" => $has_power,
			//"has_power_limit" => $has_power_limit,
			//"has_time_limit" => $has_time_limit,
			//"current_date_time" => $current_date_time,
			//"time_limit_value" => $time_limit_value,
			//"power_limit_value" => $power_limit_value,
			//"current_power_usage" => $current_power_usage,
			//"avg_watthr" => $avg_watthr,
			//"estimated_cost" => $estimated_cost
			"status" => "registered"

        );
    }
	$json_has_power_data = json_encode($appliance_arr, JSON_PRETTY_PRINT);
	// get from signedPowerData.php since this is impoted
	//echo $UID."||". $voltage."||".$ampere."||". $power."||".$watthr."||".$date."||".$time."||".$timezone."\n\r";
	echo  $json_has_power_data ;

	//create json file
	file_put_contents('json_has_power_data.json',  $json_has_power_data);

}
else if($num>0 && $appl_uid == "NO_UID"){
    // retrieve our table contents
    // fetch() is faster than fetchAll()

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

		//values get from database
        $appliance_arr=array(
            "uid" => $UID,
			"has_power" => $has_power,
			"status" => "registered"

        );
	$json_has_power_data = json_encode($appliance_arr, JSON_PRETTY_PRINT);
	// get from signedPowerData.php since this is impoted
	//echo $UID."||". $voltage."||".$ampere."||". $power."||".$watthr."||".$date."||".$time."||".$timezone."\n\r";
	echo  $json_has_power_data ;

	//create json file
	file_put_contents('json_has_power_data.json',  $json_has_power_data);
	}
}
else{
//--------------------------- CHECK DB IF EXISTING NOTIFICATION -----------------
	if($notifStat == true){
		include_once 'CheckExistingNotif.php';
	}
	else{
		$appliance_arr=array(
			"uid" => $appl_uid,
			"has_power" => "0",//value change depeding on the value of the user select 0/1
			"status" => "unregistered"
		);
		$json_has_power_data = json_encode($appliance_arr, JSON_PRETTY_PRINT);
		// get from signedPowerData.php since this is impoted
		//echo $UID."||". $voltage."||".$ampere."||". $power."||".$watthr."||".$date."||".$time."||".$timezone."\n\r";
		echo  $json_has_power_data ;

		//create json file
		file_put_contents('json_has_power_data.json',  $json_has_power_data);
	}
//-------------------------------------------------------------------------------
}
?>
