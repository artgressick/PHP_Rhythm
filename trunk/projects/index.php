<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	$title = 'Projects';	
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrProject"; $_REQUEST['ordCol'] = "DESC"; } # This sets the default column order.  Asc by default.

	$q = "SELECT projects.ID, projects.chrKEY, chrProject, chrFirst, chrLast
		FROM Projects
		JOIN People ON Projects.idPerson = People.ID 
		WHERE !Projects.bDeleted ";
	
		
	$q .= "ORDER BY ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];
	
	$results = db_query($q,"getting Projects");
?>
	<script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script>
<?	

	$section = 'projects';
	$leftlink = "viewprojects";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Projects"; // Title of this page. (REQUIRED)
	$banner_icon = "projects/images/icons-project.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".mysqli_num_rows($results)." results)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "Choose a project from the list below.  Click on the column header to sort the list by that column."; // Instructions or description. (NOT REQUIRED)
	include($BF .'projects/includes/left_project.php');

	# This is the include file for the overlay to show that the delete is working.
	$TableName = "Quotes"; #  This is the Database table that you will be setting the bDeleted statuses on.
	include($BF. 'includes/overlay.php');
?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>		
<form action="" method="get" id="idForm">
	<table cellpadding="0" cellspacing="0" border="0" class="optionsTop">
<?
	$form=1;
	optionsBar();
?> 
	</table>	
	<table class='List' id='List' style='width: 100%;'  cellpadding="0" cellspacing="0">
		<tr>
<?
	$extra = "";

			sortList('Project Name', 'chrProject','',$extra);
			sortList('Created by', 'chrLast. chrFirst','',$extra);
?>
			<th class='options'>Edit</th>
			<th class='options'><img src="<?=$BF?>images/options.gif"></th>
		</tr>
<? $count=0;	
	while ($row = mysqli_fetch_assoc($results)) { 
		$link = 'window.location.href="viewproject.php?key='.$row['chrKEY'].'"'; 
?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
				onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				
				<td onclick='<?=$link?>'><?=$row['chrProject']?></td>
				<td onclick='<?=$link?>'><?=$row['chrLast'] . ", " . $row['chrFirst']?></td>
				<td class='options'><?=(!$row['bDeleted'] ? '<a title="Edit '.$row['chrQuote'].'" href="editproject.php?key='.$row['chrKEY'].'"><img src="'.$BF.'images/edit.png" alt="edit" /></a>' : "")?></td>
				<td class='options'><?=(!$row['bDeleted'] ? deleteButton($row['ID'],$row['chrProject'],$row['chrKEY']) : "")?></td>
			</tr>
<?	} 
if($count == 0) { ?>
			<tr>
				<td align="center" colspan='8' height="20">No Projects to Display</td>
			</tr>
<?	} ?>
	</table>
	<table cellpadding="0" cellspacing="0" border="0" class="optionsBottom">
<?
	$form=2;
	optionsBar();
?> 
	</table>
</form>
<?
	include($BF .'includes/bottom.php'); 

function optionsBar() {
global $BF;
global $form;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="addbutton"><a title="Add Quote" href="addproject.php"><img src="<?=$BF?>images/plus_add.gif" alt="Add Quote" /></a></td>
			<td class="right">
				Status: 
				<select id='idStatus<?=$form?>' name='idStatus' onchange="javascript:document.getElementById('idStatus<?=($form==1?"2":"1")?>').value = document.getElementById('idStatus<?=($form==1?"1":"2")?>').value">
					<option value="">-Select Status-</option>
<?
	$status = db_query("SELECT ID,chrStatus FROM StatusTypes ORDER BY ID","Getting all Status");
				while ($row = mysqli_fetch_assoc($status)) {
?>
					<option value="<?=$row['ID']?>"<?=($_REQUEST['idStatus'] == $row['ID'] ? ' selected="selected" ' : "")?>><?=$row['chrStatus']?></option>
<?			
				}
?>
					<option value="DEL"<?=($_REQUEST['idStatus'] == "DEL" ? ' selected="selected" ' : "")?>>Deleted</option>
				</select>
				<input type="submit" name="submit" value="Filter" />
			</td>
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}
?>