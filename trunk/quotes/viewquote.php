<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	if(!isset($_REQUEST['key'])) {
		 errorPage('Invalid Quote');
	}
	
	# Getting all original data from this record
	
	$q = "SELECT Q.*, C.chrCustomer, Curr.chrCurrency, Curr.chrShort, chrStatus, Divisions.chrDivision, People.chrFirst, People.chrLast,
			(SELECT SUM(LI.dbQuantity*LI.dbUnitPrice) 
			FROM WorkOrders AS WO
			JOIN LineItems AS LI ON LI.idWorkOrder=WO.ID
			WHERE !LI.bDeleted AND !WO.bDeleted AND WO.idQuote=Q.ID) AS dbTotal
		FROM Quotes AS Q
		JOIN Currencies AS Curr ON Q.idCurrency=Curr.ID
		JOIN StatusTypes AS Status ON Q.idStatus=Status.ID
		JOIN Customers AS C ON Q.idCustomer=C.ID
		LEFT JOIN Divisions ON Q.idDivision=Divisions.ID
		LEFT JOIN People ON People.ID=Q.idContact
		WHERE Q.chrKEY='".$_REQUEST['key']."' 
		GROUP BY Q.ID";
	
	$info = db_query($q,"getting Quote",1);
	if($info['ID'] == "") { errorPage('Invalid Quote'); }

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "dtCreated"; } # This sets the default column order.  Asc by default.


	$title = 'View Quote';
	include($BF .'includes/meta.php');

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	

	$section = 'quotes';
	$leftlink = "viewquotes";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "View Quote:"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-quotes.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $info['chrQuote']; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "This is a view only version of this page. You cannot edit this information from here."; // Instructions or description. (NOT REQUIRED)
	include($BF .'includes/left_quotes.php');
	
	# This is the include file for the overlay to show that the delete is working.
	$TableName = "WorkOrders"; #  This is the Database table that you will be setting the bDeleted statuses on.
	include($BF. 'includes/overlay.php');

?>
	<table cellpadding="0" cellspacing="0" border="0" class="optionsTop" style="width:810">
<?
	optionsBar1();
?> 
	</table>	
	<div class='innerbody800'>
		<div id="errors"></div>
<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>		
		<table width="100%" class="twoCol800" id="twoCol800" cellpadding="0" cellspacing="0">
			<tr>
				<td class="left">

					<div class='FormName'>Job Code/Quote Name</div>
					<div class='FormDisplay'><?=($info['chrQuote'] != "" ? $info['chrQuote'] : "<em>N/A</em>")?></div>

					<div class='FormName'>Division</div>
					<div class='FormDisplay'><?=($info['chrDivision'] != "" ? $info['chrDivision'] : "<em>N/A</em>")?></div>

					<div class='FormName'>Customer PO #</div>
					<div class='FormDisplay'><?=($info['chrExternalPO'] != "" ? $info['chrExternalPO'] : "<em>N/A</em>")?></div>

					<div class='FormName'>Client / Customer</div>
					<div class='FormDisplay'><?=($info['chrCustomer'] != "" ? $info['chrCustomer'] : "<em>N/A</em>")?></div>

					<div class='FormName'>Contact Person</div>
					<div class='FormDisplay'><?=$info['chrFirst'] ." ". $info['chrLast']?></div>
					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<div class='FormName'>Total</div>
					<div class='FormDisplay'><?=($info['dbTotal'] != "" ? "$".number_format($info['dbTotal'],2) : "<em>N/A</em>")?></div>

					<div class='FormName'>Currency</div>
					<div class='FormDisplay'><?=$info['chrCurrency']?> (<?=$info['chrShort']?>)</div>

					<div class='FormName'>Quote Status</div>
					<div class='FormDisplay'><?=$info['chrStatus']?></div>

					<div class='FormName'>Begin Date</div>
					<div class='FormDisplay'><?=date('l, F j, Y',strtotime($info['dBegin']))?></div>

					<div class='FormName'>End Date</div>
					<div class='FormDisplay'><?=date('l, F j, Y',strtotime($info['dEnd']))?></div>

				</td>
			</tr>				
		</table>
	</div>
	<table cellpadding="0" cellspacing="0" border="0" class="optionsBottom" style="width:810">
<?
	optionsBar1();
?> 
	</table>
<?
function optionsBar1() {
global $BF;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="middle">&nbsp;</td>
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}

?>
	<div style="padding-bottom:20px;"></div>
<?
	$_SESSION['QorW'] = 'q';

	// Work Orders Section
	$q = "SELECT WO.*, chrStatus, PPL.chrFirst, PPL.chrLast,
			(SELECT SUM(LI.dbQuantity*LI.dbUnitPrice) 
			FROM LineItems AS LI
			WHERE !LI.bDeleted AND LI.idWorkOrder=WO.ID) AS dbTotal
		FROM WorkOrders AS WO
		JOIN StatusTypes AS Status ON WO.idStatus=Status.ID
		JOIN People as PPL ON WO.idPerson=PPL.ID
		WHERE !WO.bDeleted AND WO.idQuote=".$info['ID'];
		
	$results = db_query($q,"getting WorkOrders");
?>
	<div class='innerbody800'>
		<div class='header2'>Work Orders <span class='resultsShown'>(<span id='resultCount'><?=mysqli_num_rows($results)?></span> results)</span></div>
		<table cellspacing="0" cellpadding="0" class='filter'>
			<tr>
				<td>Choose a Work Order from the list below.  Click on the column header to sort the list by that column.</td>
				<td class='filterRight'><input type='button' value='Add Work Order' onclick='window.location.href="addworkorder.php?key=<?=$_REQUEST['key']?>"' <?=($info['bDeleted'] ? 'disabled="disabled"':"")?> /></td>
			</tr>
		</table>			
		<table class='List' id='List' style='width: 100%;'  cellpadding="0" cellspacing="0">
			<tr>
<?
	$extra = "&key=".$_REQUEST['key'];
	
				sortList('Work Order', 'chrWorkOrder','',$extra);
				sortList('Date', 'dtCreated','',$extra);
				sortList('Total $', 'dbTotal','',$extra);
				sortList('Created By', 'chrLast','',$extra);
				sortList('Status', 'chrStatus','',$extra);
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
					<td class='options'><?=(!$info['bDeleted'] ? '<a href="editworkorder.php?key='.$row['chrKEY'].'"><img src="'.$BF.'images/edit.png" alt="edit" /></a>' : "")?></td>
					<td class='options'><?=(!$info['bDeleted'] ? deleteButton($row['ID'],$row['chrWorkOrder'],$row['chrKEY']) : "")?></td>
				</tr>
<?	} 
	if($count == 0) { ?>
				<tr>
					<td align="center" colspan='8' height="20">No WorkOrders to Display</td>
				</tr>
<?	} ?>
		</table>
	</div>
		
<?
	include($BF. "includes/bottom.php");
?>
