<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	if (isset($_POST['idTopics'])) {
	
		$table = 'Topics';

		$idTopics = explode(",", $_POST['idTopics']);
		
		$totalfail = 0;
		foreach ($idTopics as $id) {
		
			if(!db_query("UPDATE ".$table." SET intOrder='".$_POST['intOrder'.$id]."' WHERE ID=".$id,"Updating intOrder")) {
				$totalfail++;
			}
	
		}	
		
		if($totalfail == 0) {
			$_SESSION['InfoMessage'] = "The order has been successfully updated in the Database.";
		} else {
			$_SESSION['InfoMessage'] = "An error has occured while trying to save the order.";
		}
		
		header("Location: dis_topics.php");
		die();	
	
	}


	$title = 'Discussion Topics';	
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 

	$q = "SELECT T.ID, T.chrKEY, chrTopic, T.bShow, T.intOrder, G.chrGroup
		FROM Groups G
		JOIN Topics T ON G.ID=T.idGroup
		WHERE !G.bDeleted AND !T.bDeleted
		ORDER BY G.intOrder, G.chrGroup, T.intOrder,T.chrTopic";
	$results = db_query($q,"getting IM Types");

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	

	$section = 'admin';
	$leftlink = "dis_topics";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Discussion Topics"; // Title of this page. (REQUIRED)
	$banner_icon = ""; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".mysqli_num_rows($results) ." results)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "This is a list of Discussion Topics."; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_admin.php');

	# This is the include file for the overlay to show that the delete is working.
	$TableName = "Topics"; #  This is the Database table that you will be setting the bDeleted statuses on.
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
			<th>Topic Name</th>
			<th class='options'>Order</th>
			<th class='options'>Show/Hide</th>
			<th class='options'>Edit</th>
			<th class='options'><img src="<?=$BF?>images/options.gif"></th>
		</tr>
<? $count=0;
	$idTopics = array();
	$groupname="";
	while ($row = mysqli_fetch_assoc($results)) { 
		$idTopics[] = $row['ID'];
		$link = 'window.location.href="dis_edittopic.php?key='.$row['chrKEY'].'"'; 
		if($groupname != $row['chrGroup']) {
			$groupname = $row['chrGroup'];
?>
			<tr id='tr<?=$count?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' >
				<td colspan="5"><strong><?=$row['chrGroup']?></strong></td>
			</tr>
<?
		}
?>
			<tr id='tr<?=$count?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
				onmouseover='RowHighlight("tr<?=$count-1?>");' onmouseout='UnRowHighlight("tr<?=$count-1?>");'>
				<td onclick='<?=$link?>'>&nbsp;&nbsp;&nbsp;<?=$row['chrTopic']?></td>
				<td class="NoCursor"><input type="text" size="3" name="intOrder<?=$row['ID']?>" id="intOrder<?=$row['ID']?>" value="<?=$row['intOrder']?>" /></td>
				<td onclick="showHide('<?=$BF?>',<?=$row['ID']?>,'<?=$TableName?>',document.getElementById('bShow<?=$row['ID']?>').value,<?=$_SESSION['idPerson']?>,'<?=$row['chrKEY']?>');" align="center" />
					<input type="hidden" name="bShow<?=$row['ID']?>" id="bShow<?=$row['ID']?>" value="<?=$row['bShow']?>" />
					<span id='bShowTD<?=$row['ID']?>' style="color:blue; text-decoration:underline;"><?=($row['bShow']==1?"Shown":"Hidden")?></span>
				</td>
				<td class='options'><a title="Edit <?=$row['chrTopic']?>" href='dis_edittopic.php?key=<?=$row['chrKEY']?>'><img src='<?=$BF?>images/edit.png' alt='edit' /></a></td>
				<td class='options'><?=deleteButton($row['ID'],$row['chrTopic'],$row['chrKEY'])?></td>
			</tr>
<?	} 
if($count == 0) { ?>
			<tr>
				<td align="center" colspan='5' height="20">No Topics have been created at this time.</td>
			</tr>
<?	} ?>
	</table>
<? if ($count != 0) { ?>
	<input type="hidden" id="idTopics" name="idTopics" value="<?=implode(',',$idTopics)?>" />
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
			<td class="addbutton"><a title="Add Topic" href="dis_addtopic.php"><img src="<?=$BF?>images/plus_add.gif" alt="Add Discussion Topic" /></a></td>
			<td class="right"><input type="submit" name="submit" id="submit" value="Save Order" /></td>			
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}
?>