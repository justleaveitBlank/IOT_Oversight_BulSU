<!DOCTYPE HTML>
<html>
<head>
	<title>OVERSIGHT</title>
	<link rel="shortcut icon" href="imgs/Logo3.png">
	
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<!--Let browser know website is optimized for mobile-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	
	<!--Import Google Icon Font-->
	<link href="css/material+icons.css" rel="stylesheet">
	
	<!--Import materialize.css-->
	<link type="text/css" rel="stylesheet" href="css/materialize.css"  media="screen,projection"/>
	<style>
		@viewport {
		  width: device-width ;
		  zoom: 1.0 ;
		}
		@-ms-viewport {
		  width: device-width ;
		} 
		*{
			margin:0;
			font-family: verdana,arial;
			font-smooth: always;
			font-smoothing: antialiased;
			font-size:16px;
			user-select:none;
		}
		html, body{
			height: 100%;
		}
		.navbg{
			background:rgba(75,0,130,1);

		}
		.titleHolder{
			padding: 1rem;
			margin-bottom:1rem;
			background: rgba(138,43,226,1) radial-gradient(transparent,rgba(75,0,130,1));
			-webkit-box-shadow: 0 1px 0 1px #eeeeee;
					box-shadow: 0 1px 0 1px #eeeeee;
		}
		.appLogo{
			margin:0 auto;
			height:5rem;
			width:5rem;
			background:url("imgs/Logo_100px.png") no-repeat;
			background-size:cover;
			font-size: 3.2vh;
			user-select:none;
			position:relative;
		}
		.appName{
			font-size: 2rem;
			color:white;
			font-weight:bold;
			margin: 0 auto;
			width: fit-content;
			user-select:none;
			position:relative;
		}
		.version{
			font-size: 1rem;
			color:white;
			font-weight:bold;
			margin: 0 auto;
			width: fit-content;
			user-select:none;
			position:relative;
		}
		.waves-effect.waves-purple1 .waves-ripple {
			background:rgba(75,0,130,1);
		}
		.my-wrapper {
		  height: 100%;
		  overflow-y:scroll;
		  padding:10px;
		}
		.body-wrapper{
			position:relative;
			top:56px;
			z-index:-1;
		}
		.sidenav-overlay{
			z-index: 996;
		}
		.had-container{
			margin: 0.75rem auto;
		}
		.collapsible{
			margin:0;
		}
		span.badge{
			padding:0;
		}
		.kwh{
			padding:.1rem .5rem;
			font-size: 1rem;
		}
		@media only screen and (min-width: 320px) {
			.applianceName {
				font-size: 1rem;
				max-width: 10rem;
			}
		}
		@media only screen and (min-width: 360px) {
			.applianceName {
				font-size: 1rem;
				max-width: 15rem;
			}
		}
		@media only screen and (min-width: 390px) {
			.applianceName {
				font-size: 1rem;
				max-width: 15rem;
			}
		}
		@media only screen and (min-width: 420px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 450px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}		
		}
		@media only screen and (min-width: 480px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}

		@media only screen and (min-width: 510px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 540px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}		
		}
		@media only screen and (min-width: 570px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 600px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 630px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 660px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 690px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 720px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 750px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 780px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		@media only screen and (min-width: 900px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}

		@media only screen and (min-width: 930px) {
			.applianceName {
				font-size: 1rem;
				max-width: 50rem;
			}
		}
		
		.collapsible-body {
			padding: 1rem;
		}
		.fullinfo{
			font-size: 1rem;
			font-weight:700;
		}
		.fullinfo span{
			font-size: 1rem;
			font-weight:400;
		}
		.switch label input[type=checkbox]:checked + .lever {
			background-color: #ce93d8;
		}
		.switch label input[type=checkbox]:checked + .lever:after {
			background-color: #6a1b9a;
		}
		nav .minFont{
			font-size:1.15rem;
			min-width: 20rem;
		}
		input.border_bottom_green.valid:not([type]), 
		input.border_bottom_green.valid:not([type]):focus,
		input[type=text].border_bottom_green.valid:not(.browser-default),
		input[type=text].border_bottom_green.valid:not(.browser-default):focus,
		input[type=password].border_bottom_green.valid:not(.browser-default),
		input[type=password].border_bottom_green.valid:not(.browser-default):focus,
		input[type=email].border_bottom_green.valid:not(.browser-default),
		input[type=email].border_bottom_green.valid:not(.browser-default):focus,
		input[type=url].border_bottom_green.valid:not(.browser-default),
		input[type=url].border_bottom_green.valid:not(.browser-default):focus,
		input[type=time].border_bottom_green.valid:not(.browser-default),
		input[type=time].border_bottom_green.valid:not(.browser-default):focus,
		input[type=date].border_bottom_green.valid:not(.browser-default),
		input[type=date].border_bottom_green.valid:not(.browser-default):focus,
		input[type=datetime].border_bottom_green.valid:not(.browser-default),
		input[type=datetime].border_bottom_green.valid:not(.browser-default):focus,
		input[type=datetime-local].border_bottom_green.valid:not(.browser-default),
		input[type=datetime-local].border_bottom_green.valid:not(.browser-default):focus,
		input[type=tel].border_bottom_green.valid:not(.browser-default),
		input[type=tel].border_bottom_green.valid:not(.browser-default):focus,
		input[type=number].border_bottom_green.valid:not(.browser-default),
		input[type=number].border_bottom_green.valid:not(.browser-default):focus,
		input[type=search].border_bottom_green.valid:not(.browser-default),
		input[type=search].border_bottom_green.valid:not(.browser-default):focus,
		textarea.materialize-textarea.border_bottom_green.valid,
		textarea.materialize-textarea.border_bottom_green.valid:focus, 
		.select-wrapper.border_bottom_green.valid > input.border_bottom_green.select-dropdown{
		  border-bottom: 1px solid #4CAF50;
		  -webkit-box-shadow: 0 1px 0 0 #4CAF50;
				  box-shadow: 0 1px 0 0 #4CAF50;
		}
		
		.text_indent{
			text-indent:2rem
		}
		input.border_bottom_darkgray:not([type]):focus:not([readonly]),
		input[type=text].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=password].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=email].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=url].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=time].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=date].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=datetime].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=datetime-local].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=tel].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=number].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		input[type=search].border_bottom_darkgray:not(.browser-default):focus:not([readonly]),
		textarea.border_bottom_darkgray.materialize-textarea:focus:not([readonly]) {
		  border-bottom: 1px solid #616161;
		  -webkit-box-shadow: 0 1px 0 0 #757575;
				  box-shadow: 0 1px 0 0 #757575;
		}
		.overhidden{
			overflow:hidden;
		}
		.marginer{
			margin:.5rem;
		}
		.card .card-content {
			padding: .6rem;
			border-radius: 0 0 2px 2px;
		}
		.card .card-content p{
			margin: 0 1rem;
		}
		.card .card-action {
			background-color: inherit;
			border-top: 1px solid rgba(160, 160, 160, 0.2);
			position: relative;
			padding: .7rem .7rem;
		}
		.card {
			margin: 0.1rem 0; 
		}
	</style>
	<!--JavaScript at end of body for optimized loading-->
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.js"></script>
	<script type="text/javascript" src="js/sidenav.js"></script>
	<script type="text/javascript" src="js/transition.js"></script>
	<script type="text/javascript" src="js/socket.io.js"></script>
	<script type="text/javascript" src='js/oversight_dep.js'></script>

	<script>
		$(document).ready(function(){
			// the "href" attribute of .modal-trigger must specify the modal ID that   wants to be triggered
			$('#allowUnregistered').modal();
			$('#updateLimit').modal();
			
			
			$('select').formSelect();
		});
	</script>
</head>
<body>
	<div class="navbar-fixed">
		<nav class="navbg" role="navigation">
			<div class="nav-wrapper container">
				<a id="logo-container" href="#" class="brand-logo center-align minFont">NOTIFICATIONS</a>
				<ul class="right hide-on-med-and-down">
					<li><a class="waves-effect" href="home.html">Home</a></li>
					<li ><a class="waves-effect" href="registerAppliance.html">Register Appliance</a></li>
					<li><a class="waves-effect" href="statistic.html">Overall Statistics</a></li>
					<li class="active"><a class="waves-effect" href="notifications.html">Notifications</a></li>
					<li><a class="waves-effect" href="settings.html">General Settings</a></li>
					<li><a  class="waves-effect" href="#">Logout</a></li>
				</ul>

				<ul id="nav-mobile" class="sidenav grey darken-3">
					<div class="titleHolder">
						<div class="appLogo"></div>
						<p class="appName center-align">OVERSIGHT</p>
						<div class="version right-align">v1.0.0</div>
					</div>
					<li><a class="waves-effect white-text" href="home.html"><i class="material-icons prefix white-text">home</i><p>Home</p></a></li> 
					<li><a class="waves-effect white-text" href="registerAppliance.html"><i class="material-icons prefix white-text">dashboard</i><p>Register Appliance</p></a></li>
					<li><a class="waves-effect white-text" href="statistic.html"><i class="material-icons prefix white-text">pie_chart</i><p> Overall Statistics</p></a></li>
					<li class="active"><a class="waves-effect white-text" href="notifications.html"><i class="material-icons prefix white-text">notifications</i><p>Notifications</p></a></li>
					<li><a class="waves-effect white-text" href="settings.html"><i class="material-icons prefix white-text">settings</i><p>General Settings</p></a></li>
					<li><a class="waves-effect white-text" href="#"><i class="material-icons prefix white-text">input</i><p id='SignOut'>Logout</p></a></li>
				</ul>
				<a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
			</div>
		</nav>
	</div>
	
	<div class="had-container">
		<div class="row">
			<div class="col s12 m4 l2 hide-on-med-and-down"></div>
			<div class="row">
				<div class="col s12 m6">
					<div class="card">
						<div class="card-content white-text">
							<span class="card-title black-text">THIS BUTTONS ARE FOR DEPLOYING NOTIFICATION</span>
						</div>
						<div class="card-action right-align">
							<a class="btn-small waves-effect waves-light orange white-text sTitle" id='addnotif_newapp'>NEW ID APPL</a>
							<a class="btn-small waves-effect waves-light brown white-text sTitle" id='addnotif_newanoapp'>NEW ANONYMOUS APPL</a>
							<a class="btn-small waves-effect waves-light red white-text sTitle" id='addnotif_limit'>LIMIT WARNING</a>
						</div>
					</div>	
				</div>
			</div>
			<div class="col s12 m4 l8 overhidden" id='notifholder'>
				
				
				
			</div>
			
			<div class="col s12 m4 l2 hide-on-med-and-down"></div>
		</div>
	</div>
	<!--Modal Structures-->
	<div class="modalCon">
		<div class="my-wrapper valign-wrapper">
			<!-- Modal Structure for Unregistered Device Granted-->
			<div id="allowUnregistered" class="modal modal-fixed-footer">
				<form class="col s12" method="POST" action="">
					<div class="modal-content">
						<div class="row">
							<h5>Allow Restrictions</h5>
						</div>	
						<div class="row">
							<div class="input-field col s12">
								<select>
									<option value="" disabled selected>Choose your option</option>
									<option value="1">No Restrictions</option>
									<option value="2">1 hour</option>
									<option value="3">6 hrs</option>
									<option value="3">12 hrs</option>
								</select>
								<label>Restrictions</label>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<a href="#!" class=" modal-action modal-close waves-effect waves-light green white-text btn-flat">Accept</a>
						<a href="#!" class=" modal-action modal-close waves-effect waves-light red white-text btn-flat">Cancel</a>
					</div>
				</form>
			</div>
			
			<!-- Modal Structure for Update Device Limit -->
			<div id="updateLimit" class="modal modal-fixed-footer">
				<form class="col s12" method="POST" action="">
					<div class="modal-content">
						<div class="row">
							<h5>Update Limit</h5>
						</div>	
						<div class="row">
							<p class="black-text"><b>ID : </b>qw er ty ui</p>
							<p class="black-text"><b>Name : </b>Appliance Name</p>
							<p class="black-text"><b>Consumption : </b>50kwh</p>
							<p class="black-text"><b>Current Limit : </b>50kwh</p>
							<div class="input-field col s12">
								<input id="d_limit" type="number" class="validate border_bottom_green border_bottom_darkgray" min="0" autocomplete="off" required>
								<label for="d_limit" class="grey-text text-darken-2">Limit (kwh)</label>
							</div>
							
							<p class="black-text">Note: Make the value 0 if you want to turn Off its Consumption limit.</p>
						</div>
					</div>
					<div class="modal-footer">
						<a href="#!" class=" modal-action modal-close waves-effect waves-light green white-text btn-flat">Accept</a>
						<a href="#!" class=" modal-action modal-close waves-effect waves-light red white-text btn-flat">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript"> //the functions are the important ones others are not
		$('#addnotif_newapp').click(function() {
			newappNOTIF();
		});


		$('#addnotif_newanoapp').click(function() {
			newanoappNOTIF();
		});

		$('#addnotif_limit').click(function() {
			consumptionNOTIF();
		});

		function ignoreButtons() {
			$('.ignore').click(function() {
				$(this).closest('.row').fadeOut(function() {
					$(this).remove();
				});
			});
		}

		function newappNOTIF() {
			var app_id = Math.floor((Math.random() * 99999999) + 10000001).toString(); // put the appliance id here (RFID)
			var params = 'UID='+app_id;
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && xhr.status == 200) {
					console.log("Data Sent Successfully!"); // just for testing
					ignoreButtons();
				}
			}
			xhr.open("GET", "http://"+deviceHost+"/generate_json_has_power.php?"+params, true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(null);
			return false;
		}

		function newanoappNOTIF() {
			var params = 'app_id=NOID';
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && xhr.status == 200) {
					console.log("Data Sent Successfully!"); // just for testing
					ignoreButtons();
				}
			}
			xhr.open("POST", "http://"+deviceHost+"/newanoappNotification.php", true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(params);
			return false;
		}

		function consumptionNOTIF() {
			var app_id = 'asdfghjk'; // put the appliance id here
			var params = 'app_id='+app_id; 
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && xhr.status == 200) {
					console.log("Data Sent Successfully!"); // just for testing
					ignoreButtons();
				}
			}
			xhr.open("POST", "http://"+deviceHost+"/consumptionNotification.php", true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(params);
			return false;
		}

		var no_notifs = 0;
		var maxnotif = 0;
		function checknotifs() {
			$.ajax({
				type: "POST",
				data: "countnotifs=" + maxnotif + "&notifs=" + no_notifs,
				url: 'http://' + deviceHost + '/notifmethods.php',
				crossDomain: true,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				success: function(data) {
					console.log(data.trim());
					var res = data.trim().split("|");
					if (data.trim().match(/RELOAD/i)) {
						if (res[0] > no_notifs) {
							diff = res[0] - no_notifs;
							//identifyNotifs(diff);
							no_notifs = res[0];
							maxnotif = res[1];
						}
						loadnotifs();
					} else if (res[0] == 0) {
						$('#notifholder').text("No Current Notifications");
					}
				}
			});
		}

		function loadnotifs(){
			$.ajax({
				type: "POST",
				data: "loadnotifs=1",
				url: 'http://'+deviceHost+'/notifmethods.php',
				crossDomain: true,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				success: function(data) {
					$('#notifholder').html(data);
					ignoreButtons();
				}
			});
		}

		checknotifs();
		setInterval(checknotifs, 500);

	</script>
</body>
</html>