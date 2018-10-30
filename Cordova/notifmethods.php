<?php
	require 'Config.php';

	if(isset($_POST['countnotifs'])){
		$num = $_POST['notifs'];
		$max_id = $_POST['countnotifs'];

		$query = 'SELECT COUNT(notif_id) as notifs , IFNULL(MAX(notif_id),0) as MaxId FROM t_notification WHERE Status = "unresolved" ORDER BY notif_id desc';
		$result = $con->query($query);
		if(mysqli_num_rows($result)>0){
			while($row = mysqli_fetch_assoc($result)){
				if($row["notifs"] != $num){
					echo $row["notifs"] . "|" . $row['MaxId'] . "|RELOAD";
				} else if ($row["MaxId"] != $max_id){
					echo $row["notifs"] . "|" . $row['MaxId'] . "|RELOAD";
				} else {
					echo $row["notifs"] . "|" . $row['MaxId'] . "|FINE";
				}
			}
		}
	}

	if(isset($_POST['ignorenotif'])){
		$notif_id = $_POST['ignorenotif'];
		$query = 'UPDATE t_notification SET Status="ignored" WHERE notif_id = "' . $notif_id . '"';
		$result = $con->query($query);
		if($result){
			echo 'Success' . mysqli_error($con);
		}else{
			echo 'Failed' . mysqli_error($con);
		}
	}

	if(isset($_POST['allowapp'])){
		$app_id = $_POST['allowapp'];
		$notif_id = $_POST['notif'];
		$time = $_POST['timelimit'];

		if($time =="0"){
			$date="0000-00-00 00:00:00";
		}
		else{
			$datenow = date_create();
			$addedTime =$time." minutes";
			$rawdate = date_add($datenow, date_interval_create_from_date_string($addedTime));
			$date = date_format($rawdate, 'Y-m-d H:i:s');
		}
		if($app_id == "NO_UID"){
			$query = "INSERT INTO t_appliance VALUES('".$app_id."','Anonymous_Appliance',1,0,1,DEFAULT,'" .$date. "',DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT)";
		} else {
			$query = "INSERT INTO t_appliance VALUES('".$app_id."','Unregistered_Appliance',1,0,1,DEFAULT,'" .$date. "',DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT)";
		}

		if($con->query($query)){
			$query = 'UPDATE t_notification SET Status="allowed" WHERE notif_id = "' . $notif_id . '"';
			$result = $con->query($query);
			if($result){
			  echo 'Success' . mysqli_error($con);
			}else{
			  echo 'Failed' . mysqli_error($con);
			}
		} else {
			echo "Something Went Wrong!" .$query. mysqli_error($con);
		}
	}

	if(isset($_POST['getnewnotifs'])){
		$num = (int)$_POST['getnewnotifs'];
		$newnotifs = array();
		for ($i=0; $i < $num; $i++) {
			$query = 'SELECT notif_id,type,appliance_id FROM t_notification WHERE notif_id = (SELECT notif_id FROM t_notification WHERE Status = "unresolved" ORDER by notif_id desc limit ' .$i. ' , 1 )';
			$result = $con->query($query);
			if(mysqli_num_rows($result)>0){
				while($row=mysqli_fetch_assoc($result)) {
					array_push($newnotifs, array('id' => $row['notif_id'],'type'=>$row['type'],'app' => $row['appliance_id']));
				}
			}
		}
		echo json_encode($newnotifs);
	}

	if(isset($_POST['updatelimit'])){
		$newlimit = $_POST['updatelimit'];
		$notif_id = $_POST['notif'];

		$query = 'SELECT appliance_id FROM t_notification WHERE notif_id = ' . $notif_id;

		$p_result = $con->query($query);
		if(mysqli_num_rows($p_result)==1){
			while($row=mysqli_fetch_assoc($p_result)) {
				$app_id = $row['appliance_id'];

				$has_power_limit = 0;
				if($newlimit > 0){
					$has_power_limit = 1;
				}

				$query = 'UPDATE t_appliance SET power_limit_value = ' . $newlimit . ', has_power_limit = ' . $has_power_limit . '  WHERE uid = "' . $app_id . '"';

				$result = $con->query($query);
				if($result){
					$query = 'UPDATE t_notification SET Status = "resolved" WHERE notif_id = ' . $notif_id;

					$result = $con->query($query);
					if($result){
						echo "Success " . mysqli_error($con);

					}else {
						echo "Failed " . mysqli_error($con);
					}

				}else {
					echo "Failed " . mysqli_error($con);
				}
			}
		}else {
			echo "Failed " . mysqli_error($con);
		}
	}

	if(isset($_POST['loadnotifs'])){
		$query = 'SELECT * FROM t_notification WHERE Status = "unresolved"';

		$result = $con->query($query);
		if(mysqli_num_rows($result)>0)
		{
			while($row=mysqli_fetch_assoc($result)) {
				$id = $row['notif_id'];
				$type = $row['type'];
				$app_id = $row['appliance_id'];

				if(trim($type)=='consumption'){
					$query2 = 'SELECT * FROM t_appliance WHERE uid = "'. $app_id .'"';
					$result2 = $con->query($query2);
					if(mysqli_num_rows($result2)==1){
						while($row2 = mysqli_fetch_assoc($result2)){
							$name = $row2['appl_name'];
							$consumption = $row2['current_power_usage'];
							$limit = $row2['power_limit_value'];
							?>
								<div class="row" name='<?php echo $id;?>'>
									<div class="col s12 m12 center-block">
										<div class="card" id='<?php echo $id?>' name='<?php echo $limit?>'>
											<div class="card-content white-text">
												<span class="card-title black-text">Consumption on Limit</span>
												<p class="black-text">ID: <em><?php echo $app_id?></em></p>
												<p class="black-text">Name: <em><?php echo $name?></em></p>
												<p class="black-text">Consumption: <em><?php echo $consumption?> kwh</em></p>
												<p class="black-text">Limit: <em><?php echo $limit?> kwh</em></p>
											</div>
											<div class="card-action right-align">
												<a class="consumption_btn btn-small waves-effect waves-light orange white-text modal-trigger sTitle" href='#updateLimit' id='<?php echo $app_id;?>'>Update</a>
											</div>
										</div>

									</div>
								</div>
							<?php
						}

					}
				}else if(trim($type)=='newapp'){
				?>
					<div class='row' name='<?php echo $id;?>'>
						<div class="col s12 m12 center-block">
							<div class="card" id='<?php echo $id?>'>
								<div class="card-content white-text">
									<span class="card-title black-text">Unregistered Device</span>
									<p class="black-text">ID: <?php echo $app_id;?></p>
								</div>
								<div class="card-action right-align">
									<a id='<?php echo $app_id;?>' class="register-trigger modal-trigger btn-small waves-effect waves-light orange white-text sTitle" href="#register_appl">Register</a>
									<a id='<?php echo $app_id;?>' class="allow-trigger btn-small waves-effect waves-light green white-text modal-trigger sTitle" href='#allowUnregistered'>Allow</a>
									<a class="ignore btn-small waves-effect waves-light red white-text sTitle" name='<?php echo $app_id;?>' id='<?php echo $id?>'>Ignore</a>
								</div>
							</div>
						</div>
					</div>
				<?php
				}else if(trim($type)=='newanoapp'){
				?>
					<div class='row' name='<?php echo $id;?>'>
						<div class="col s12 m12 center-block">
							<div class="card" id='<?php echo $id?>'>
								<div class="card-content white-text">
									<span class="card-title black-text">Anonymous Device</span>
									<p class="black-text">ID: No Id</p>
								</div>
								<div class="card-action right-align">
									<a id='<?php echo $app_id;?>' class="register-trigger btn-small waves-effect waves-light orange white-text sTitle" href="#register_appl" disabled>Register</a>
									<a id='<?php echo $app_id;?>' class="allow-trigger btn-small waves-effect waves-light green white-text modal-trigger sTitle" href='#allowUnregistered'>Allow</a>
									<a class="ignore btn-small waves-effect waves-light red white-text sTitle" name='<?php echo $app_id;?>' id='<?php echo $id?>'>Ignore</a>
								</div>
							</div>
						</div>
					</div>
				<?php
				}

			}
		}
	}

?>
