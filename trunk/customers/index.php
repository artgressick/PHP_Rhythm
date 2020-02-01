<?
	$BF = "../"; #This is the BASE FOLDER.  This should be located at the top of every page with the proper set of '../'s to find the root folder 
	$AT = "customer"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');
		
	$title = "Work Orders";
	include($BF .'customers/includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrCustomer,dtCreated"; } # This sets the default column order.  Asc by default.

	$q = "SELECT WO.ID, WO.chrKEY, WO.chrWorkOrder, WO.dtCreated, Customers.chrCustomer, Status.chrStatus, 
				(SELECT SUM(LI.dbQuantity*LI.dbUnitPrice) FROM LineItems as LI WHERE !LI.bDeleted AND LI.idWorkOrder=WO.ID) as dbTotal
			FROM WorkOrders AS WO
			JOIN Quotes ON WO.idQuote=Quotes.ID AND !Quotes.bDeleted
			JOIN Customers ON Quotes.idCustomer=Customers.ID AND !Customers.bDeleted
			JOIN StatusTypes AS Status ON WO.idStatus=Status.ID
			WHERE Customers.ID IN (SELECT idCustomer FROM CustomerAccess WHERE idPerson=".$_SESSION['idCustomer'].")";
			
	// This is for Filtering
	if(!isset($_REQUEST['idStatus']) || $_REQUEST['idStatus'] == "") { $_REQUEST['idStatus'] = 1; }
	
	if(is_numeric($_REQUEST['idStatus'])) {
		$q .= " AND !WO.bDeleted AND WO.idStatus='".$_REQUEST['idStatus']."' ";
	} else if ($_REQUEST['idStatus'] == "DEL") {
		$q .= " AND WO.bDeleted ";
	}
			
			
	$q .= " ORDER BY ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];
	$results = db_query($q,"getting workorders");

	include($BF .'customers/includes/top.php');

?>	
<form action="" method="post" id="idForm">
	<div class='header2'>Work Orders <span class='resultsShown'>(<span id='resultCount'><?=mysqli_num_rows($results)?></span> results)</span></div>
	<table cellspacing="0" cellpadding="0" class='filter'>
		<tr>
			<td>Choose a Work Order from the list below to view.  Click on the column header to sort the list by that column.</td>
		</tr>
	</table>			
	<table cellspacing="0" cellpadding="0" class='filter2'>
		<tr>
			<td>
				Filter Options:
			</td>
			<td class='filter2Right'>
				Status: 
				<select id='idStatus' name='idStatus'>
					<option value="">-Select Status-</option>
<?
	$status = db_query("SELECT ID,chrStatus FROM StatusTypes ORDER BY ID","Getting all Status");
				while ($row = mysqli_fetch_assoc($status)) {
?>
					<option value="<?=$row['ID']?>"<?=($_REQUEST['idStatus'] == $row['ID'] ? ' selected="selected" ' : "")?>><?=$row['chrStatus']?></option>
<?			
				}
?>
				</select>
				<input type="submit" name="submit" value="Filter" />
			</td>
		</tr>
	</table>		

	<table class='List' id='List' style='width: 100%;'  cellpadding="0" cellspacing="0">
		<tr>
<?
	$extra = "&idStatus=".$_REQUEST['idStatus'];
	
			sortList('Work Order #', 'chrWorkOrder','',$extra);
			sortList('Customer Name', 'chrCustomer','',$extra);
			sortList('Date', 'dtCreated','',$extra);
			sortList('Total $', 'dbTotal','',$extra);
			sortList('Status', 'chrStatus','',$extra);
?>
		</tr>
<? $count=0;	
	while ($row = mysqli_fetch_assoc($results)) { 
		$link = 'window.open("workorder.php?key='.$row['chrKEY'].'","plain","width=850,height=600,location=no,menubar=no,status=no,toolbar=yes,scrollbars=yes,resizable=yes")'; 
?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
				onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				<td onclick='<?=$link?>'><?=$row['chrWorkOrder']?></td>
				<td onclick='<?=$link?>'><?=$row['chrCustomer']?></td>
				<td onclick='<?=$link?>'><?=date('m/d/Y - g:i a',strtotime($row['dtCreated']))?></td>
				<td onclick='<?=$link?>'><?=($row['dbTotal'] != "" ? "$".number_format($row['dbTotal'],2) : "<em>N/A</em>")?></td>
				<td onclick='<?=$link?>'><?=$row['chrStatus']?></td>
			</tr>
<?	} 
if($count == 0) { ?>
			<tr>
				<td align="center" colspan='5' height="20">No Work Orders to Display</td>
			</tr>
<?	} ?>
	</table>
</form>

<?	include($BF.'customers/includes/bottom.php'); ?>