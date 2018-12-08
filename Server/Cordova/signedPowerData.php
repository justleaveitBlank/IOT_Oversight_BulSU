<?php
	require_once 'Config.php';
	$UID = $_GET["UID"];
	$powerdata = $_GET["powerdata"];
	$notifStat = $_GET["notifStat"];
	$aDevice = $_GET["aDevice"];// value 0/1
	$unPlugged = $_GET["unplugged"];
	//echo "<<\r\n";// IMPORTANT
	//echo "rf\_check\r\n";
	//echo "UNPLUGGED STATUS : ".$unPlugged."\r\n";
	//echo "NotifStat : ".$notifStat."\r\n";
	//echo $UID."\r\n".$powerdata."\r\n";
	$timeLimiNotif="0";
	$getCurrenVal;
	$c_consumed = 0;
	$m_consumed = 0;
	$m_Kwatthr = 0;
	$e_price =  0;
	$m_avg_watthr = 0;
	$lastUID;
	$monthlyReseter;
	
	
	
	//echo $lastUID."\r\n";
	//echo "NOTIFSTAT:\t".$notifStat."\r\n";
	//echo "aDevice\t:\t".$aDevice."\r\n"; 
	//echo "unPlugged :\t".$unPlugged."\r\n";
	//echo "getCurrentVal :\t".$getCurrentVal."\r\n";
	

	$AR_powerdata = explode( "||", $powerdata);
	$voltage = $AR_powerdata[0];
	$ampere = $AR_powerdata[1];
	$power = $AR_powerdata[2];
	$watthr = $AR_powerdata[3];
	$dateTime = date('Y-m-d H:i:s');
	$current_date = date('Y-m-d');
	$time = date('H:i:s');
	$current_month=date('m');
	$current_day=date('d');
	$current_year=date('Y');
	$lstMoUpdate =0;
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
	if($handle = fopen('textVariables.txt','r')){
		while(!feof($handle))
		{
			$content = fgets($handle);
			$arraystr = explode("||",$content);
			$lastUID = substr($arraystr[0],8);
			$c_consumed = (float)trim(substr($arraystr[1],19));
			$lstMoUpdate = substr($arraystr[2],13);
		}
	}
	if($notifStat == "1"){
		$query = "SELECT * FROM t_history WHERE uid='".$UID."' and DATE(effective_date) ='".$current_date."'";
		$result = $con->query($query);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()) {
				// extract row
				// this will make $row['name'] to
				// just $name only
				extract($row);
				file_put_contents("textVariables.txt","lastUID=".$UID ."||currentConsumedVal=".$consumed."||lastMoUpdate=".$lstMoUpdate);
			}
		}
		else{
			file_put_contents("textVariables.txt","lastUID=".$UID."||currentConsumedVal=0||lastMoUpdate=".$lstMoUpdate);
		}
	}
	
	if($handle = fopen('textVariables.txt','r')){
		while(!feof($handle))
		{
			$content = fgets($handle);
			$arraystr = explode("||",$content);
			$lastUID = substr($arraystr[0],8);
			$c_consumed = (float)trim(substr($arraystr[1],19));
			$lstMoUpdate = substr($arraystr[2],13);
		}
	}
	fclose($handle);
	//echo $lstMoUpdate;
	//echo $lastUID." || ".$c_consumed."\r\n";
	include_once 'checkSettings.php';
?>