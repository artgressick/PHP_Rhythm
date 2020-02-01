<?

	$BF = ""; #This is the BASE FOLDER.  This should be located at the top of every page with the proper set of '../'s to find the root folder 
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	$auth_not_required = true;
	require($BF .'_lib.php');

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
		
			$q = "SELECT * FROM People WHERE chrLostPassword='" . $_POST['d'] . "' AND !bDeleted";
			$info = db_query($q, 'Find Account',1);

			if($info['ID'] == "") {
	
				$_SESSION['ErrorMessage'][] = 'The Lost Password Request you clicked was invalid.  Please fill out this form to send a new Request.'; 
				header("Location: forgotpassword.php");
				exit();

			} else {
				// make sure it isn't over 25 hours old (give them extra time to fill in the form)
				if(strtotime($info['dtLostPassword']) < strtotime('NOW-25 HOURS')) {
					$_SESSION['ErrorMessage'][] = 'The Lost Password Request you clicked has expired (it must be used within 24 hours).  Please fill out this form to send a new Request.'; 
					header("Location: forgotpassword.php?chrEmail=" . $info['chrEmail']);
					exit();
				}
			}

			if ($_POST['chrPassword1'] == "") {
				$error_messages[] = "You must enter a New Password."; 
			} else if($_POST['chrPassword1'] != $_POST['chrPassword2']) {
				$error_messages[] = "The passwords you entered do not match."; 
			} else {
	
				$q = "UPDATE People SET chrPassword='" . sha1($_POST['chrPassword1']) . "', chrLostPassword=NULL WHERE chrLostPassword='" . $_POST['d'] . "' AND !bDeleted";
				$result = db_query($q, 'change password');
		
				$_SESSION['InfoMessage'][] = 'Your password has been changed.'; 
				header("Location: " . $BF . "./?chrEmail=" . urlencode($info['chrEmail']));
				exit();
			}
		
		} else { // if this is a submission of the first form
			$q = "SELECT ID, chrKEY, chrFirst, chrLast, chrEmail,
							(SELECT SA.ID FROM SiteAccess SA WHERE SA.idPerson=People.ID LIMIT 1) AS idSiteAccess,
							(SELECT CA.ID FROM CustomerAccess CA WHERE CA.idPerson=People.ID LIMIT 1) AS idCustomerAccess
						FROM People 
						WHERE chrEmail='". $_POST['chrEmail'] ."'";
			$info = db_query($q, 'Get Account Info',1);
	
			if($info['ID'] != "" || $info['idSiteAccess'] != "" || $info['idCustomerAccess'] != "") {
				// create a password change request
				$special = makekey();
				$q = "UPDATE People SET chrLostPassword='" . $special . "', dtLostPassword=NOW() WHERE ID='" . $info['ID'] . "'";
				$result = db_query($q, 'Set Forgot Password Information');
	
				//send the user their password
	
				$info['chrSubject'] = "Forgot Your Password? - Rhythm Business Portal";
	
				$info['txtMsg'] = "<p>Someone (hopefully you!) notified us that you have forgotten your password to the Rhythm Business Portal Website.</p>
								<p>To change your password, click the following link (or copy and paste it into your browser's address bar):</p>
								<p><a href='".$_SERVER['SCRIPT_URI']."?d=". $special ."'>".$_SERVER['SCRIPT_URI']."?d=". $special ."</a></p>
								<p>Please note, you must use this link in the next 24 hours or it will be disabled, and you will have to place another Forgot Password Request.</p>";
	
				// Send E-mail
				include($BF.'includes/emailer.php');				
	
				$_SESSION['InfoMessage'][] = 'An email has been sent to you with instructions to change your account password.';
				header("Location: ./");
				exit();				
			} else {
				$error_messages[] = "There is no account with that email address.";
			}
		}
	}	




	include($BF ."includes/meta.php");
?>	

<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
body {
	background-color: #FFFFFF;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style2 {font-size: 10px}
-->
</style>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
	<tr>
		<td valign="middle">
			<form name="form1" method="post" action="">
			<table width="510" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="<?=$BF?>images/login_top.gif" width="510" height="167"></td>
				</tr>
				<tr>
					<td background="<?=$BF?>images/login_bg.gif"><table width="100%" border="0" cellspacing="0" cellpadding="5">
						<tr>
							<td height="10"></td>
						</tr>
						<tr>
							<td align="center">
								<table border="0" cellspacing="0" cellpadding="5">
									<tr>
										<td colspan="2" width="300">
<? 	foreach($error_messages as $error) { ?>
											<div class='ErrorMessage'><?=$error?></div>
<?	} ?>
<? 	foreach($info_messages as $infomsg) { ?>
											<div class='InfoMessage'><?=$infomsg?></div>
<?	} ?>
										</td>
									</tr>

<?
	if(isset($_REQUEST['d'])) { // provide the form to actually change the password
?>
								<tr>
									<td colspan="2" width="300">To change your password, enter your new password twice in the form below.</td>
								</tr>
								<tr>
									<td><strong>Your New Password</strong></td>
									<td><input name="chrPassword1" type="password" id="chrPassword1" size="30"></td>
								</tr>
								<tr>
									<td><strong>(Confirm)</strong></td>
									<td><input name="chrPassword2" type="password" id="chrPassword2" size="30" ></td>
								</tr>
								<tr>
									<td align="right">&nbsp;</td>
									<td align="right"><input type="submit" name="button" id="button" value="Change My Password">&nbsp;&nbsp;<input type='button' value='Cancel' onclick='location.href="./";'></td>
								</tr>
								<input type="hidden" name="d" value="<?=$_REQUEST['d']?>" />

<?
	} else {
?>

								<tr>
									<td colspan="2" width="300">To request a new password, enter your email address in the form below. An email will be sent to your email address with instructions to change your password.</td>
								</tr>
								<tr>
									<td><strong>Email Address</strong></td>
									<td><input name="chrEmail" type="text" id="chrEmail" size="30" value='<?=(isset($_REQUEST['chrEmail']) ? $_REQUEST['chrEmail'] : '')?>'></td>
								</tr>
								<tr>
									<td align="right">&nbsp;</td>
									<td align="right"><input type="submit" name="button" id="button" value="Submit Request">&nbsp;&nbsp;<input type='button' value='Cancel' onclick='location.href="./";'></td>
								</tr>
<?
	}
?>
							</table></td>
							</tr>
											
						<tr>
							<td align="center"><span class="style2">Copyright &copy; 2000-<?=date('Y')?> techIT Solutions, Inc. All rights reserved.</span></td>
						</tr>

					</table></td>
				</tr>
				<tr>
					<td><img src="<?=$BF?>images/login_bottom.gif" width="510" height="8"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
</body>
</html>

