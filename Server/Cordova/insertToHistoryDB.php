<?php
	require_once 'Config.php';
	
	//echo $dateTime."\r\n";
	//echo $current_day." || ".$time."\r\n";
	$query = "SELECT * FROM t_history WHERE uid='".$UID."' and DATE(effective_date) ='".$current_date."'";
	$result = $con->query($query);
	
	if($result->num_rows > 0){
		//echo "01\r\n";
		while($row = $result->fetch_assoc()) {
			// extract row
			// this will make $row['name'] to
			// just $name only
			extract($row);
			$c_watthr = $c_consumed + $watthr;
			if($has_power == "1" && $watthr != 0){
				//echo $c_consumed." + ".$watthr. " = ".$c_watthr."\r\n";
				$upd_q ="UPDATE t_history SET consumed=$c_watthr,lst_updt_dte ='$dateTime' WHERE uid='$UID' and DATE(effective_date)='$current_date'";
				if($con->query($upd_q)){
					echo mysqli_error($con);
				}
			}
			
		}
			
	}
	else{
		//echo "02\r\n";
		if($has_power == "1"){
			//echo "INSERT CODE_1\r\n";
			$insert_query = "INSERT INTO t_history VALUES ('$UID', '$watthr','$dateTime','$dateTime')";
			if($con->query($insert_query)){
				echo mysqli_error($con);
			}
			file_put_contents("textVariables.txt","lastUID=".$UID."||currentConsumedVal=".$watthr);
		}
	}
?>