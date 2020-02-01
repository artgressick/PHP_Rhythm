<?
	$BF = "../";
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
</style></head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
	<tr>
		<td valign="middle"><form name="form1" method="post" action="">
			<table width="510" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="<?=$BF?>images/login_top.gif" width="510" height="167"></td>
				</tr>
				<tr>
					<td background="<?=$BF?>images/login_bg.gif"><table border="0" cellspacing="0" cellpadding="5">
						<tr>
							<td height="10"></td>
						</tr>
						<tr>
							<td>
								<div style="font-size:14px; padding-left:10px; font-weight:bold;">Logged Off</div>
										
								<div style="padding: 10 10 0 30;">You are now logged off the Rhythm Business Portal. &nbsp;Possible reasons this has occurred may be:</div>
								<div style="padding: 0 10 10 30;">
									<ul style="line-height:18px;">
										<li>You clicked on the logoff link.</li>
										<li>The link between our webserver and your browser has been broken.</li>
										<li>You have stayed on the same page for longer than 20 minutes.</li>
									</ul>
								</div>
								<div style='padding: 0 0 20 0; text-align: center'><a href="<?=$BF?>index.php">Please click here to log in again</a>.</div>
							</td>
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

