<?
	if (!isset($BF)) { $BF = "../"; }
	$title = "Login Page";
	
	if(isset($_SESSION['ErrorMessage']) && count($_SESSION['ErrorMessage'])) { 
		$error_messages = $_SESSION['ErrorMessage'];
		unset($_SESSION['ErrorMessage']);		
	} else { $error_messages = array(); }

	if(isset($_SESSION['InfoMessage']) && count($_SESSION['InfoMessage'])) { 
		$info_messages = $_SESSION['InfoMessage'];
		unset($_SESSION['InfoMessage']);		
	} else { $info_messages = array();}
	
	
	include($BF. "customers/includes/meta.php");
	include($BF. "customers/includes/top.php");
?>
<form id="form1" name="form1" method="post" action="">
<table width="300" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td>
      <div style='padding: 10px;'>
<?
	foreach($info_messages as $infomsg) { ?>
		<div class='infoMessage'><?=$infomsg?></div>
<?
	}
	foreach($error_messages as $er) { ?>
		<div class='ErrorMessage'><?=$er?></div>
<?
	}
?>
        <p><span class="FormName">Email Address</span> <span class="FormRequired">(Required)</span> <br />
            <input name="auth_form_name" type="text" size="30" maxlength="35" value='<?=(isset($_REQUEST['chrEmail']) && $_REQUEST['chrEmail'] != "" ? $_REQUEST['chrEmail'] : 
											(isset($_REQUEST['auth_form_name']) ? $_REQUEST['auth_form_name'] : ''))?>' />
		</p>
        <p><span class="FormName">Password</span> <span class="FormRequired">(Required)</span> <br />
            <input name="auth_form_password" type="password" size="30" maxlength="30" />
		</p>
        <p>
			<input type="submit" name="Submit" value="Submit" />
		</p>
        <p class="FormRequired"><a href="<?=$BF?>customers/forgotpassword.php">Did you forget your password? Send me a new password</a><br />Problems? Contact The Administrator</p>
    	 </td>
	</tr>
</table>
<?
	include($BF. "customers/includes/bottom.php");
?>