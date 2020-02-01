<?
	$BF = "../";
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
						<div class='header1' style='margin: 10px 0 20px;'>Locked Account</div>
						
							<div class='bottom10'>Your account has been blocked from logging in. &nbsp;Possible reasons this has occurred may be:</div>
							<div class='bottom10'><span class='pointers'>>></span>Too many failed attempts.</div>
							<div class='bottom10'><span class='pointers'>>></span>An administrator has locked you out for security reasons. </div>
							<div style='margin-top: 20px; text-align: center'>Please contact an Administrator or click <a href='<?=$BF?>includes/lostpassword.php'>here</a> to get a new password.</div>
		
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
