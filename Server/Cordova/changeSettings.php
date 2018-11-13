<?php  
	require_once 'Config.php';
	if(isset($_POST['getSettings'])){
		$query = "SELECT * from t_settings";
		$result = $con->query($query);
		$rows =array();

		if(mysqli_num_rows($result)==1){
			while ($row = mysqli_fetch_assoc($result)) {
				$rows[] = $row;
			}
			echo json_encode($rows);
		}
	}

	if(isset($_POST['settings'])){
		$data = $_POST['settings'];
		$settings = json_decode($data);
		$socket = $settings->socket;
		$limit = $settings->limit;
		$authenticate = $settings->deviceauthentication;
		$price = $settings->price;
		$admin = $settings->admin;

		$query = "UPDATE t_settings SET socket = '$socket', limitation = '$limit' , authentication = '$authenticate' , price = $price, admin = '$admin'";
		
		$result = $con->query($query);
		if($result){
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

					$res = $xml->save('xml/overpass.xml');
					if($res){
						echo 'Success!';
					} else{
						echo "Failed!" . mysqli_error($con); 
					}
				}
			}
		}
		
		else{
			echo "Failed!" . mysqli_error($con); 
		}
	}
?>