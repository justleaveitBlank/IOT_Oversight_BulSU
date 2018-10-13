<?php
	require_once 'Config.php';
	$en_email = md5("email");
	$en_confirm = md5("confirm");

	if(isset($_POST['signout'])){
		$xml = new DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;
		$xml->preserveWhiteSpace = false;
		$xml->load('xml/activeuser.xml');

		$userlist = $xml->getElementsByTagName('ActiveUser')->item(0);
		$users = $userlist->getElementsByTagName('ip');
		$flag = 0;
		foreach ($users as $user) {
			if($user->nodeValue == $_SERVER['REMOTE_ADDR']){
				$userlist->removeChild($user);
				echo "Removed" . $user->nodeValue;
			}
		}

		$xml->save('xml/activeuser.xml');
	}

	if(isset($_POST['islogged'])){
		$xml = new DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;
		$xml->preserveWhiteSpace = false;
		$xml->load('xml/activeuser.xml');

		$userlist = $xml->getElementsByTagName('ActiveUser')->item(0);
		$users = $userlist->getElementsByTagName('ip');
		$flag = 0;
		foreach ($users as $user) {
			if($user->nodeValue == $_SERVER['REMOTE_ADDR']){
				$flag = 1;
			}
		}
		if($flag==1){
			echo "true";
		}
	}

	if(isset($_GET[$en_email])){
		echo "REACHED";
		$confirm_code = $_GET[$en_confirm];
		$email = $_GET[$en_email];
		register($confirm_code,$email);
	}

	if(isset($_POST['login_data'])){
		$data = $_POST['login_data'];
		$credentials = json_decode($data);
		login($credentials,$con);
	}

	if(isset($_POST['register_data'])){
		$data = $_POST['register_data'];
		$user = json_decode($data);
		$username = $user->username;
		$email = $user->email;
		$confirm_code = md5($username.$email);
		confirm($confirm_code,$user);
	}

	if(isset($_POST['validate'])){
		$type = $_POST['validate'];
		$value = $_POST['value'];
		$query = "SELECT * from t_users where $type = '$value'";

		$result = $con->query($query);
		if(mysqli_num_rows($result)>0){
			echo "invalid";
		}else{
			echo "valid";
		}
	}

	if(isset($_POST['getUserInfo'])){
		$username = $_POST['getUserInfo'];
		$query = "SELECT * from t_users where username = '".$username."'";

		$resultset = array();

		$result = $con->query($query);
		if(mysqli_num_rows($result)==1){
			while($row = mysqli_fetch_assoc($result)){
				$resultset[] = $row;
			}
			echo json_encode($resultset);
		}else{
			echo "Something is Wrong Here!";
		}
	}

	if(isset($_POST['updateAccount'])){
		$params = json_decode($_POST['updateAccount']);
		$username = $params->username;
		$column = $params->column;
		$value = $params->value;

		$query = "UPDATE t_users SET " .$column. " = '" .$value. "' WHERE username = '" .$username. "'";
		$result = $con->query($query);
		if($result){
			echo "Success";
		}else{
			echo "Something is Wrong Here!";
		}
	}

	function login($user,$con){
		$username = $user->username;
		$password = $user->password;

		$query = "SELECT * from t_users where username = '$username' and password = '$password' and confirm_code IS NULL";

		$result = $con->query($query);
		if(mysqli_num_rows($result)==1)
		{
			$query = "SELECT * from t_settings";
			$result = $con->query($query);
			if(mysqli_num_rows($result)==1){
				while ($row = mysqli_fetch_assoc($result)) {

					//----------------------------------- GET PASS ---------------------
					$_SESSION['admin'] = $row['admin'];
					$xml = new DOMDocument('1.0', 'utf-8');
					$xml->formatOutput = true;
					$xml->preserveWhiteSpace = false;
					$xml->load('xml/overpass.xml');

					$admin = $xml->getElementsByTagName('admin')->item(0) ;

					$newadmin = $xml->createElement('admin',$row['admin']);

					$xml->replaceChild($newadmin,$admin);

					$xml->save('xml/overpass.xml');
					//------------------------------------ SET IP -----------------------
					$_SESSION['admin'] = $row['admin'];
					$xml = new DOMDocument('1.0', 'utf-8');
					$xml->formatOutput = true;
					$xml->preserveWhiteSpace = false;
					$xml->load('xml/activeuser.xml');

					$userlist = $xml->getElementsByTagName('ActiveUser')->item(0);
					$users = $userlist->getElementsByTagName('ip');
					$flag = 0;
					foreach ($users as $user) {
						if($user->nodeValue == $_SERVER['REMOTE_ADDR']){
							$flag = 1;
						}
					}
					if($flag==0){
						$newuser = $xml->createElement('ip',$_SERVER['REMOTE_ADDR']);
						$userlist->appendChild($newuser);
					}

					$success = $xml->save('xml/activeuser.xml');
					if($success){
						echo "Success";
					}
				}
			}
		}
		else{
			echo "Account Doesn't Exist!";
		}
	}

	function register($confirm, $email){
		global $con;
		$query = "UPDATE t_users SET confirm_code=NULL where email = '$email' and confirm_code='$confirm'"; // insert confirm code and email on a table

		$result = $con->query($query);
		if(mysqli_error($con)){
			echo "Failed" .  mysqli_error($con);
		}else if($result){
  			echo "<script>window.close();</script>";
		}
	}

	function confirm($confirm_code,$user){
		global $con;
		$firstname = $user->firstname;
		$lastname = $user->lastname;
		$username = $user->username;
		$password = $user->password;
		$email = $user->email;
		$contact = $user->contact;

		include 'PHPMailerAutoload.php'; //
		$name = 'Maam/Sir';
		$mailer = new PHPMailer(); // instantiate PHPMailer
		$mailer->IsSMTP(); // set the type of protocol to smtp
		$mailer->Host = 'smtp.gmail.com:465'; // i think its default well i didn't change
		$mailer->SMTPAuth = TRUE; // gmg
		$mailer->Port = 465; //port given by google for test runs
		$mailer->mailer="smtp";  // gmg
		$mailer->SMTPSecure = 'ssl'; //gmg
		$mailer->IsHTML(true); //gmg (GOOGLE MO GAGO)
		$mailer->SMTPOptions = array('ssl' => array(
								'verify_peer' => false,
								'verify_peer_name' => false,
								'allow_self_signed' => true)
								);
		$mailer->Username = 'homeoversightapp@gmail.com'; //email as far as im concern i dont want to give my password or email
		$mailer->Password = 'oversightpass'; // so I stick with this 1
		$mailer->From = 'homeoversightapp@gmail.com'; // displayed name of sender in the description later
		$mailer->FromName = 'OVERSIGHT Verification'; // name on inbox
		$en_email = md5("email");
		$en_confirm = md5("confirm");
		$mailer->Body =  'Good Day '.$name.'! This message is from Overisght as part of registration. If you are aware of this please follow this link to proceed http://localhost/methods.php?'.$en_email.'='.$email.'&'.$en_confirm.'='.$confirm_code.' else  please ignore. Following the link may or may not cost depending on your choice.' ; //link of the php file with the recovery email and confirm code
		$mailer->Subject = 'Verification'; //subject
		$mailer->AddAddress($email); // add email as receiver of the mail
		if(!$mailer->send()) { // send mail and check whether it succeed
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mailer->ErrorInfo;
		} else { // if it does succeed

			$query = "INSERT INTO t_users VALUES ('$username','$password','$firstname','$lastname','$contact','$email','$confirm_code',DEFAULT)";
			if(mysqli_query($con,$query)){ //execute query
				echo "An Message have been Sent to your Email!" . mysqli_error($con); // inform if success
			}
			else{
				echo "Something Went Wrong" . mysqli_error($con); // inform if failed
			}
		}
	}


//-------------------------------------APPLIANCE RELATED----------------------------------------------------------
	if(isset($_POST['appl_updates'])){
		$data = $_POST['appl_updates'];
		$updates = json_decode($data);

		$id = $updates->id;
		$name = $updates->name;
		$limit = $updates->limit;

		$query = "UPDATE t_appliance SET appl_name = '".$name."' , appl_name = '".$name."' , power_limit_value = ".$limit." WHERE uid = '".$id."'";

		$result = $con->query($query);
		if($result){
			echo "Success! " . mysqli_error($con);
		} else {
			echo "Failed! " . mysqli_error($con);
		}
	}

	if(isset($_POST['checkappchanges'])){
		$appliance = (int)$_POST['checkappchanges'];
		$existing = 0;

		$query = "SELECT COUNT(uid) as appcount FROM t_appliance";
		$result = $con->query($query);

		if(mysqli_num_rows($result)==1){
			while($row = mysqli_fetch_assoc($result)){
				if($row['appcount']==$appliance){
						echo "No app changes";
				}else{
					echo "Reload";
				}
			}
		}else{
			echo "No Result" . mysqli_error($con);
		}
	}

	if(isset($_POST['off'])){
		$uid = $_POST['off'];
		$query = 'UPDATE t_appliance SET has_power = 0 where uid = "'. $uid .'"';

		$result = $con->query($query);
		if($result){
			echo 'Success ' . mysqli_error($con);
		} else {
			echo 'Failed ' . mysqli_error($con);
		}
	}

	if(isset($_POST['on'])){
		$uid = $_POST['on'];
		$query = 'UPDATE t_appliance SET has_power = 1 where uid = "'. $uid .'"';

		$result = $con->query($query);
		if($result){
			echo 'Success ' . mysqli_error($con);
		} else {
			echo 'Failed ' . mysqli_error($con);
		}
	}

	if(isset($_POST['getconsumptions'])){
		$app = $_POST['getconsumptions'];
		$mon = (int)$_POST['mon'];

		$cons = array();

		for ($i=0; $i <=$mon ; $i++) {
			$month = $i+1;
			$query = "SELECT IFNULL(MAX(consumed),0) as consumed FROM t_history WHERE YEAR(effective_date) = YEAR(CURDATE()) AND MONTH(effective_date) = ". $month ." AND uid = '" . $app . "'";
			$result = $con->query($query);
			if(mysqli_num_rows($result)>0){
				while($row=mysqli_fetch_assoc($result)) {
					array_push($cons, $row['consumed']);
				}
			}else{
				array_push($cons, 0);
			}
		}
		echo json_encode($cons);
	}

	if(isset($_POST['loadappinfo'])){
		$query = "SELECT * FROM t_appliance";
		$result = $con->query($query);

		$rows = array();
		if(mysqli_num_rows($result)>0){
			while($r = mysqli_fetch_assoc($result)) {
			    $rows[]=$r;
			}
			echo json_encode($rows);
		}
	}

//-----------------------------------------------------------------------------------------------------------------
?>
