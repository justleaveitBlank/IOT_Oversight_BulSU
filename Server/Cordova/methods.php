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

	//login : check credentials and save ip_address

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

	//confirmation : update database

	function register($confirm, $email){
		global $con;
		require_once 'EmailSender.php';
		$message = '<div bgcolor="#FFFFFF" style="margin:0;padding:0">
		  <table border="0" cellpadding="0" cellspacing="0" height="100%" lang="en" style="min-width:348px" width="100%">
		    <tbody>
		      <tr height="32px">
		      </tr>
		      <tr align="center">
		        <td>
		          <div>
		            <div>
		            </div>
		          </div>
		          <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom:20px;max-width:600px;min-width:220px">
		            <tbody>
		              <tr>
		                <td>
		                  <table cellpadding="0" cellspacing="0">
		                    <tbody>
		                      <tr>
		                        <td>
		                        </td>
		                        <td>
		                          <table border="0" cellpadding="0" cellspacing="0" style="direction:ltr;padding-bottom:7px" width="100%">
		                            <tbody>
		                              <tr>
		                                <td align="left">

		                                </td>
		                                <td align="right" style="font-family:Roboto-Light,Helvetica,Arial,sans-serif">
		                                  ' .ucwords(strtolower($firstname)). " " .ucwords(strtolower($lastname)).'</td>
		                                  <td align="right" width="35">
		                                    <img height="28" src="https://ci4.googleusercontent.com/proxy/QpsGaULeBaBhhOTpb-uwGsICda8b1ae95rM7JtYlDtcjbrJ_fDlrGcQ9nUwocVilT_dWdlntnRieTr4GY_IFycf2zxXXuPXiHCdY7G5yRw7uJHHhalp2NYvY=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/anonymous_profile_photo.png" style="width:28px;height:28px;border-radius:50%" width="28" class="CToWUd">
		                                  </td>
		                                </tr>
		                              </tbody>
		                            </table>
		                          </td>
		                          <td>
		                          </td>
		                        </tr>
		                        <tr>
		                          <td height="5" style="background:url("https://ci6.googleusercontent.com/proxy/YK2pE8krofoXEkR4_ul0pmTbXOy8oBMwTKgL5SDU8gwoBnuUsbqBgIDCNap4vDrhYM9k9WQHjvh8SSWGbE0M45cu0RWDOKma_-JQl351yZdov7XF=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-corner-nw.png") top left no-repeat" width="6">
		                            <div>
		                            </div>
		                          </td>
		                          <td height="5" style="background:url("https://ci4.googleusercontent.com/proxy/qd6_Ib81Wm2U6icWL4nSdpndZgMy4yX6Ix9KvhWXDadf7f3C4l1FnqVN9FTFKhcS9NTqc7JwBwmjbI5BKa-LvU09Qa4wYfZQ1hZAJJ5rtSgg1A=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-pixel-n.png") top center repeat-x">
		                            <div>
		                            </div>
		                          </td>
		                          <td height="5" style="background:url("https://ci4.googleusercontent.com/proxy/MeEz6V35gI1jeN-fvP5huv6MbMHb9IrpN6TgclOoj_3vGKPMkjav1DRu3oLD5fpuYVwW--1pVntWzVDyBNZ9FMRKptnA5G6Rh3RoqxC6ENwKn34l=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-corner-ne.png") top right no-repeat" width="6">
		                            <div>
		                            </div>
		                          </td>
		                        </tr>
		                        <tr>
		                          <td style="background:url("https://ci6.googleusercontent.com/proxy/63Xt9bMnBbXxlWgWllE04IJ5P1F6Uc2fHModZeCbhpvHzKK47r6-LFf2GcwHSHdjnZydc_aiz0TMnguOnLZeRpQOIsd2G0j90oHqfmVcJ3WiSw=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-pixel-w.png") center left repeat-y" width="6">
		                            <div>
		                            </div>
		                          </td>
		                          <td>
		                            <div style="color:green;font-family:Roboto-Regular,Helvetica,Arial,sans-serif;padding-left:20px;padding-right:20px;border-bottom:thin solid #f0f0f0;font-size:24px;padding-bottom:38px;padding-top:40px;text-align:center;word-break:break-word">
		                              <div class="m_-964311646947607407v2sp">
		                                <strong>Oversight Account Verified</strong><br>
		                                </div>
		                              </div>
		                              <div style="text-align:center;font-family:Roboto-Regular,Helvetica,Arial,sans-serif;font-size:13px;color:rgba(0,0,0,0.87);line-height:1.6;padding-left:20px;padding-right:20px;padding-bottom:32px;padding-top:24px">
		                                <div class="m_-964311646947607407v2sp">
		                                  Your Oversight Account is now Verified and is now Ready to Use. Have a nice day!<div style="padding-top:24px;text-align:center">

		                                    </div>
		                                  </div>
		                                </div>
		                              </td>
		                              <td style="background:url("https://ci6.googleusercontent.com/proxy/CJS7pBPoAKq2Vl0J2a5adG7qDta1FQpw7geEstEHuo7HwblSAe4CugSezxZhu1ir9naNbzv4tCP_kaaKusmEuYYMMVVMiANxcEFQvTwGMkbEQA=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-pixel-e.png") center left repeat-y" width="6">
		                                <div>
		                                </div>
		                              </td>
		                            </tr>
		                            <tr>
		                              <td height="5" style="background:url("https://ci6.googleusercontent.com/proxy/WXdndO1hXG3H-61_dYefWZbs5WiAti-HYWWSZzgWzPlqVquImgIgRHZlBHTakSR77SpraKtLoBRqwN9lcrdW_FskxPzZYl7X05j0iygqIGw71MDK=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-corner-sw.png") top left no-repeat" width="6">
		                                <div>
		                                </div>
		                              </td>
		                              <td height="5" style="background:url("https://ci5.googleusercontent.com/proxy/vTO93_TyCFAYa1BAMAcBCUMups35HxfekeSHqfkgw_doDs7UmcRnOSkJVPGLbsLL3NEAJgm1YBUflQWLxULI8jWzyiurT3ST_kgJX5gkgJSk-w=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-pixel-s.png") top center repeat-x">
		                                <div>
		                                </div>
		                              </td>
		                              <td height="5" style="background:url("https://ci3.googleusercontent.com/proxy/6BzqtMBCorI0QmM9duPWLqgwOdM6am_nlIHrlz3ROW8m1Cf5NjyvAjj1CyQrRtZoE2rNYtbYXMPiLxrox6k8JITXAOTFO8oUM9UgKkNuD3oinwNM=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-corner-se.png") top left no-repeat" width="6">
		                                <div>
		                                </div>
		                              </td>
		                            </tr>
		                            <tr>
		                              <td>
		                              </td>
		                              <td>
		                                <div style="text-align:left">
		                                  <div style="text-align:center;font-family:Roboto-Regular,Helvetica,Arial,sans-serif;color:rgba(0,0,0,0.54);font-size:12px;line-height:20px;padding-top:10px">
		                                    <div>
		                                      You received this email to let you know about the activities which concerns your accounts.
		                                      Do not share your information to others.
		                                      Ignore or reply to this message if you are not responsible for this action and we will act fast and accordingly.</div><br>
		                                      <div style="direction:ltr">
		                                        © 2018 Home Oversight, DOI Inc<br><a class="m_-964311646947607407afal" style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;color:rgba(0,0,0,0.54);font-size:12px;line-height:20px;padding-top:10px">
		                                          Bulacan State University, city of Malolos, Bulacan, Philippines</a>
		                                        </div>
		                                      </div>
		                                      <div style="display:none!important;max-height:0px;max-width:0px">
		                                        1541396550000000</div>
		                                      </div>
		                                    </td>
		                                    <td>
		                                    </td>
		                                  </tr>
		                                </tbody>
		                              </table>
		                            </td>
		                          </tr>
		                        </tbody>
		                      </table>
		                    </td>
		                  </tr>
		                  <tr height="32px">
		                  </tr>
		                </tbody>
		              </table>
		              <img height="1" src="https://ci5.googleusercontent.com/proxy/rAV_l523Yl_ClQLW28Gj0jfP1usT2jWA9INIdWOh4MbZvrxN795dun5ZX2g_CopEw8cKyn2n1uMrvKM5fHhTzOoQ0UtJ7lFtjU03hKL82IYg-A-KnqNBpc_uvZWRtTNhBV-yUqET2WiTpqjLUO-g2JUUOjfRobG_9g7pzszeK1er8hzeBmYW1m63vH3bHfewXV27bClN1Xqv9VxoDgDYpSz_3nwLGhWctm_fFvnybuJcCH-CYnUSZKBbMLlw0kXWKLdWzdNWfWz4BWsNSmpScnEJ0o5Db0piFR-SMq8JGN-pg5_s6ZPVC2UIkPR9VyB1UXSJ_htmrNiiYZtCOMMvdCnd8E6VBiF6BsLfgfmphXsORFTJwEksZrqzcXTKIxj53nq1VVHUFK5XBPec20Ak7XcM2Q=s0-d-e1-ft#https://notifications.googleapis.com/email/t/AFG8qyVTEW5C8zaceBkyG53ARhpKYuBE-844cd-IMXqDIMttEqz_lxfXgpsANQ8HorWxcHOU45fLEdJX2uAC8S6Ea0UqhmQQsmE5_Pa_NFlu8eEJhmh6njTHj5yET2yGRGphMN_4BaLw-ai3fMWd6_z8kvwpH7ZzVyNq_ez_n4stt95GUtWksxxIQXcSYeivGen7_UUOd7qxm6PuhUK-JrEE4XwGR5ac9kfG9AKIsWYr/a.gif" width="1" class="CToWUd">
		            </div>';

		$TypeOfMail = "Verification";
		$mailer_result = SendEmail($message,$email,$TypeOfMail);
		if(!$mailer_result) { // send mail and check whether it succeed
		echo 'Message could not be sent.';
		} else { // if it does succeed
			$query = "UPDATE t_users SET confirm_code=NULL where email = '$email' and confirm_code='$confirm'"; // insert confirm code and email on a table

			$result = $con->query($query);
			if(mysqli_error($con)){
				echo "Failed" .  mysqli_error($con);
			}else if($result){
	  			echo "<script>window.close();</script>";
			}
		}

	}

	//registration : send confirm code

	function confirm($confirm_code,$user){
		global $con;
		$firstname = $user->firstname;
		$lastname = $user->lastname;
		$username = $user->username;
		$password = $user->password;
		$email = $user->email;
		$contact = $user->contact;
		$en_email = md5("email");
    $en_confirm = md5("confirm");
		require_once 'EmailSender.php';
		$message = '<div bgcolor="#FFFFFF" style="margin:0;padding:0">
		  <table border="0" cellpadding="0" cellspacing="0" height="100%" lang="en" style="min-width:348px" width="100%">
		    <tbody>
		      <tr height="32px">
		      </tr>
		      <tr align="center">
		        <td>
		          <div>
		            <div>
		            </div>
		          </div>
		          <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom:20px;max-width:600px;min-width:220px">
		            <tbody>
		              <tr>
		                <td>
		                  <table cellpadding="0" cellspacing="0">
		                    <tbody>
		                      <tr>
		                        <td>
		                        </td>
		                        <td>
		                          <table border="0" cellpadding="0" cellspacing="0" style="direction:ltr;padding-bottom:7px" width="100%">
		                            <tbody>
		                              <tr>
		                                <td align="left">

		                                </td>
		                                <td align="right" style="font-family:Roboto-Light,Helvetica,Arial,sans-serif">
		                                  ' .ucwords(strtolower($firstname)). " " .ucwords(strtolower($lastname)).'</td>
		                                  <td align="right" width="35">
		                                    <img height="28" src="https://ci4.googleusercontent.com/proxy/QpsGaULeBaBhhOTpb-uwGsICda8b1ae95rM7JtYlDtcjbrJ_fDlrGcQ9nUwocVilT_dWdlntnRieTr4GY_IFycf2zxXXuPXiHCdY7G5yRw7uJHHhalp2NYvY=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/anonymous_profile_photo.png" style="width:28px;height:28px;border-radius:50%" width="28" class="CToWUd">
		                                  </td>
		                                </tr>
		                              </tbody>
		                            </table>
		                          </td>
		                          <td>
		                          </td>
		                        </tr>
		                        <tr>
		                          <td height="5" style="background:url("https://ci6.googleusercontent.com/proxy/YK2pE8krofoXEkR4_ul0pmTbXOy8oBMwTKgL5SDU8gwoBnuUsbqBgIDCNap4vDrhYM9k9WQHjvh8SSWGbE0M45cu0RWDOKma_-JQl351yZdov7XF=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-corner-nw.png") top left no-repeat" width="6">
		                            <div>
		                            </div>
		                          </td>
		                          <td height="5" style="background:url("https://ci4.googleusercontent.com/proxy/qd6_Ib81Wm2U6icWL4nSdpndZgMy4yX6Ix9KvhWXDadf7f3C4l1FnqVN9FTFKhcS9NTqc7JwBwmjbI5BKa-LvU09Qa4wYfZQ1hZAJJ5rtSgg1A=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-pixel-n.png") top center repeat-x">
		                            <div>
		                            </div>
		                          </td>
		                          <td height="5" style="background:url("https://ci4.googleusercontent.com/proxy/MeEz6V35gI1jeN-fvP5huv6MbMHb9IrpN6TgclOoj_3vGKPMkjav1DRu3oLD5fpuYVwW--1pVntWzVDyBNZ9FMRKptnA5G6Rh3RoqxC6ENwKn34l=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-corner-ne.png") top right no-repeat" width="6">
		                            <div>
		                            </div>
		                          </td>
		                        </tr>
		                        <tr>
		                          <td style="background:url("https://ci6.googleusercontent.com/proxy/63Xt9bMnBbXxlWgWllE04IJ5P1F6Uc2fHModZeCbhpvHzKK47r6-LFf2GcwHSHdjnZydc_aiz0TMnguOnLZeRpQOIsd2G0j90oHqfmVcJ3WiSw=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-pixel-w.png") center left repeat-y" width="6">
		                            <div>
		                            </div>
		                          </td>
		                          <td>
		                            <div style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;padding-left:20px;padding-right:20px;border-bottom:thin solid #f0f0f0;color:rgba(0,0,0,0.87);font-size:24px;padding-bottom:38px;padding-top:40px;text-align:center;word-break:break-word">
		                              <div class="m_-964311646947607407v2sp">
		                                <strong>New Oversight Account Registered To</strong><br>
		                                <a style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;color:rgba(0,0,0,0.87);font-size:16px;line-height:1.8">
		                                  '.$email.'</a>
		                                </div>
		                              </div>
		                              <div style="text-align:center;font-family:Roboto-Regular,Helvetica,Arial,sans-serif;font-size:13px;color:rgba(0,0,0,0.87);line-height:1.6;padding-left:20px;padding-right:20px;padding-bottom:32px;padding-top:24px">
		                                <div class="m_-964311646947607407v2sp">
		                                  Your Google Account was used as part of registration of an Oversight Account. You are getting this email to make sure it was you.<div style="padding-top:24px;text-align:center">
		                                    <a href="http://localhost/methods.php?'.$en_email.'='.$email.'&'.$en_confirm.'='.$confirm_code.'" style="display:inline-block;text-decoration:none">
		                                      <table border="0" cellpadding="0" cellspacing="0" style="background-color:#4184f3;border-radius:2px;min-width:90px">
		                                        <tbody>
		                                          <tr style="height:6px">
		                                          </tr>
		                                          <tr>
		                                            <td style="padding-left:8px;padding-right:8px;text-align:center">
		                                              <a href="http://localhost/methods.php?'.$en_email.'='.$email.'&'.$en_confirm.'='.$confirm_code.'" style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;color:#ffffff;font-weight:400;line-height:20px;text-decoration:none;font-size:13px;text-transform:uppercase" >
		                                                Verify My Account</a>
		                                              </td>
		                                            </tr>
		                                            <tr style="height:6px">
		                                            </tr>
		                                          </tbody>
		                                        </table>
		                                      </a>
		                                    </div>
		                                  </div>
		                                </div>
		                              </td>
		                              <td style="background:url("https://ci6.googleusercontent.com/proxy/CJS7pBPoAKq2Vl0J2a5adG7qDta1FQpw7geEstEHuo7HwblSAe4CugSezxZhu1ir9naNbzv4tCP_kaaKusmEuYYMMVVMiANxcEFQvTwGMkbEQA=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-pixel-e.png") center left repeat-y" width="6">
		                                <div>
		                                </div>
		                              </td>
		                            </tr>
		                            <tr>
		                              <td height="5" style="background:url("https://ci6.googleusercontent.com/proxy/WXdndO1hXG3H-61_dYefWZbs5WiAti-HYWWSZzgWzPlqVquImgIgRHZlBHTakSR77SpraKtLoBRqwN9lcrdW_FskxPzZYl7X05j0iygqIGw71MDK=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-corner-sw.png") top left no-repeat" width="6">
		                                <div>
		                                </div>
		                              </td>
		                              <td height="5" style="background:url("https://ci5.googleusercontent.com/proxy/vTO93_TyCFAYa1BAMAcBCUMups35HxfekeSHqfkgw_doDs7UmcRnOSkJVPGLbsLL3NEAJgm1YBUflQWLxULI8jWzyiurT3ST_kgJX5gkgJSk-w=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-pixel-s.png") top center repeat-x">
		                                <div>
		                                </div>
		                              </td>
		                              <td height="5" style="background:url("https://ci3.googleusercontent.com/proxy/6BzqtMBCorI0QmM9duPWLqgwOdM6am_nlIHrlz3ROW8m1Cf5NjyvAjj1CyQrRtZoE2rNYtbYXMPiLxrox6k8JITXAOTFO8oUM9UgKkNuD3oinwNM=s0-d-e1-ft#https://www.gstatic.com/accountalerts/email/hodor/4-corner-se.png") top left no-repeat" width="6">
		                                <div>
		                                </div>
		                              </td>
		                            </tr>
		                            <tr>
		                              <td>
		                              </td>
		                              <td>
		                                <div style="text-align:left">
		                                  <div style="text-align:center;font-family:Roboto-Regular,Helvetica,Arial,sans-serif;color:rgba(0,0,0,0.54);font-size:12px;line-height:20px;padding-top:10px">
		                                    <div>
		                                      You received this email to let you know about the activities which concerns your accounts.
		                                      Do not share your information to others.
		                                      Ignore or reply to this message if you are not responsible for this action and we will act fast and accordingly.</div><br>
		                                      <div style="direction:ltr">
		                                        © 2018 Home Oversight, DOI Inc<br><a class="m_-964311646947607407afal" style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;color:rgba(0,0,0,0.54);font-size:12px;line-height:20px;padding-top:10px">
		                                          Bulacan State University, city of Malolos, Bulacan, Philippines</a>
		                                        </div>
		                                      </div>
		                                      <div style="display:none!important;max-height:0px;max-width:0px">
		                                        1541396550000000</div>
		                                      </div>
		                                    </td>
		                                    <td>
		                                    </td>
		                                  </tr>
		                                </tbody>
		                              </table>
		                            </td>
		                          </tr>
		                        </tbody>
		                      </table>
		                    </td>
		                  </tr>
		                  <tr height="32px">
		                  </tr>
		                </tbody>
		              </table>
		              <img height="1" src="https://ci5.googleusercontent.com/proxy/rAV_l523Yl_ClQLW28Gj0jfP1usT2jWA9INIdWOh4MbZvrxN795dun5ZX2g_CopEw8cKyn2n1uMrvKM5fHhTzOoQ0UtJ7lFtjU03hKL82IYg-A-KnqNBpc_uvZWRtTNhBV-yUqET2WiTpqjLUO-g2JUUOjfRobG_9g7pzszeK1er8hzeBmYW1m63vH3bHfewXV27bClN1Xqv9VxoDgDYpSz_3nwLGhWctm_fFvnybuJcCH-CYnUSZKBbMLlw0kXWKLdWzdNWfWz4BWsNSmpScnEJ0o5Db0piFR-SMq8JGN-pg5_s6ZPVC2UIkPR9VyB1UXSJ_htmrNiiYZtCOMMvdCnd8E6VBiF6BsLfgfmphXsORFTJwEksZrqzcXTKIxj53nq1VVHUFK5XBPec20Ak7XcM2Q=s0-d-e1-ft#https://notifications.googleapis.com/email/t/AFG8qyVTEW5C8zaceBkyG53ARhpKYuBE-844cd-IMXqDIMttEqz_lxfXgpsANQ8HorWxcHOU45fLEdJX2uAC8S6Ea0UqhmQQsmE5_Pa_NFlu8eEJhmh6njTHj5yET2yGRGphMN_4BaLw-ai3fMWd6_z8kvwpH7ZzVyNq_ez_n4stt95GUtWksxxIQXcSYeivGen7_UUOd7qxm6PuhUK-JrEE4XwGR5ac9kfG9AKIsWYr/a.gif" width="1" class="CToWUd">
		            </div>';

		$TypeOfMail = "Verification";
		$mailer_result = SendEmail($message,$email,$TypeOfMail);
		if(!$mailer_result) { // send mail and check whether it succeed
		echo 'Message could not be sent.';
		} else { // if it does succeed
			$query = "INSERT INTO t_users VALUES ('$username','$password','$firstname','$lastname','$contact','$email','$confirm_code',DEFAULT)";
			if(mysqli_query($con,$query)){ //execute query
				echo "A Message has been Sent to your Email!" . mysqli_error($con); // inform if success
			}
			else{
				echo "Something Went Wrong" . mysqli_error($con); // inform if failed
			}
		}
	}


//forgot password
if(isset($_POST['forgotPassword'])){
	$email = $_POST['forgotPassword'];
	$query = "SELECT * FROM t_users WHERE email = '" .$email. "' ";
	$result = $con->query($query);
	if(mysqli_num_rows($result)==1){
		while($row = mysqli_fetch_assoc($result)){
			require_once 'EmailSender.php';
			$TypeOfMail = 'Account Information';

			$username = $row['username'];
		  $password = $row['password'];
		  $firstname = $row['firstname'];
		  $lastname = $row['lastname'];

			$message = '
		  <center style="width:100%;min-width:580px">

		      <table class="m_518678353464715935container" style="border-spacing:0;border-collapse:collapse;padding:0;vertical-align:top;text-align:inherit;width:580px;margin:0 auto">
		        <tbody><tr style="padding:0;vertical-align:top;text-align:center">
		          <td style="padding:0;vertical-align:top;text-align:center;color:#333333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:normal;margin:0;line-height:20px;font-size:14px;border-collapse:collapse!important">
		            <table class="m_518678353464715935row" style="border-spacing:0;border-collapse:collapse;padding:0px;vertical-align:top;text-align:center;width:100%;display:block">
		              <tbody><tr style="padding:0;vertical-align:top;text-align:center">
		                <td class="m_518678353464715935wrapper m_518678353464715935last" style="padding:0;vertical-align:top;text-align:center;color:#333333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:normal;margin:0;line-height:20px;font-size:14px;padding-right:0px;border-collapse:collapse!important">
		                  <div class="m_518678353464715935panel" style="background:#ffffff;background-color:#ffffff;border:1px solid #dddddd;padding:20px;border-radius:3px">
		                    <table class="m_518678353464715935twelve m_518678353464715935columns" style="border-spacing:0;border-collapse:collapse;padding:0;vertical-align:top;text-align:center;margin:0 auto;width:540px">
		                      <tbody><tr style="padding:0;vertical-align:top;text-align:center">
		                        <td style="padding:0px 0px 10px;vertical-align:top;text-align:center;color:#333333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:normal;margin:0;line-height:20px;font-size:14px;border-collapse:collapse!important">
		                          <div class="m_518678353464715935content">
		                            <h1 class="m_518678353464715935primary-heading" style="color:#333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:300;padding:0;margin:10px 0 25px;text-align:center;line-height:1;font-size:38px;letter-spacing:-1px">Account Information<br></h1>

		                            <div style="margin:30px;"></div>

		                            <h3 class="m_518678353464715935primary-heading" style="color:#333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:300;padding:0;margin:10px 0 25px;text-align:center;line-height:1;font-size:20px;letter-spacing:-1px">'.ucwords(strtolower($firstname)).' '.ucwords(strtolower($lastname)).'</h3>

		                            <div style="margin:30px;"></div>

		                            <p style="margin:0;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;color:#333333;line-height:20px;padding:0;text-align:center;margin-bottom:15px">We have received a request regarding the retrieval of your login account credentials. As requested, listed below are your account informations:</p>

		                            <div style="margin:60px;"></div>
		                            <div style="margin:60px;"></div>

		                            <table align="center">
		                              <tr style="margin:20px;">
		                                <td style="">
		                                  <img height="40px" width="40px" src="http://simpleicon.com/wp-content/uploads/user1-150x150.png">
		                                </td>
		                                <td><label style="color:grey; font-size: 1em;">Username :</label></td>
		                                <td style="border-bottom: solid 2px grey; font-size: 1.2em;">
		                                  '.$username.'

		                                </td>
		                              </tr>
		                              <tr style="margin:20px;">
		                                <td style="">
		                                  <img height="40px" width="40px" src="http://simpleicon.com/wp-content/uploads/lock-150x150.png">
		                                </td>
		                                <td><label style="color:grey; font-size: 1em;">Password :</label></td>
		                                <td style="border-bottom: solid 2px grey; font-size: 1.2em;">
		                                  '.$password.'
		                                </td>
		                              </tr>
		                            </table>

		                            <div style="margin:30px;"></div>

		                            <div style="margin:60px;"></div>
		                            </div></div></div>
		                          </div>

		                        </td>
		                        <td class="m_518678353464715935expander" style="padding:0!important;vertical-align:top;text-align:center;color:#333333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:normal;margin:0;line-height:20px;font-size:14px;width:0px;border-collapse:collapse!important">
		                        </td>
		                      </tr>
		                    </tbody></table>

		                  </div>

		                </td>
		              </tr>
		            </tbody></table>

		          </td>
		        </tr>
		      </tbody></table>

		      <table class="m_518678353464715935row m_518678353464715935layout-footer" style="border-spacing:0;border-collapse:collapse;padding:0px;vertical-align:top;text-align:center;width:100%">
		        <tbody><tr style="padding:0;vertical-align:top;text-align:center">
		          <td class="m_518678353464715935center" align="center" style="padding:0;vertical-align:top;text-align:center;color:#333333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:normal;margin:0;line-height:20px;font-size:14px;border-collapse:collapse!important">
		            <center style="width:100%;min-width:580px">
		              <table class="m_518678353464715935container" style="border-spacing:0;border-collapse:collapse;padding:0;vertical-align:top;text-align:inherit;width:580px;margin:0 auto">
		                <tbody><tr style="padding:0;vertical-align:top;text-align:center">
		                  <td class="m_518678353464715935wrapper m_518678353464715935last" style="padding:0;vertical-align:top;text-align:center;color:#333333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:normal;margin:0;line-height:20px;font-size:14px;padding-right:0px;border-collapse:collapse!important">
		                    <table class="m_518678353464715935twelve m_518678353464715935columns" style="border-spacing:0;border-collapse:collapse;padding:0;vertical-align:top;text-align:center;margin:0 auto;width:540px">
		                      <tbody><tr style="padding:0;vertical-align:top;text-align:center">
		                        <td style="padding:0px 0px 10px;vertical-align:top;text-align:center;color:#333333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:normal;margin:0;line-height:20px;font-size:14px;border-collapse:collapse!important">
		                          <div class="m_518678353464715935footer-links" style="padding:20px 0;text-align:center">
		                            <p class="m_518678353464715935footer-text" style="margin:0;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:12px;font-weight:normal;color:#999;line-height:20px;padding:0;text-align:center">
		                              You received this email to let you know about your account information due to a forgot password request.
		                          </p>
		                      </div>
		                      <div class="m_518678353464715935content" style="margin:0 0 15px 0">
		                        <a style="color:#4183c4;text-decoration:none" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://github.com&amp;source=gmail&amp;ust=1541489164144000&amp;usg=AFQjCNGq0Jq5gnH3BakjTgyPaCwD5yuPFQ">
		                          <img height="48" src="https://i.imgur.com/8V2u2oFl.png" style="width:48px;height:48px;" width="48" class="CToWUd">

		                        </a>
		                        </div>
		                        <div class="m_518678353464715935content" style="margin:0 0 15px 0">
		                          <p class="m_518678353464715935footer-text" style="margin:0;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:12px;font-weight:normal;color:#999;line-height:20px;padding:0;text-align:center">© 2018 Oversight</p>
		                          <p class="m_518678353464715935footer-text" style="margin:0;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:12px;font-weight:normal;color:#999;line-height:20px;padding:0;text-align:center"> Bulacan State University, Malolos City<br>Bulacan, Philippines</p>
		                        </div>
		                      </td>
		                      <td class="m_518678353464715935expander" style="padding:0!important;vertical-align:top;text-align:center;color:#333333;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-weight:normal;margin:0;line-height:20px;font-size:14px;width:0px;border-collapse:collapse!important">
		                      </td>
		                    </tr>
		                  </tbody></table>

		                </td>
		              </tr>
		            </tbody></table>

		          </center>
		        </td>
		      </tr>
		    </tbody></table>


		  </center>';
			$mailer_result = SendEmail($message,$email,$TypeOfMail);
			if(!$mailer_result) { // send mail and check whether it failed or succeed
				echo 'Message could not be sent.';
			} else { // if it does succeed
				echo "A Message has been Sent to your Email!"; // inform if success
			}
		}
	} else {
		echo "Something Went Wrong..." . mysqli_error($con);
	}
}
//-------------------------------------SETTINGS RELATED-----------------------------------------------------------

if(isset($_POST['getPrice'])){
	$priceQuery = "SELECT * FROM t_settings";
	$priceResult = $con->query($priceQuery);
	if(mysqli_num_rows($priceResult)==1){
		while($priceRow = mysqli_fetch_assoc($priceResult)){
			echo $priceRow["price"];
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
		$haslimit = 1;
		if($limit == 0){
			$haslimit = 0;
		}

		$query = "UPDATE t_appliance SET appl_name = '".$name."' , appl_name = '".$name."' , has_power_limit = ".$haslimit." , power_limit_value = ".$limit." WHERE uid = '".$id."'";

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
		$flag=0;
		$settingsQuery = "SELECT * FROM t_settings";
		$settingsResult = $con->query($settingsQuery);
		if(mysqli_num_rows($settingsResult)==1){
			while($settingsRow = mysqli_fetch_assoc($settingsResult)){
				if($settingsRow["socket"]=="true"){
					if($settingsRow["limitation"]=="true"){
						$query = "SELECT * FROM t_appliance WHERE uid = '". $uid ."'";
						$result = $con->query($query);
						if(mysqli_num_rows($result) == 1){
							while($row = mysqli_fetch_assoc($result)){
								if($row['appl_name']=="Anonymous_Appliance" || $row['appl_name']=="Unregistered_Appliance"){
									$date = strtotime($row['time_limit_value']);
									if(($row['has_time_limit']==1 && (time()<$date)) || ($row['time_limit_value'] == "0000-00-00 00:00:00")){
										$flag = 1;
									} else {
										$messageToClient = "Expired";
									}
								} else {
									if($row["has_power_limit"] == 1){
										if(($row['current_power_usage']/1000) >= $row['power_limit_value']){
											$messageToClient = "Overconsumed";
										} else {
											$flag = 1;
										}
									} else {
										$flag = 1;
									}
								}
							}
						} else {
							echo mysqli_error($con);
						}
					} else {
						$flag = 1;
					}
				} else {
					$messageToClient = "Socket is Off! Check Settings!";
				}
			}
		}



		if($flag == 1){
			$query2 = 'UPDATE t_appliance SET has_power = 1 where uid = "'. $uid .'"';
			if($con->query($query2)){
				echo 'Success ' . mysqli_error($con);
			} else {
				echo 'Failed ' . mysqli_error($con);
			}
		} else {
			echo $messageToClient ;
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

	if(isset($_POST["getPrice"])){
		$query = "SELECT price FROM t_settings";
		$result = $con->query($query);

		if(mysqli_num_rows($result)>0){
			while($r = mysqli_fetch_assoc($result)) {
		   echo $r['price'];
			}
		}
	}

//-----------------------------------------------------------------------------------------------------------------
?>
