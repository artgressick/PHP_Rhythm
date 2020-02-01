<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	if (isset($_POST['idGroups'])) {
	
		$table = 'Groups';

		$idGroups = explode(",", $_POST['idGroups']);
		
		$totalfail = 0;
		foreach ($idGroups as $id) {
		
			if(!db_query("UPDATE ".$table." SET intOrder='".$_POST['intOrder'.$id]."' WHERE ID=".$id,"Updating intOrder")) {
				$totalfail++;
			}
	
		}	
		
		if($totalfail == 0) {
			$_SESSION['InfoMessage'] = "The order has been successfully updated in the Database.";
		} else {
			$_SESSION['InfoMessage'] = "An error has occured while trying to save the order.";
		}
		
		header("Location: dis_groups.php");
		die();	
	
	}


	$title = 'Discussion Groups';	
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 

	$q = "SELECT ID, chrKEY, chrGroup, bShow, intOrder
		FROM Groups
		WHERE !bDeleted
		ORDER BY intOrder,chrGroup";
	$results = db_query($q,"getting IM Types");

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	

	$section = 'admin';
	$leftlink = "dis_groups";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Discussion Groups"; // Title of this page. (REQUIRED)
	$banner_icon = ""; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".mysqli_num_rows($results) ." results)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "This is a list of Discussion Groups."; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_admin.php');

	# This is the include file for the overlay to show that the delete is working.
	$TableName = "Groups"; #  This is the Database table that you will be setting the bDeleted statuses on.
	include($BF. 'includes/overlay.php');
?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>
<form name='idForm' id='idForm' action='' method="post">		
	<table cellpadding="0" cellspacing="0" border="0" class="optionsTop">
<?
	optionsBar();
?> 
	</table>	
	<table class='List' id='List' style='width: 100%;'  cellpadding="0" cellspacing="0">
		<tr>
			<th>Group Name</th>
			<th class='options'>Order</th>
			<th class='options'>Show/Hide</th>
			<th class='options'>Edit</th>
			<th class='options'><img src="<?=$BF?>images/options.gif"></th>
		</tr>
<? $count=0;
	$idGroups = array();
	while ($row = mysqli_fetch_assoc($results)) { 
		$idGroups[] = $row['ID'];
		$link = 'window.location.href="dis_editgroup.php?key='.$row['chrKEY'].'"'; 
?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
				onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				<td onclick='<?=$link?>'><?=$row['chrGroup']?></td>
				<td class="NoCursor"><input type="text" size="3" name="intOrder<?=$row['ID']?>" id="intOrder<?=$row['ID']?>" value="<?=$row['intOrder']?>" /></td>
				<td onclick="showHide('<?=$BF?>',<?=$row['ID']?>,'<?=$TableName?>',document.getElementById('bShow<?=$row['ID']?>').value,<?=$_SESSION['idPerson']?>,'<?=$row['chrKEY']?>');" align="center" />
					<input type="hidden" name="bShow<?=$row['ID']?>" id="bShow<?=$row['ID']?>" value="<?=$row['bShow']?>" />
					<span id='bShowTD<?=$row['ID']?>' style="color:blue; text-decoration:underline;"><?=($row['bShow']==1?"Shown":"Hidden")?></span>
				</td>
				<td class='options'><a title="Edit <?=$row['chrGroup']?>" href='dis_editgroup.php?key=<?=$row['chrKEY']?>'><img src='<?=$BF?>images/edit.png' alt='edit' /></a></td>
				<td class='options'><?=deleteButton($row['ID'],$row['chrGroup'],$row['chrKEY'])?></td>
			</tr>
<?	} 
if($count == 0) { ?>
			<tr>
				<td align="center" colspan='5' height="20">No Groups have been created at this time.</td>
			</tr>
<?	} ?>
	</table>
<? if ($count != 0) { ?>
	<input type="hidden" id="idGroups" name="idGroups" value="<?=implode(',',$idGroups)?>" />
<? } ?>
	<table cellpadding="0" cellspacing="0" border="0" class="optionsBottom">
<?
	optionsBar();
?> 
	</table>
</form>


<?	include($BF .'includes/bottom.php'); 

function optionsBar() {
global $BF;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="addbutton"><a title="Add Group" href="dis_addgroup.php"><img src="<?=$BF?>images/plus_add.gif" alt="Add Discussion Group" /></a></td>
			<td class="right"><input type="submit" name="submit" id="submit" value="Save Order" /></td>			
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}
?>