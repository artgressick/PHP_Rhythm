<html>
<head>
<style type="text/css">

</style>
</head>
</html><?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	$title = 'Quotes';	
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrCustomer,dtCreated"; } # This sets the default column order.  Asc by default.

	$q = "SELECT WorkOrders.ID,WorkOrders.chrWorkOrder,WorkOrders.dtCreated,Customers.chrCustomer,StatusTypes.chrStatus,
		  (SELECT SUM(LI.dbQuantity*LI.dbUnitPrice) FROM LineItems as LI WHERE LI.idWorkOrder=WorkOrders.ID) as dbTotal
		FROM WorkOrders
		JOIN Customers ON Customers.ID=WorkOrders.idCustomer
		JOIN StatusTypes ON StatusTypes.ID=WorkOrders.idStatus 
		WHERE WorkOrders.idStatus=1
		ORDER BY ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];
	$results = db_query($q,"getting customers");

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	

	$section = 'quotes';
	$leftlink = "viewquotes";
	include($BF .'includes/top.php');
	include($BF .'includes/left_quotes.php');

	# This is the include file for the overlay to show that the delete is working.
	$TableName = "Customers"; #  This is the Database table that you will be setting the bDeleted statuses on.
	include($BF. 'includes/overlay.php');
?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>		
	<div class='header2'>Quotes <span class='resultsShown'>(<span id='resultCount'><?=mysqli_num_rows($results)?></span> results)</span></div>
	<table cellspacing="0" cellpadding="0" class='filter'>
		<tr>
			<td>Choose a quote from the list below.  Click on the column header to sort the list by that column.</td>
			<td class='filterRight'><input type='button' value='Add Quote' onclick='window.location.href="addquote.php"' /></td>
		</tr>
	</table>			
	<table cellspacing="0" cellpadding="0" class='filter2'>
		<tr>
			<td>This is a filter for something?</td>
			<td class='filter2Right'><input type='button' value='Add Quote' onclick='window.location.href="addquote.php"' /></td>
		</tr>
	</table>			
		
	<table class='List' id='List' style='width: 100%;'  cellpadding="0" cellspacing="0">
		<tr>
			<? sortList('Work Order #', 'chrWorkOrder'); ?>
			<? sortList('Customer Name', 'chrCustomer'); ?>
			<? sortList('Date', 'dtCreated'); ?>
			<? sortList('Total $', 'dbTotal'); ?>
			<? sortList('Status', 'chrStatus'); ?>
			<th class='options'>Edit</th>
			<th class='options'><img src="<?=$BF?>images/options.gif"></th>
		</tr>
<? $count=0;	
	while ($row = mysqli_fetch_assoc($results)) { 
		$link = 'window.location.href="workorder.php?id='.$row['ID'].'"'; 
?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
				onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				<td onclick='<?=$link?>'><?=$row['chrWorkOrder']?></td>
				<td onclick='<?=$link?>'><?=$row['chrCustomer']?></td>
				<td onclick='<?=$link?>'><?=$row['dtCreated']?></td>
				<td onclick='<?=$link?>'>$<?=number_format($row['dbTotal'],2)?></td>
				<td onclick='<?=$link?>'><?=$row['chrStatus']?></td>
				<td class='options'><a href='editcustomer.php?id=<?=$row['ID']?>'><img src='<?=$BF?>images/edit.png' alt='edit' /></a></td>
				<td class='options'><?=deleteButton($row['ID'],$row['chrCustomer'])?></td>
			</tr>
<?	} 
if($count == 0) { ?>
			<tr>
				<td align="center" colspan='3' height="20">No Customers to Display</td>
			</tr>
<?	} ?>
	</table>



<?	include($BF .'includes/bottom.php'); ?>