<?php
	$BF = "../"; // Lets set the Base Folder so we can get back to the root
	$auth_not_required = true;
	require_once($BF . "_lib.php"); // Grab the lib file with most of functions in it

	if(isset($_SESSION['ErrorMessage']) && count($_SESSION['ErrorMessage'])) { 
		$error_messages = $_SESSION['ErrorMessage'];
		unset($_SESSION['ErrorMessage']);		
	} else { $error_messages = array(); }

	if(isset($_SESSION['InfoMessage']) && count($_SESSION['InfoMessage'])) { 
		$info_messages = $_SESSION['InfoMessage'];
		unset($_SESSION['InfoMessage']);		
	} else { $info_messages = array();}
	
	if(count($_POST)) {
		if(isset($_POST['d'])) { // if this was a submission of the final form
			parse_str(base64_decode($_POST['d']), $data);

			$query = "SELECT * FROM People WHERE chrEmail='" . $data['chrEmail'] . "'  AND !bDeleted AND chrLostPassword='" . $data['special'] . "'";
			$result = db_query($query, 'find account');
			$row = mysqli_fetch_assoc($result);

			if($row === false) {
				// if they gave the wrong special, clean the account of any special that exists.
				// this is a safeguard from crackers
				$query = "UPDATE People SET chrLostPassword=NULL WHERE chrEmail='" . $data['chrEmail'] . "' AND !bDeleted";
				$result = db_query($query, 'clear account');

				$_SESSION['ErrorMessage'][] = 'The Lost Password Request you clicked was invalid.  Please fill out this form to send a new Request.'; 
				header("Location: lostpassword.php?chrEmail=" . $data['chrEmail']);
				exit();

			} else {
				// make sure it isn't over 25 hours old (give them extra time to fill in the form)
				if(strtotime($row['dtLostPassword']) < strtotime('NOW-25 HOURS')) {
					$_SESSION['ErrorMessage'][] = 'The Lost Password Request you clicked has expired (it must be used within 24 hours).  Please fill out this form to send a new Request.'; 
					header("Location: lostpassword.php?chrEmail=" . $data['chrEmail']);
					exit();
				}
			}

			if($_POST['chrPassword1'] != $_POST['chrPassword2']) {
				$error_messages[] = "The passwords you entered do not match."; 
			} else {
	
				$query = "UPDATE People SET chrPassword=sha1('" . $_POST['chrPassword1'] . "'), chrLostPassword=NULL WHERE chrEmail='" . $data['chrEmail'] . "' AND !bDeleted";
				$result = db_query($query, 'change password');
		
				$_SESSION['InfoMessage'][] = 'Your password has been changed.'; 
				header("Location: " . $BF . "./?chrEmail=" . urlencode($data['chrEmail']));
				exit();
			}

		} else { // if this is a submission of the first form

			$query = "SELECT * FROM People WHERE chrEmail='" . $_POST['chrEmail'] . "' AND !bDeleted";
			$result = db_query($query, 'get account');
			$row = mysqli_fetch_assoc($result);
	
			if($row) {
				// create a password change request
				$special = mt_rand(100000000, 9999999999);
				$query = "UPDATE People SET chrLostPassword='" . $special . "', dtLostPassword=NOW() WHERE ID='" . $row['ID'] . "'";
				$result = db_query($query, 'create special');
	
				//send the user their password
				$Headers = "From: techIT Rhythm Portal <admin@techitsolutions.com>\r\n";
	
				$Subject = "Forgot Your Password?";
	
				$Message = "Someone (hopefully you!) notified us that you have forgotten your password to the Rhythm Portal.\n\n";
				$Message .= "To change your password, click the following link (or copy and paste it into your browser's address bar):\n\n";
				$Message .= "http://assets.itechit.com/lostpassword.php?d=" . base64_encode("special=" . $special . "&chrEmail=" . urlencode($_REQUEST['chrEmail'])) . "\n\n";
				$Message .= "Please note, you must use this link in the next 24 hours or it will be disabled, and you will have to place another Lost Password Request.\n";
	
				mail($_POST['chrEmail'], $Subject, $Message, $Headers);
	
				$_SESSION['InfoMessage'][] = 'An email has been sent to you with instructions to change your account password.';
				header("Location: ". $BF ."includes/lostpassword.php");
				exit();				
			} else {
				$error_messages[] = "There is no account with that email address.";
			}
		}
	}

	include($BF ."includes/meta.php");
?>
		<link href="<?=$BF?>includes/oneshots.css" rel="stylesheet" type="text/css">
	</head>
<body>


<table style='width: 100%;'>
	<tr>
		<td>

			<table cellpadding="0" cellspacing="0" class='logoutframe' align='center'>
				<tr>
					<td class='topcorner'><img src="<?=$BF?>images/topleftblack.gif"></td>
					<td>
						<div class='middletext header1'>Asset Management Enterprise Portal</strong></div>
					</td>
					<td class='topcorner'><img src="<?=$BF?>images/toprightblack.gif"></td>
				</tr>
				<tr>
					<td class='left'></td>
					<td class='middle'>
						<div class='header1' style='margin: 10px 0 20px;'>Forgot My Password</div>
		
<? 	foreach($error_messages as $error) { ?>
						<div class='ErrorMessage'><?=$error?></div>
<?	} ?>
<? 	foreach($info_messages as $infomsg) { ?>
						<div class='InfoMessage'><?=$infomsg?></div>
<?	} ?>
		
					
<?	if(isset($_REQUEST['d'])) { // provide the form to actually change the password
?>
						<div style='margin-bottom: 10px;'>To change your password, enter your new password twice in the form below.	</div>
					
						<form action='' method='post'>
		
							<div class='FormName'>Your New Password</div>
							<div class='FormField'><input name="chrPassword1" type="password" size="30" /></div>
						
							<div class='FormName'>(Confirm)</div>
							<div class='FormField'><input name="chrPassword2" type="password" size="30" /></div>
						
							<div>
								<input type='hidden' name='d' value='<?=$_REQUEST['d']?>' />
								<input type="submit" value="Change My Password" />
								<input type='button' value='Cancel' onclick='location.href="./";'>
							</div>
		
						</form>
					
					
<?	} else { // provide the form to submit request
?>
						<div style='margin-bottom: 10px;'>To request a new password, enter your email address in the form below.  An email will be sent to your email address with instructions to change your password.</div>
					
						<form action='' method='post'>
							<div class='FormName'>Your Email Address <span class="FormRequired">(Required)</span></div>
							<div class='FormField'><input name="chrEmail" type="text" size="40" maxlength="50"<?=(isset($_REQUEST['chrEmail'])?' value="' . $_REQUEST['chrEmail'] . '"':'')?> /></div>
							
							<div>
								<input type="submit" value="Submit Request" />
								<input type='button' value='Cancel' onclick='history.go(-1)'>
							</div>
						</form>
					
						</div>
<?	} ?>
		
					</td>
					<td class='right'></td>
				</tr>
				<tr> 
					<td class='bottomcorner'><img src="<?=$BF?>images/bottomleftblack.gif"></td>
					<td class='middletext'>
						<div style='font-size: 10px;'>
							Powered by Corporate Business Intelligence&reg;. Copyright &copy; <?=date('Y')?> techIT Solutions, LLC
						</div>
					<td class='bottomcorner'><img src="<?=$BF?>images/bottomrightblack.gif"></td>
				</tr>
			</table>
        
		</td>
	</tr>
</table>



</body>
</html>
