<?php 
	require 'Config.php';
	
	$query = "SELECT * from t_settings";

	$result = $con->query($query);
	if(mysqli_num_rows($result)==1)
	{
		while($row=mysqli_fetch_assoc($result)) {
			$socket_status = $row['socket'];
			$limit_status = $row['limitation'];
			$authentication_status = $row['authentication'];
			$price = $row['price'];

			$socket = ($socket_status=='true') ? "checked" : "";
			$limit = ($limit_status=='true') ? "checked" : ""; 
			$authentication = ($authentication_status=='true') ? "checked" : "";
		}
	}
	
	else{
		echo "Something Went Wrong!";
	}
?>
<input type="hidden" name="settings" id='adco' value='1234'>
<input type="hidden" name="settings" id='h_socket' value='<?php echo $socket_status;?>'>
<input type="hidden" name="settings" id='h_limit' value='<?php echo $limit_status?>'>
<input type="hidden" name="settings" id='h_authentication' value='<?php echo $authentication_status?>'>
<input type="hidden" name="settings" id='h_price' value='<?php echo $price;?>'>

<div class="row" id='row_setting' name="Price (₱ <?php echo $price;?>)">
	<div class="col s12 m4 l2 hide-on-med-and-down"></div>
	
	<div class="col s12 m4 l8 overhidden">
		<div class="divider"></div>
		<div class="section row">
			<div class="col s8">
				<div class="sTitle">Socket</div>
				<div class="sDesc">Main switch of the power socket</div>
			</div>
			<div class="col s4">
				<span class="badge">
					<div class="switch">
						<label>
							Off
							<input type="checkbox" id='s_socket' <?php echo $socket;?>>
							<span class="lever"></span>
							On
						</label>
					</div>
				</span>
			</div>
		</div>
		
		<div class="divider"></div>
		<div class="section row">
			<div class="col s8">
				<div class="sTitle">Consumption Limit</div>
				<div class="sDesc">Option for Granting Consumption Limits for registered appliances</div>
			</div>
			<div class="col s4">
				<span class="badge">
					<div class="switch">
						<label>
							Off
							<input type="checkbox" id='s_limit' <?php echo $limit;?>>
							<span class="lever"></span>
							On
						</label>
					</div>
				</span>
			</div>
		</div>
		<div class="divider"></div>
		<div class="section row">
			<div class="col s8">
				<div class="sTitle">Strict Authentication</div>
				<div class="sDesc">Socket Strict mode</div>
			</div>
			
			<div class="col s4">
				<span class="badge">
					<div class="switch">
						<label>
							Off
							<input type="checkbox" id='s_authenticate' <?php echo $authentication;?>>
							<span class="lever"></span>
							On
						</label>
					</div>
				</span>
			</div>
		</div>
		
		<div class="divider"></div>
		<div class="section">
			<a id='price-button-admin' class="black-text modal-trigger sTitle" style="display:block" href="#adminConfirmChangeRate">Rate / KwH </a>
		</div>
		
		<div class="divider"></div>

		<div class="section">
			<a class="black-text modal-trigger sTitle" style="display:block" href="#adminConfirmChangePass">Change Administrator Password</a>
		</div>
		
		<div class="divider"></div>
	</div>	
	
</div>

