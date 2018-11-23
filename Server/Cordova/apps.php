<?php
	require_once 'Config.php';

	$xml = new DOMDocument('1.0', 'utf-8');
	$xml->formatOutput = true;
	$xml->preserveWhiteSpace = false;
	$xml->load('xml/overpass.xml');

	$admin_code = $xml->getElementsByTagName('admin')->item(0)->nodeValue;
?>
<body>
	<input type="hidden" id='admin_pass' name="admin_pass" class='<?php echo $admin_code;?>'>
	<?php
		$query = "SELECT * from t_appliance ";

		$result = $con->query($query);
		if(mysqli_num_rows($result) > 0)
		{
			$c = 0;
			while($row=mysqli_fetch_assoc($result)) {
					$c++;

					$checked = '';
					if($row['has_power']==1){
						$checked = 'checked';
					}
					$disabled = "";
					if($row['uid']=="NO_UID" || $row['appl_name']=="Unregistered_Appliance"){
						$disabled = "disabled";
					}
				?>
					<li class='appliance-info chartHolder' name='<?php echo $row['uid']; ?>'>
						<div class="collapsible-header">
							<div>
								<div class="applianceName truncate" name='<?php echo $row['uid']; ?>'><?php echo $row['appl_name']; ?></div>
								<div class="grey-text kwh" name='<?php echo $row['uid']; ?>'><?php echo $row['current_power_usage']; ?> kwh / <?php echo $row['power_limit_value']; ?> kwh</div>
							</div>

							<span class="badge">
								<div class="switch" style="display:none;">
									<label>
										<!--Off-->
										<input type="checkbox" class='switcher' name='<?php echo $row['uid']; ?>' id='<?php echo $row['uid']; ?>' <?php echo $checked;?>>
										<span class="lever"></span>
										<!--On-->
									</label>
								</div>
							</span>

						</div>
						<div class="collapsible-body">
							<div style="margin-bottom:1rem;" class="actualbody" name='<?php echo $row['uid']; ?>'>
								<p class="fullinfo"> UID: <span> <?php echo $row['uid']; ?> </span></p>
								<p class="fullinfo"> Name: <span> <?php echo $row['appl_name']; ?> </span></p>
								<p class="fullinfo"> Type: <span> <?php echo $row['appl_type']; ?> </span></p>
								<p class="fullinfo"> Power Consumption: <span> <?php echo $row['current_power_usage']; ?> watt(s) </span></p>
								<p class="fullinfo"> Average Consumption: <span> <?php echo $row['avg_watthr']; ?> </span></p>
								<p class="fullinfo pricekwhr"> Price per kWhr: <span> 0 </span></p>
								<p class="fullinfo"> Estimated Price: <span> <?php echo $row['estimated_cost']; ?> </span></p>
								<p class="fullinfo"> Limit: <span> <?php echo $row['power_limit_value']; ?> watt(s) </span> </p>
							</div>

							<div class="row m_bottom0">
								<div class="container">
									<a href="#adminConfirmUpdate" class="d_update_btn col s12 btn-small modal-trigger orange waves-effect waves-light " <?php echo $disabled; ?>><b>Update</b></a>

								</div>

								<div class='forUpdate' style='display:none;'>
									<input type="hidden" class='appl_id' name='<?php echo $row['uid']; ?>' value='<?php echo $row['uid']; ?>'>
									<input type="hidden" class='appl_name' name='<?php echo $row['uid']; ?>' value='<?php echo $row['appl_name']; ?>'>
									<input type="hidden" class='appl_consumption' name='<?php echo $row['uid']; ?>' value='<?php echo $row['current_power_usage']; ?>'>
									<input type="hidden" class='appl_limit' name='<?php echo $row['uid']; ?>' value='<?php echo $row['power_limit_value']; ?>'>
								</div>
							</div>
							
							<!--<div class="divider"></div>-->
							<!--<div class="section">-->
								<!--<div class="id_holder chartjs-wrapper" id="<php //echo $row['uid']?>">-->
									<!--Dynamic ID of canvas-->
									<!--<canvas id="chart<?php echo $c;?>"></canvas>-->
								<!--</div>-->
							<!--</div>-->
						</div>
					</li>
				<?php
			}
		}

		else{
			echo "No Appliance Registered!";
		}

	?>
</body>
</html>
