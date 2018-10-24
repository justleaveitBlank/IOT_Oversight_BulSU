<?php
	require_once 'Config.php';
	$current_day=date('Y-m-d');
	
	//echo $dateTime."\r\n";
	//echo $current_day." || ".$time."\r\n";
	$query = "SELECT * FROM t_history WHERE uid='".$UID."' and DATE(effective_date) ='".$current_day."'";
	$result = $con->query($query);
	
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			// extract row
			// this will make $row['name'] to
			// just $name only
			extract($row);
			$h_effective_date = explode(" ",$effective_date);
			$effective_day = date("Y-m-d",strtotime($h_effective_date[0]));
			echo $watthr."||".$c_consumed."\r\n";
			if($unPlugged = "false" && $getCurrentConsumption == "false"){
				if($has_power == "1"){
						
					echo "UPDATE\r\n";
					echo $c_consumed+$watthr."\r\n";
					echo $watthr."\r\n";
					$upd_q ="UPDATE t_history SET consumed=$watthr+$c_consumed,lst_updt_dte ='$dateTime' WHERE uid='$UID' and DATE(effective_date)='$current_day'";
					if($con->query($upd_q)){
						echo mysqli_error($con);
					}
				}
				else{
					if($has_power == "1"){
						echo "INSERT CODE_2\r\n";
						$insert_query = "INSERT INTO t_history VALUES ('$UID', '$watthr','$dateTime','$dateTime')";
						if($con->query($insert_query)){
							echo mysqli_error($con);
						}
					}
				}
			}
			else if($unPlugged = "false" && $getCurrentConsumption == "true"){
				$c_consumed = $consumed;
				file_put_contents("textVariables.txt","getCurrentVal=false||currentConsumedVal=".$c_consumed);
				echo $watthr."||".$c_consumed."\r\n";
			}
			
		}
	}
	else{
		if($has_power == "1"){
			echo "INSERT CODE_1\r\n";
			$insert_query = "INSERT INTO t_history VALUES ('$UID', '$watthr','$dateTime','$dateTime')";
			if($con->query($insert_query)){
				echo mysqli_error($con);
			}
		}
	}
?>