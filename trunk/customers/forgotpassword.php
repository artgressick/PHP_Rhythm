<?

	$BF = "../"; #This is the BASE FOLDER.  This should be located at the top of every page with the proper set of '../'s to find the root folder 
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
				header("Location: " . $BF . "customers/?chrEmail=" . urlencode($info['chrEmail']));
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




	include($BF. "customers/includes/meta.php");
	include($BF. "customers/includes/top.php");
?>	

<form id="form1" name="form1" method="post" action="">
<table width="300" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td>
      <div style='padding: 10px;'>
<? 
	foreach($error_messages as $er) { ?>
			<div class='ErrorMessage'><?=$er?></div>
<?
	}
	foreach($info_messages as $infomsg) { ?>
			<div class='InfoMessage'><?=$infomsg?></div>
<?
	}
	if(isset($_REQUEST['d'])) { // provide the form to actually change the password
?>
		<p>To change your password, enter your new password twice in the form below.<p>
        
        <p><span class="FormName">Password</span> <span class="FormRequired">(Required)</span> <br />
            <input name="chrPassword1" type="password" size="30" />
        
        <p><span class="FormName">(Confirm)</span> <span class="FormRequired">(Required)</span> <br />
            <input name="chrPassword2" type="password" size="30" maxlength="30" />
		</p>
        <p>
			<input type="submit" name="Submit" value="Submit" />&nbsp;&nbsp;<input type='button' value='Cancel' onclick='location.href="./";'>
			<input type="hidden" name="d" value="<?=$_REQUEST['d']?>" />
		</p>
<?
	} else {
?>
		<p>To request a new password, enter your email address in the form below. An email will be sent to your email address with instructions to change your password.</p>
        <p><span class="FormName">Email Address</span> <span class="FormRequired">(Required)</span> <br />
            <input name="chrEmail" type="text" size="30" value='<?=(isset($_REQUEST['chrEmail']) ? $_REQUEST['chrEmail'] : '')?>' />
		</p>
        <p>
			<input type="submit" name="Submit" value="Submit" />&nbsp;&nbsp;<input type='button' value='Cancel' onclick='location.href="./";'>
		</p>
<?
	}
?>
   	 </td>
	</tr>
</table>
<?
	include($BF. "customers/includes/bottom.php");
?>