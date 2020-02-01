</head>
<body <?=(isset($bodyParams) ? 'onload="'. addslashes($bodyParams) .'"' : '')?>>

 <table width="908" border="0" align="center" cellpadding="0" cellspacing="0">
 	<tr>
		<td colspan="3"><a href='<?=$BF?>customers/index.php' title='link to the main page'><img src="<?=$BF?>customers/images/general-logo.gif" width="309" height="200" /><img src="<?=$BF?>customers/images/general-main1.jpg" width="599" height="200" /></a></td>
	</tr>
    <tr>
    	<td width="4" background="<?=$BF?>customers/images/shadow-left.gif"><img src="<?=$BF?>customers/images/shadow-left.gif" width="4" height="5" /></td>
      	<td width="900" bgcolor="#ffffff">
	  
<!--This is the log in bar which will be dynamic -->
	  		<table width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#f1f1f1" style='background: url("<?=$BF?>customers/images/smoothbar.gif") repeat-x;'>
          		<tr>
					<td width="20"><img src="<?=$BF?>customers/images/smoothbar_arrow.gif" /></td>
					<td<?=(!isset($_SESSION['idCustomer'])?' style="display:none;"':"")?>>

						<span class="loginbar" style=''>Welcome <?=(isset($_SESSION['chrFirst']) ? $_SESSION['chrFirst'] : "")?> <?=(isset($_SESSION['chrLast']) ? $_SESSION['chrLast'] : "")?></span>

					</td>
					<td align="right" nowrap="nowrap"<?=(!isset($_SESSION['idCustomer'])?' style="display:none;"':"")?>>
						<div class="navstyle" id="nav">
							<ul>
								<li><a href="<?=$BF?>customers/index.php">Work Orders</a></li>

<? // Old Nav Item								<li><a href="#" id="id-dropmenu1" rel="dropmenu1">Work Orders</a></li> ?>
							</ul>
						</div>
						<!--1st drop down menu -->                                               
<?
/*
						<div id="dropmenu1" class="dropmenudiv">
							<ul>
								<li><a href="<?=$BF?>customers/index.php">Work Orders</a></li>
							</ul>
						</div>
					<script type="text/javascript">dropdown.startnav("nav")</script>
*/
?>
            		</td>
            		<td width="70"<?=(!isset($_SESSION['idCustomer'])?' style="display:none;"':"")?>> | &nbsp;&nbsp; <a href="index.php?logout=2">Log Out</a></td>
        		</tr>
			</table>		
<!-- this is the end of the login bar -->
<!-- this is the main section of the site and will be used to modify -->
			<table cellspacing="0" cellpadding="0" border="0" width="100%" style="padding:10px;">
				<tr>
              		<td>