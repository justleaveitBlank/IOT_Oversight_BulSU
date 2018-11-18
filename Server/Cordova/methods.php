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
		                                  <img height="40px" width="40px" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBmaWxsPSJub25lIiBkPSJNMCAwaDI0djI0SDBWMHoiLz48cGF0aCBvcGFjaXR5PSIuMyIgZD0iTTEyIDRjLTQuNDEgMC04IDMuNTktOCA4IDAgMS44Mi42MiAzLjQ5IDEuNjQgNC44MyAxLjQzLTEuNzQgNC45LTIuMzMgNi4zNi0yLjMzczQuOTMuNTkgNi4zNiAyLjMzQzE5LjM4IDE1LjQ5IDIwIDEzLjgyIDIwIDEyYzAtNC40MS0zLjU5LTgtOC04em0wIDljLTEuOTQgMC0zLjUtMS41Ni0zLjUtMy41UzEwLjA2IDYgMTIgNnMzLjUgMS41NiAzLjUgMy41UzEzLjk0IDEzIDEyIDEzeiIvPjxwYXRoIGQ9Ik0xMiAyQzYuNDggMiAyIDYuNDggMiAxMnM0LjQ4IDEwIDEwIDEwIDEwLTQuNDggMTAtMTBTMTcuNTIgMiAxMiAyek03LjA3IDE4LjI4Yy40My0uOSAzLjA1LTEuNzggNC45My0xLjc4czQuNTEuODggNC45MyAxLjc4QzE1LjU3IDE5LjM2IDEzLjg2IDIwIDEyIDIwcy0zLjU3LS42NC00LjkzLTEuNzJ6bTExLjI5LTEuNDVjLTEuNDMtMS43NC00LjktMi4zMy02LjM2LTIuMzNzLTQuOTMuNTktNi4zNiAyLjMzQzQuNjIgMTUuNDkgNCAxMy44MiA0IDEyYzAtNC40MSAzLjU5LTggOC04czggMy41OSA4IDhjMCAxLjgyLS42MiAzLjQ5LTEuNjQgNC44M3pNMTIgNmMtMS45NCAwLTMuNSAxLjU2LTMuNSAzLjVTMTAuMDYgMTMgMTIgMTNzMy41LTEuNTYgMy41LTMuNVMxMy45NCA2IDEyIDZ6bTAgNWMtLjgzIDAtMS41LS42Ny0xLjUtMS41UzExLjE3IDggMTIgOHMxLjUuNjcgMS41IDEuNVMxMi44MyAxMSAxMiAxMXoiLz48L3N2Zz4=">
		                                </td>
		                                <td><label style="color:grey; font-size: 1rem;">Username :</label></td>
		                                <td style="border-bottom: solid 2px grey;">
		                                  '.$username.'

		                                </td>
		                              </tr>
		                              <tr style="margin:20px;">
		                                <td style="">
		                                  <img height="40px" width="40px" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij48ZyBmaWxsPSJub25lIj48cGF0aCBkPSJNMCAwaDI0djI0SDBWMHoiLz48cGF0aCBvcGFjaXR5PSIuODciIGQ9Ik0wIDBoMjR2MjRIMFYweiIvPjwvZz48cGF0aCBvcGFjaXR5PSIuMyIgZD0iTTYgMjBoMTJWMTBINnYxMHptNi03YzEuMSAwIDIgLjkgMiAycy0uOSAyLTIgMi0yLS45LTItMiAuOS0yIDItMnoiLz48cGF0aCBkPSJNMTggOGgtMVY2YzAtMi43Ni0yLjI0LTUtNS01UzcgMy4yNCA3IDZ2Mkg2Yy0xLjEgMC0yIC45LTIgMnYxMGMwIDEuMS45IDIgMiAyaDEyYzEuMSAwIDItLjkgMi0yVjEwYzAtMS4xLS45LTItMi0yek05IDZjMC0xLjY2IDEuMzQtMyAzLTNzMyAxLjM0IDMgM3YySDlWNnptOSAxNEg2VjEwaDEydjEwem0tNi0zYzEuMSAwIDItLjkgMi0ycy0uOS0yLTItMi0yIC45LTIgMiAuOSAyIDIgMnoiLz48L3N2Zz4=">
		                                </td>
		                                <td><label style="color:grey; font-size: 1rem;">Password :</label></td>
		                                <td style="border-bottom: solid 2px grey;">
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
		                          <img height="48" src="data:image/png;base64,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" style="width:48px;height:48px;" width="48" class="CToWUd">

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
			echo "Success! " . $haslimit . mysqli_error($con);
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
										if($row['current_power_usage'] >= $row['power_limit_value']){
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