<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	$title = 'Work Orders';	
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "dtCreated"; $_REQUEST['ordCol'] = "DESC"; } # This sets the default column order.  Asc by default.

/*	$q = "SELECT Q.ID, Q.chrKEY, Q.chrQuote, C.chrCustomer, D.chrDivision, Q.dtCreated, S.chrStatus, Q.bDeleted,Q.chrExternalPO,
			(SELECT SUM(LI.dbQuantity*LI.dbUnitPrice) 
			FROM WorkOrders AS WO
			JOIN LineItems AS LI ON LI.idWorkOrder=WO.ID
			WHERE !LI.bDeleted AND !WO.bDeleted AND WO.idQuote=Q.ID) AS dbTotal 
		FROM Quotes AS Q
		LEFT JOIN Divisions AS D ON Q.idDivision=D.ID
		JOIN Customers AS C ON Q.idCustomer=C.ID
		JOIN StatusTypes AS S ON Q.idStatus=S.ID ";
*/
	$q = "SELECT WO.*, chrStatus, PPL.chrFirst, PPL.chrLast,
			(SELECT SUM(LI.dbQuantity*LI.dbUnitPrice) 
			FROM LineItems AS LI
			WHERE !LI.bDeleted AND LI.idWorkOrder=WO.ID) AS dbTotal
		FROM WorkOrders AS WO
		JOIN StatusTypes AS Status ON WO.idStatus=Status.ID
		JOIN People as PPL ON WO.idPerson=PPL.ID";

		
	// This is for filtering
	if(!isset($_REQUEST['idStatus']) || $_REQUEST['idStatus'] == "") { $_REQUEST['idStatus'] = 1; }
	
	if(is_numeric($_REQUEST['idStatus'])) {

		$q .= " WHERE !WO.bDeleted AND WO.idStatus='".$_REQUEST['idStatus']."' ".is_searched();

	} else if ($_REQUEST['idStatus'] == "DEL") {
	
		$q .= " WHERE WO.bDeleted ".is_searched();
 
	} else if($_REQUEST['idStatus'] == "ALL") {
		$q .= " WHERE !WO.bDeleted ".is_searched();
	}
	
	function is_searched() {
		if(isset($_REQUEST['chrSearch']) && $_REQUEST['chrSearch'] != "") {
			return " AND chrWorkOrder LIKE '%".$_REQUEST['chrSearch']."%' ";
		}
		return false;
	}
	
	$q .= " GROUP BY WO.ID ORDER BY ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];
	
	$results = db_query($q,"getting Work Orders");
	
?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	
	$_SESSION['QorW'] = 'w';
	$section = 'quotes';
	$leftlink = "workorders";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Work Orders "; // Title of this page. (REQUIRED)
	$banner_icon = "icons-quotes.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".mysqli_num_rows($results)." results)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "Choose a Work Order from the list below.  Click on the column header to sort the list by that column.<br /><em>Filter will show all Work Orders of the status chosen.</em>"; // Instructions or description. (NOT REQUIRED)
	include($BF .'includes/left_quotes.php');

	# This is the include file for the overlay to show that the delete is working.
	$TableName = "WorkOrders"; #  This is the Database table that you will be setting the bDeleted statuses on.
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
				sortList('Work Order', 'chrWorkOrder');
				sortList('Date', 'dtCreated');
				sortList('Total $', 'dbTotal');
				sortList('Created By', 'chrLast');
				sortList('Status', 'chrStatus');
?>
				<th class='options'>Edit</th>
				<th class='options'><img src="<?=$BF?>images/options.gif"></th>
			</tr>
<? $count=0;	
		while ($row = mysqli_fetch_assoc($results)) { 
			$link = 'window.location.href="workorder.php?key='.$row['chrKEY'].'"'; 
?>
				<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
					onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
					<td onclick='<?=$link?>'><?=$row['chrWorkOrder']?></td>
					<td onclick='<?=$link?>'><?=date('m/d/Y - g:i a',strtotime($row['dtCreated']))?></td>
					<td onclick='<?=$link?>'><?=($row['dbTotal'] != "" ? "$".number_format($row['dbTotal'],2) : "<em>N/A</em>")?></td>
					<td onclick='<?=$link?>'><?=$row['chrLast']?>, <?=$row['chrFirst']?></td>
					<td onclick='<?=$link?>'><?=$row['chrStatus']?></td>
					<td class='options'><?=(!$row['bDeleted'] ? '<a href="editworkorder.php?key='.$row['chrKEY'].'"><img src="'.$BF.'images/edit.png" alt="edit" /></a>' : "&nbsp;")?></td>
					<td class='options'><?=(!$row['bDeleted'] ? deleteButton($row['ID'],$row['chrWorkOrder'],$row['chrKEY']) : "&nbsp;")?></td>
				</tr>
<?	} 
	if($count == 0) { ?>
				<tr>
					<td align="center" colspan='8' height="20">No WorkOrders to Display</td>
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
			<td class="addbutton"><a title="Add Work Order" href="addworkorder.php"><img src="<?=$BF?>images/plus_add.gif" alt="Add Work Order" /></a></td>
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
					<option value="ALL"<?=($_REQUEST['idStatus'] == "ALL" ? ' selected="selected" ' : "")?>>Show All</option>
				</select>
				<input type="submit" name="submit" value="Filter" />
			</td>
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}

?>