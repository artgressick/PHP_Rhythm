<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	$title = 'Quotes';	
	include($BF .'includes/meta.php');

	if(!isset($_REQUEST['idStatus'])) { $_REQUEST['idStatus'] = ""; }
	if(!isset($_REQUEST['dFrom'])) { $_REQUEST['dFrom'] = ""; }
	if(!isset($_REQUEST['dTo'])) { $_REQUEST['dTo'] = ""; }
	
	include($BF. 'components/list/sortList.php');
	# This is for the sorting of the rows and columns.  We must set the default order and name
	
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrCustomer";} # This sets the default column order.  Asc by default.
	
	if ($_REQUEST['idStatus'] != "" || $_REQUEST['dFrom'] != "") { //If there is nothing the first time then don't do the work

		$q = "select WorkOrders.ID, WorkOrders.chrKey, LineItems.idWorkOrder, chrCustomer, chrWorkOrder, WorkOrders.dtCreated, chrStatus, sum((dbQuantity*dbUnitPrice)) as dbTotal
			FROM LineItems
			JOIN WorkOrders ON LineItems.idWorkOrder = WorkOrders.ID 
			JOIN Quotes ON WorkOrders.idQuote = Quotes.ID 
			JOIN Customers ON Quotes.idCustomer = Customers.ID 
			JOIN StatusTypes ON WorkOrders.idStatus = StatusTypes.ID 
			WHERE !LineItems.bDeleted
			". ($_REQUEST['dFrom'] != "" ? " AND WorkOrders.dtCreated BETWEEN '". date('Y-m-d',strtotime($_REQUEST['dFrom'])) ."' AND '". date('Y-m-d',strtotime($_REQUEST['dTo'])) ."' " : '' ) ."
			". ($_REQUEST['idStatus'] != "" & $_REQUEST['idStatus'] != "ALL" ? " AND WorkOrders.idStatus='". $_REQUEST['idStatus'] ."' " : '' ) ."
			". ($_REQUEST['idCustomer'] != "" ? " AND Quotes.idCustomer='". $_REQUEST['idCustomer'] ."' " : '' ) ."
			GROUP BY idWorkOrder 
			ORDER BY ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];
			
		$results = db_query($q,"getting Quotes");
		
	} //If there is nothing then don't run the query

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/showhide.js"></script>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		totalErrors = 0;

		if(errEmpty('dFrom', "You must enter From Date.")) { totalErrors++; 
		} else if(errDate('dFrom','US',"You must have a properly Formated From Date (mm/dd/yyyy).")) { totalErrors++; }
		if(errEmpty('dTo', "You must enter a To Date.")) { totalErrors++; 
		} else if(errDate('dTo','US',"You must have a properly Formated To Date (mm/dd/yyyy).")) { totalErrors++; }
		
		if(errEmpty('idStatus', "You must select a Status.")) { totalErrors++; }

		return (totalErrors == 0 ? true : false);
	}
</script>

<?	

	$section = 'quotes';
	$leftlink = "report-customer";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Customer Report"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-quotes.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".mysqli_num_rows($results)." results)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "This is a list of quotes by date range and customer."; // Instructions or description. (NOT REQUIRED)
	include($BF .'includes/left_quotes.php');

?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>		
<form action="" method="get" id="idForm" onsubmit="return error_check()">

	<div id='errors'></div>

	<table cellpadding="0" cellspacing="0" border="0" class="optionsTop">
		<tr>
			<td class="leftCapBlue"></td>
			<td class="rightBlue">

				<table cellpadding="0" cellspacing="0">
					<tr>
						<td style='padding-right: 20px;'>
							Dates: <input type='text' id='dFrom' name='dFrom' size='10' value='<?=$_REQUEST['dFrom']?>' /><span style='cursor: pointer;' 'onclick='document.getElementById("dFrom").value="<?=date('m/d/Y')?>";'>X</span> to <input type='text' id='dTo' name='dTo' size='10' value='<?=$_REQUEST['dTo']?>' /><span style='cursor: pointer;' onclick='document.getElementById("dTo").value="<?=date('m/d/Y')?>";'>X</span> (mm/dd/yyyy)
						</td>
						<td>
							<select id='idCustomer' name='idCustomer' style="width: 200px;">
								<option value=""<?=($_REQUEST['idCustomer'] == "" ? ' selected="selected" ' : "")?>>All Customers</option>
<?
	$customer = db_query("SELECT ID,chrCustomer FROM Customers ORDER BY chrCustomer","Getting all Customers");
	while ($row = mysqli_fetch_assoc($customer)) {
?>
								<option value="<?=$row['ID']?>"<?=($_REQUEST['idCustomer'] == $row['ID'] ? ' selected="selected" ' : "")?>><?=$row['chrCustomer']?></option>
<?			
	}
?>
							</select>
						</td>
						<td>
							<select id='idStatus' name='idStatus'>
								<option value="">-Select Status-</option>
								<option value="ALL"<?=($_REQUEST['idStatus'] == "ALL" ? ' selected="selected" ' : "")?>>All</option>
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
							<input type="submit" value="Filter" />
						</td>
						<td>
							<span style='padding-left: 20px;'><a href="javascript:showHideLink('moreFiltersBox');">[Additional Filters]</a></span>
						</td>
					</tr>
				</table>
				
			</td>
			<td class="rightCapBlue"></td>
		</tr>
	</table>	
	<div id='moreFiltersBox' class='showHideBox' style='display: none; border: 1px solid gray; border-top: none; padding: 10px;'>Anything in here</div>
<?
	if ($_REQUEST['idStatus'] != "" || $_REQUEST['dFrom'] != "") { //If there is nothing the first time then don't do the work
?>
	<table class='List' id='List' style='width: 100%;'  cellpadding="0" cellspacing="0">
		<tr>
<?
		$extra = "idStatus=".$_REQUEST['idStatus']."&dFrom=".$_REQUEST['dFrom']."&dTo=".$_REQUEST['dTo'];
			sortList('Customer', 'chrCustomer','',$extra);
			sortList('Work Order #', 'chrWorkOrder','',$extra);
			sortList('Created', 'dtCreated','',$extra);
			sortList('Total', 'dbTotal','',$extra);
			sortList('chrStatus', 'chrStatus','',$extra);
?>
		</tr>
<?
		$count=0;
		while ($row = mysqli_fetch_assoc($results)) { 
			$link = 'window.location.href="workorder.php?key='.$row['chrKey'].'"'; 
?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
				onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				<td onclick='<?=$link?>'><?=$row['chrCustomer']?></td>
				<td onclick='<?=$link?>'><?=$row['chrWorkOrder']?></td>
				<td onclick='<?=$link?>'><?=date('m/d/Y - g:i a',strtotime($row['dtCreated']))?></td>
				<td onclick='<?=$link?>'><?=($row['dbTotal'] != "" ? "$".number_format($row['dbTotal'],2) : "<em>N/A</em>")?></td>
				<td onclick='<?=$link?>'><?=$row['chrStatus']?></td>
			</tr>
<?
		} 
		if($count == 0) {
?>
			<tr>
				<td align="center" colspan='5' height="20">No Quotes to Display</td>
			</tr>
<?
		}
?>
	</table>
	
<? 
	} else { //If there is nothing then don't run the query instead show the message to enter some information
?>
	<table cellpadding="0" cellspacing="0" style="border: 1px solid gray; width: 100%; height: 20px; text-align: center;">
		<tr>
			<td style="font-size: 11px;">
				You need to enter in criteria before we can generate a report.
			</td>
		</tr>
	</table>
<?
	}
?>
</form>

<?
	include($BF .'includes/bottom.php'); 
?>