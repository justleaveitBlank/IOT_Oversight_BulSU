<?php
	$UID = $_GET["UID"];
	$powerdata = $_GET["powerdata"];
	$notifStat = $_GET["notifStat"];
	$aDevice = $_GET["aDevice"];// value 0/1
	$unPlugged = $_GET["unplugged"];
	//echo "UNPLUGGED STATUS : ".$unPlugged."\r\n";
	//echo "NotifStat : ".$notifStat."\r\n";
	//echo $UID."\r\n".$powerdata."\r\n";
	$getCurrentConsumption;
	$c_consumed;
	if($unPlugged == "true"){
		file_put_contents("textVariables.txt","getCurrentVal=true||currentConsumedVal=0");
	}
	
	if($handle = fopen('textVariables.txt','r')){
		while(!feof($handle))
		{
			$content = fgets($handle);
			$arraystr = explode("||",$content);
			$getCurrentConsumption = substr($arraystr[0],14);
			$c_consumed = (int)trim(substr($arraystr[1],19));
		}
	}
	fclose($handle);
	
	//echo "NOTIFSTAT:\t".$notifStat."\r\n";
	//echo "aDevice\t:\t".$aDevice."\r\n"; 
	echo "unPlugged:\t".$unPlugged."\r\n";
	echo "getCurrentConsumption :\t".$getCurrentConsumption."\r\n";
	

	$AR_powerdata = explode( "||", $powerdata);
	$voltage = $AR_powerdata[0];
	$ampere = $AR_powerdata[1];
	$power = $AR_powerdata[2];
	$watthr = $AR_powerdata[3];
	$dateTime = date('Y-m-d H:i:s');
	$date = date('Y-m-d');
	$time = date('H:i:s');
	//$status0 = file_put_contents("txt_has_power.txt",$UID. "||" . $powerdata . "||" . $dateTime . "\r\n" , FILE_APPEND);
	
	//$Kwatthr=$watthr/1000;
	//$appliance_consumption_data = array(
    //       "uid" => $UID,
	//		"voltage" => $voltage,
	//		"ampere" => $ampere,
	//		"power" => $power,
	//		"watthr" => $watthr,
	//		"date" => $date,
	//		"time" => $time
	//	);
	//$json_appliance_consumption_data = json_encode($appliance_consumption_data, JSON_PRETTY_PRINT);
	//echo $json_appliance_consumption_data;

	//create json file
	//$status = file_put_contents('json_appliance_consumption_data.json', $json_appliance_consumption_data);


	//if ($status && $status0){
		//echo  $UID. "||" . $powerdata . "||" . $date . "\r\n";
	//}
	//else{
		//echo "Falied to write";
	//}
	include_once 'checkSettings.php';
?>