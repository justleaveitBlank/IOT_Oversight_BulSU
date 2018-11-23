<?php
	require_once 'Config.php';

	//Delete other plugged Notices
	$query = "UPDATE t_notification SET status='ignored' WHERE status='unresolved' and type in ('newapp','newanoapp')";
	if($con->query($query)){
		$query = 'SELECT *
				  FROM t_notification
				  WHERE type="'.$type.'" and (status = "ignored" or status = "allowed" or status="unresolved") and appliance_id = "'.$appl_uid.'"';

		$result = $con->query($query);

		//--------------------------------------- CHECK IF EXISTING  ----------------------------
		if(mysqli_num_rows($result) > 0){
			$whilePluggedIn = true;
			$statusQuery = 'SELECT status
							FROM t_notification
							WHERE type="'.$type.'" (status = "ignored" or status = "allowed") and appliance_id = "' . $appl_uid.'"';
			$statusResult = $con->query($query);
			if(mysqli_num_rows($statusResult) > 0 ){
				$updateQuery = "UPDATE t_notification 
								SET status = 'unresolved' , date_pop = '".date("y-m-d H:i:s")."'
								WHERE notif_id = (SELECT MAX(tn.notif_id) from (select * from t_notification) as tn WHERE tn.type='".$type."' and (tn.status = 'ignored' or tn.status = 'allowed') and tn.appliance_id = '" . $appl_uid . "') and type='".$type."' and (status = 'ignored' or status = 'allowed') and appliance_id = '" . $appl_uid. "'";

				//echo "query: " . $updateQuery;
				if($con->query($updateQuery)){
					echo mysqli_error($con);
				}
			}
		}
		else{
			$query = "INSERT INTO t_notification values ('','".$type."','unresolved','".$appl_uid."','".date("y-m-d H:i:s")."')";
			if($con->query($query)){
				echo mysqli_error($con);
			}
		}
	}
?>
