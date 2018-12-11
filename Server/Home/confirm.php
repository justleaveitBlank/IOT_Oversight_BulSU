<?php  
	require_once 'Config.php';

	if(isset($_GET['confirm'])){
		$email = $_GET['email'];
		$confirm_code = $_GET['confirm'];
		$query = "UPDATE t_users SET confirm_code=NULL where email = '$email' and confirm_code = '$confirm_code'"; // insert confirm code and email on a table 
		if(mysqli_query($con,$query)){ //execute query
			echo "<script>window.close();</script>";
		}
		else{
			echo "Something Went Wrong" . mysqli_error(); // inform if failed
		}
	}



?>