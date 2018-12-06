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
	
	if(isset($_POST['countAppNotifs'])){
		$query = 'SELECT COUNT(notif_id) as notifs FROM t_notification WHERE Status = "unresolved" and type in ("newanoapp","newapp")';
		$result = $con->query($query);
		if(mysqli_num_rows($result)>0){
			while($row = mysqli_fetch_assoc($result)){
				echo $row["notifs"];
					
			}
		} else {
			echo "0";
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
			$query = "INSERT INTO t_appliance VALUES('".$app_id."','Anonymous_Appliance','GENERAL APPLIANCE',1,0,1,DEFAULT,'" .$date. "',0,0,0,0,DEFAULT)";
		} else {
			$query = "INSERT INTO t_appliance VALUES('".$app_id."','Unregistered_Appliance','GENERAL APPLIANCE',1,0,1,DEFAULT,'" .$date. "',0,0,0,0,DEFAULT)";
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
			echo "Something Went Wrong!" . mysqli_error($con);
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
				if($newlimit != 0){
					$has_power_limit = 1;
				}

				$query = 'UPDATE t_appliance SET has_power=1, power_limit_value = ' . $newlimit . ', has_power_limit = ' . $has_power_limit . '  WHERE uid = "' . $app_id . '"';

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
	
	if(isset($_POST['loadresolvednotifs'])){
		$query = 'SELECT * FROM t_notification WHERE Not Status = "unresolved" ORDER BY date_pop DESC';

		$result = $con->query($query);
		if(mysqli_num_rows($result)>0)
		{
			while($row=mysqli_fetch_assoc($result)) {
				$id = $row['notif_id'];
				$type = $row['type'];
				$app_id = $row['appliance_id'];
				$rowdate = $row['date_pop']; 
				$app_date = date('M d, Y \a\t h:i:s a', strtotime($rowdate));

				if(trim($type)=='consumption'){
					$query2 = 'SELECT * FROM t_appliance WHERE uid = "'. $app_id .'"';
					$result2 = $con->query($query2);
					if(mysqli_num_rows($result2)==1){
						while($row2 = mysqli_fetch_assoc($result2)){
							$name = $row2['appl_name'];
							$consumption = $row2['current_power_usage'];
							$limit = $row2['power_limit_value'];
							$color = "orange";
							$title = "Consumption Almost at Limit";
							if($consumption/1000>=$limit){
								$title = "Consumption Reached Limit";
								$color = "red";
							}
							?>
								<li style="border: solid <?php echo $color;?> 1px; margin-bottom: 10px; border-radius: inherit;-webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2); box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);">
									<div class="collapsible-header" style="display: block;">
										<div class="row" style="margin:0;">
											<div class="col s11 m11" style="padding:0 .1rem;">
												<div>Consumption Warning</div>
												<div class="grey-text kwh"><?php echo $app_date; ?></div>
											</div>
											<div class=" col s1 m1 icon-expand" style="padding:0;"><i class="material-icons" style="margin: 1rem 0;">expand_more</i></div>
										</div>
									</div>
									
									<div class="collapsible-body">
										<div class="row" name='<?php echo $id;?>'  style="margin:0;">
											<div class="col s12 m12 center-block consumption_value" name="<?php echo $consumption?>">
												<div class="consumption_limit" id='<?php echo $id?>' name='<?php echo $limit?>'>
													<div class="card-content white-text">
														<span class="card-title black-text"><b><?php echo $title?></b></span>
														<p class="black-text">ID: <em><?php echo $app_id?></em></p>
														<p class="black-text">Name: <em><?php echo $name?></em></p>
														<p class="black-text">Consumption: <em><?php echo $consumption?> Wh</em></p>
														<p class="black-text">Limit: <em><?php echo $limit?> kWh</em></p>
													</div>
												</div>

											</div>
										</div>
									</div>
								</li>
							<?php
						}

					}
				}else if(trim($type)=='newapp'){
				?>
					<li style="border: solid green 1px;  margin-bottom: 10px; border-radius: inherit; -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2); box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);">
						<div class="collapsible-header" style="display: block;">
							<div class="row" style="margin:0;">
								<div class="col s11 m11" style="padding:0 .1rem;">
									<div>New Appliance Notice</div>
									<div class="grey-text kwh"><?php echo $app_date; ?></div>
								</div>
								<div class=" col s1 m1 icon-expand" style="padding:0;"><i class="material-icons" style="margin: 1rem 0;">expand_more</i></div>
							</div>
						</div>
						
						<div class="collapsible-body">					
							<div class='row' name='<?php echo $id;?>'  style="margin:0;">
								<div class="col s12 m12 center-block">
									<div id='<?php echo $id?>'>
										<div class="card-content white-text">
											<span class="card-title black-text"><b>Unregistered Device</b></span>
											<p class="black-text">ID: <em><?php echo $app_id;?></em></p>
										</div>
									</div>
								</div>
							</div>
					
						</div>
					</li>
				<?php
				}else if(trim($type)=='newanoapp'){
				?>
					<li style="border: solid grey 1px;  margin-bottom: 10px; border-radius: inherit; -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2); box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);">
						<div class="collapsible-header" style="display: block;">
							<div class="row" style="margin:0;">
								<div class="col s11 m11" style="padding:0 .1rem;">
									<div>New Anonymous Appliance Notice</div>
									<div class="grey-text kwh"><?php echo $app_date; ?></div>
								</div>
								<div class=" col s1 m1 icon-expand" style="padding:0;"><i class="material-icons" style="margin: 1rem 0;">expand_more</i></div>
							</div>
						</div>
						
						<div class="collapsible-body">
							<div class='row' name='<?php echo $id;?>'  style="margin:0;">
								<div class="col s12 m12 center-block">
									<div id='<?php echo $id?>'>
										<div class="card-content white-text">
											<span class="card-title black-text"><b>Anonymous Device</b></span>
											<p class="black-text">ID: No Id</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</li>
					
				<?php
				}

			}
		}
	}

	if(isset($_POST['loadnotifs'])){
		$query = 'SELECT * FROM t_notification WHERE Status = "unresolved" ORDER BY date_pop DESC';

		$result = $con->query($query);
		if(mysqli_num_rows($result)>0)
		{
			while($row=mysqli_fetch_assoc($result)) {
				$id = $row['notif_id'];
				$type = $row['type'];
				$app_id = $row['appliance_id'];
				$rowdate = $row['date_pop']; 
				$app_date = date('M d, Y \a\t h:i:s a', strtotime($rowdate));

				if(trim($type)=='consumption'){
					$query2 = 'SELECT * FROM t_appliance WHERE uid = "'. $app_id .'"';
					$result2 = $con->query($query2);
					if(mysqli_num_rows($result2)==1){
						while($row2 = mysqli_fetch_assoc($result2)){
							$name = $row2['appl_name'];
							$consumption = $row2['current_power_usage'];
							$limit = $row2['power_limit_value'];
							$color = "orange";
							$title = "Consumption Almost at Limit";
							if($consumption/1000>=$limit){
								$title = "Consumption Reached Limit";
								$color = "red";
							}
							?>
								<li style="border: solid <?php echo $color;?> 1px; margin-bottom: 10px; border-radius: inherit;">
									<div class="collapsible-header" style="display: block;">
										<div class="row" style="margin:0;">
											<div class="col s11 m11" style="padding:0 .1rem;">
												<div>Consumption Warning</div>
												<div class="grey-text Kwhr"><?php echo $app_date; ?></div>
											</div>
											<div class=" col s1 m1 icon-expand" style="padding:0;"><i class="material-icons" style="margin: 1rem .1rem;">expand_more</i></div>
										</div>
									</div>
									
									<div class="collapsible-body">
										<div class="row" name='<?php echo $id;?>'  style="margin:0;">
											<div class="col s12 m12 center-block consumption_value" name="<?php echo $consumption?>">
												<div class="consumption_limit" id='<?php echo $id?>' name='<?php echo $limit?>'>
													<div class="card-content white-text" style="margin-bottom:1rem;">
														<span class="card-title black-text"><?php echo $title?></span>
														<p class="black-text">ID: <em><?php echo $app_id?></em></p>
														<p class="black-text">Name: <em><?php echo $name?></em></p>
														<p class="black-text">Consumption: <em><?php echo $consumption?> whr</em></p>
														<p class="black-text">Limit: <em><?php echo $limit?> kwhr</em></p>
													</div>
													<div class="card-action right-align" name="<?php echo $name?>">
														<a class="consumption_btn btn-small waves-effect waves-purple modal-trigger sTitle btn-register btn-flat" style="line-height:2rem;" href='#updateLimit' id='<?php echo $app_id;?>'>Update</a>
													</div>
												</div>

											</div>
										</div>
									</div>
								</li>
							<?php
						}

					}
				}else if(trim($type)=='newapp'){
				?>
					<li style="border: solid green 1px;  margin-bottom: 10px; border-radius: inherit;">
						<div class="collapsible-header" style="display: block;">
							<div class="row" style="margin:0;">
								<div class="col s11 m11" style="padding:0 .1rem;">
									<div>New Appliance Notice</div>
									<div class="grey-text kwh"><?php echo $app_date; ?></div>
								</div>
								<div class=" col s1 m1 icon-expand" style="padding:0;"><i class="material-icons" style="margin: 1rem .1rem;">expand_more</i></div>
							</div>
						</div>
						
						<div class="collapsible-body">
							<div class='row' name='<?php echo $id;?>' style="margin:0;">
								<div class="col s12 m12 center-block">
									<div class='for-id' name='<?php echo $id?>' >
										<div class="card-content white-text" style="margin-bottom:1rem;">
											<span class="card-title black-text">Unregistered Device</span>
											<p class="black-text">ID: <em><?php echo $app_id;?></em></p>
										</div>
										<div class="divider"></div>
										<div class="card-action right-align" style="margin-top:1rem;">
											<a id='<?php echo $app_id;?>' class="register-trigger modal-trigger btn-small waves-effect waves-purple sTitle btn-register btn-flat" style="line-height:2rem;" href="#register_appl">Register</a>
											<a id='<?php echo $app_id;?>' class="allow-trigger btn-small waves-effect waves-green modal-trigger sTitle btn-accept btn-flat" style="line-height:2rem;" href='#allowUnregistered'>Allow</a>
											<a class="ignore btn-small waves-effect waves-red sTitle btn-cancel btn-flat" style="line-height:2rem;" name='<?php echo $app_id;?>' id='<?php echo $id?>'>Ignore</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</li>
				<?php
				}else if(trim($type)=='newanoapp'){
				?>
					<li style="border: solid grey 1px;  margin-bottom: 10px; border-radius: inherit;">
						<div class="collapsible-header" style="display: block;">
							<div class="row" style="margin:0;">
								<div class="col s11 m11" style="padding:0 .1rem;">
									<div>New Anonymous Appliance Notice</div>
									<div class="grey-text kwh"><?php echo $app_date; ?></div>
								</div>
								<div class=" col s1 m1 icon-expand" style="padding:0;"><i class="material-icons" style="margin: 1rem .1rem;">expand_more</i></div>
							</div>
						</div>
						
						<div class="collapsible-body">
							<div class='row' name='<?php echo $id;?>' style="margin:0;">
								<div class="col s12 m12 center-block">
									<div class='for-id' name='<?php echo $id?>' >
										<div class="card-content white-text" style="margin-bottom:1rem;">
											<span class="card-title black-text">Anonymous Device</span>
											<p class="black-text">ID: <em>No Id</em></p>
										</div>
										<div class="divider"></div>
										<div class="card-action right-align" style="margin-top:1rem;">
											<a id='<?php echo $app_id;?>' class="allow-trigger btn-small waves-effect waves-green modal-trigger sTitle btn-accept btn-flat"  style="line-height:2rem;" href='#allowUnregistered'>Allow</a>
											<a class="ignore btn-small waves-effect waves-red sTitle btn-cancel btn-flat" style="line-height:2rem;" name='<?php echo $app_id;?>' id='<?php echo $id?>'>Ignore</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</li>
					
				<?php
				}

			}
		}
	}

?>
