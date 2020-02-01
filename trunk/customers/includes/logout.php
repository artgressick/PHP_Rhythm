<?
	$BF = "../../";
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
						<div class='middletext header1'>Rhythm Business Portal</strong></div>
					</td>
					<td class='topcorner'><img src="<?=$BF?>images/toprightblack.gif"></td>
				</tr>
				<tr>
					<td class='left'></td>
					<td class='middle'>
						<div class='header1' style='margin: 10px 0 20px;'>Logged Off</div>
						
							<div class='bottom10'>You are now logged off the Rhythm Business Portal. &nbsp;Possible reasons this has occurred may be:</div>
							<div class='bottom10'><span class='pointers'>>></span>You clicked on the logoff link.</div>
							<div class='bottom10'><span class='pointers'>>></span>The link between our webserver and your browser has been broken. </div>
							<div><span class='pointers'>>></span>You have stayed on the same page for longer than 20 minutes.</div>
							<div style='margin-top: 20px; text-align: center'><a href="<?=$BF?>customers/index.php">Please click here to log in again</a>.</div>
		
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