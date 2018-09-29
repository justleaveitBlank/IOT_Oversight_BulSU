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
		<div class="section">
			<span class="sTitle">Socket</span>
			
			<span class="badge">
				<div class="switch">
					<label>
						<!--Off-->
						<input type="checkbox" id='s_socket' <?php echo $socket;?>>
						<span class="lever"></span>
						<!--On-->
					</label>
				</div>
			</span>
		</div>
		
		<div class="divider"></div>
		<div class="section">
			<span class="sTitle">Consumption Limit</span>
			
			<span class="badge">
				<div class="switch">
					<label>
						<!--Off-->
						<input type="checkbox" id='s_limit' <?php echo $limit;?>>
						<span class="lever"></span>
						<!--On-->
					</label>
				</div>
			</span>
		</div>
		<div class="divider"></div>
		<div class="section">
			<span class="sTitle">Device Authentication</span>
			
			<span class="badge">
				<div class="switch">
					<label>
						<!--Off-->
						<input type="checkbox" id='s_authenticate' <?php echo $authentication;?>>
						<span class="lever"></span>
						<!--On-->
					</label>
				</div>
			</span>
		</div>
		
		<div class="divider"></div>
		<div class="section">
			<a class="black-text modal-trigger sTitle" href="#adminConfirmChangeRate">Price/KwH Rate</a>
		</div>
		
		<div class="divider"></div>

		<div class="section">
			<a class="black-text modal-trigger sTitle" href="#adminConfirmChangePass">Change Administrator Password</a>
		</div>
		
		<div class="divider"></div>
	</div>	
	
</div>

