<?php
	$UID = $_GET["UID"];
	$powerdata = $_GET["powerdata"];
	$notifStat = $_GET["notifStat"];
	$aDevice = $_GET["aDevice"];// value 0/1
	//echo "NotifStat : ".$notifStat."\r\n";
	//echo $UID."\r\n".$powerdata."\r\n";
	date_default_timezone_set("Asia/Manila");
	$dateNtimeNzone = date("c");

	$AR_powerdata = explode( "||", $powerdata);
	$voltage =$AR_powerdata[0];
	$ampere =$AR_powerdata[1];
	$power =$AR_powerdata[2];
	$watthr =$AR_powerdata[3];
	$dateTime = explode("T",$dateNtimeNzone);
	$date = $dateTime[0];
	$timeNzone = explode("+",$dateTime[1]);
	$time = $timeNzone[0];
	$timezone = "+".$timeNzone[1];
	$status0 = file_put_contents("txt_has_power.txt",$UID. "||" . $powerdata . "||" . $dateNtimeNzone . "\r\n" , FILE_APPEND);

	$appliance_consumption_data = array(
            "uid" => $UID,
			"voltage" => $voltage,
			"ampere" => $ampere,
			"power" => $power,
			"watthr" => $watthr,
			"date" => $date,
			"time" => $time,
			"gmt" => $timezone,
		);
	$json_appliance_consumption_data = json_encode($appliance_consumption_data, JSON_PRETTY_PRINT);
	//echo $json_appliance_consumption_data;

	//create json file
	$status = file_put_contents('json_appliance_consumption_data.json', $json_appliance_consumption_data);


	if ($status && $status0){
		//echo  $UID. "||" . $powerdata . "||" . $date . "\r\n";
	}
	else{
		echo "Falied to write";
	}
	include_once 'checkSettings.php';
