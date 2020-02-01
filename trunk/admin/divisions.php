<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	$title = 'Divisions';	
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrDivision"; } # This sets the default column order.  Asc by default.

	$q = "SELECT ID, chrKEY, chrDivision, chrPrefix
		FROM Divisions
		WHERE !bDeleted
		ORDER BY ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];
	$results = db_query($q,"getting customers");

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	

	$section = 'admin';
	$leftlink = "divisions";
	include($BF .'includes/top.php');

	// Banner Information
	$banner_title = "Divisions"; // Title of this page. (REQUIRED)
	$banner_icon = ""; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".mysqli_num_rows($results) ." results)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "This is a list of division within your company."; // Instructions or description. (NOT REQUIRED)
	include($BF .'includes/left_admin.php');

	# This is the include file for the overlay to show that the delete is working.
	$TableName = "Divisions"; #  This is the Database table that you will be setting the bDeleted statuses on.
	include($BF. 'includes/overlay.php');
?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>		

	<table cellpadding="0" cellspacing="0" border="0" class="optionsTop">
<?
	optionsBar();
?> 
	</table>		
	<table class='List' id='List' style='width: 100%;'  cellpadding="0" cellspacing="0">
		<tr>
			<? sortList('Division Name', 'chrDivision'); ?>
			<? sortList('Prefix', 'chrPrefix'); ?>
			<th class='options'>Edit</th>
			<th class='options'><img src="<?=$BF?>images/options.gif"></th>
		</tr>
<? $count=0;	
	while ($row = mysqli_fetch_assoc($results)) { 
		$link = 'window.location.href="editdivision.php?key='.$row['chrKEY'].'"'; 
?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
				onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				<td onclick='<?=$link?>'><?=$row['chrDivision']?></td>
				<td onclick='<?=$link?>'><?=$row['chrPrefix']?></td>
				<td class='options'><a title="Edit <?=$row['chrDivision']?>" href='editdivision.php?key=<?=$row['chrKEY']?>'><img src='<?=$BF?>images/edit.png' alt='edit' /></a></td>
				<td class='options'><?=deleteButton($row['ID'],$row['chrDivision'],$row['chrKEY'])?></td>
			</tr>
<?	} 
if($count == 0) { ?>
			<tr>
				<td align="center" colspan='3' height="20">No Divisions have been created at this time.</td>
			</tr>
<?	} ?>
	</table>
	<table cellpadding="0" cellspacing="0" border="0" class="optionsBottom">
<?
	optionsBar();
?> 
	</table>
<?	include($BF .'includes/bottom.php');

function optionsBar() {
global $BF;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="addbutton"><a title="Add Division" href="adddivision.php"><img src="<?=$BF?>images/plus_add.gif" alt="Add Division" /></a></td>
			<td class="left">&nbsp;</td>			
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}
?>