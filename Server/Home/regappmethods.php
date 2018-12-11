<?php
	require 'Config.php';

	if(isset($_POST['valappreg'])){
		$data = json_decode($_POST['valappreg']);
		$name = $data->name;

		$query = 'SELECT * FROM t_appliance WHERE appl_name = "'.$name.'"';
		$result = $con->query($query);
		if(mysqli_num_rows($result) > 0){
			echo "Invalid" . mysqli_error($con);
		} else {
			echo "Valid" . mysqli_error($con);
		}
	}

	if(isset($_POST['reg'])){
		$data = json_decode($_POST['reg']);
		$id = $data->id;
		$notif_id = $data->notif;
		$name = $data->name;
		$limit = $data->limit;
		$type = $data->type;
		$haslimit = 0;
		if($limit>0.0){
			$haslimit = 1;
		}


		$query = 'INSERT INTO t_appliance VALUES ("'.$id.'","'.$name.'","'.$type.'",1,'.$haslimit.',0,default,default,'.$limit.',0.0,0.0,0.0,NULL)';
		$result = $con->query($query);
		if($result){
			$query = 'UPDATE t_notification SET Status = "registered" WHERE notif_id = "'.$notif_id.'"';
			$result = $con->query($query);
			if($result){
				echo 'Success ' . mysqli_error($con);
			} else {
				echo 'Failed ' . mysqli_error($con);
			}

		} else {
			echo 'Failed ' . mysqli_error($con);
		}
	}
?>
