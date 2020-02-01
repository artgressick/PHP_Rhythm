<?
	if (!isset($BF)) { $BF = ""; }
	if(isset($_SESSION['ErrorMessage']) && count($_SESSION['ErrorMessage'])) { 
		$error_messages = $_SESSION['ErrorMessage'];
		unset($_SESSION['ErrorMessage']);		
	} else { $error_messages = array(); }

	if(isset($_SESSION['InfoMessage']) && count($_SESSION['InfoMessage'])) { 
		$info_messages = $_SESSION['InfoMessage'];
		unset($_SESSION['InfoMessage']);		
	} else { $info_messages = array();}

	if(!isset($_REQUEST['auth_form_name'])) { 
		if(isset($_COOKIE['rhythmlogin']) && $_COOKIE['rhythmlogin'] != '') { $_REQUEST['auth_form_name'] = $_COOKIE['rhythmlogin']; }
	}

	$title = "Login Page";
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
		<td valign="middle"><form name="form1" method="post" action="">
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
							<td align="center"><table border="0" cellspacing="0" cellpadding="5">
									<tr>
										<td colspan="2" width="300">
<? 	foreach($error_messages as $error) { ?>
											<div class='ErrorMessage'><?=$error?></div>
<?	} ?>
<? 	foreach($info_messages as $infomsg) { ?>
											<div class='infoMessage'><?=$infomsg?></div>
<?	} ?>
										</td>
									</tr>
<? 	if(isset($error_message) && $error_message != "") { ?>
								<tr>
									<td colspan="2">
										<div class='ErrorMessage'><?=$error_message?></div>
									</td>
								</tr>
<?	} ?>
							
								<tr>
									<td><strong>Email Address</strong></td>
									<td><input name="auth_form_name" type="text" id="auth_form_name" size="30" value='<?=(isset($_REQUEST['chrEmail']) && $_REQUEST['chrEmail'] != "" ? $_REQUEST['chrEmail'] : 
											(isset($_REQUEST['auth_form_name']) ? $_REQUEST['auth_form_name'] : ''))?>'></td>
								</tr>
								<tr>
									<td align="right"><strong>Password</strong></td>
									<td><input name="auth_form_password" type="password" id="auth_form_password" size="30"></td>
								</tr>
								<tr>
									<td align="right">&nbsp;</td>
									<td align="right"><input type="submit" name="button" id="button" value="Log In Now"></td>
								</tr>
							</table></td>
							</tr>
						
						<tr>
							<td height="35" align="center"><a href="<?=$BF?>forgotpassword.php">Did you forget your password? Send me a new password</a></td>
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

