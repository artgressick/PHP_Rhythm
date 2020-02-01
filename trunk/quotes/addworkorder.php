<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	if(isset($_REQUEST['key'])) {
	
		$q = "SELECT ID, chrQuote, idDivision FROM Quotes WHERE !bDeleted AND chrKEY='".$_REQUEST['key'] ."'";
		
		$quote = db_query($q,"Does Quote Exist",1);
		
		if($quote['ID'] == "") { errorPage('Invalid Quote'); }
			
	}
	

	// If a post occured
	if(isset($_POST['idPerson'])) { // When doing isset, use a required field.  Faster than the php count funtion.

		$tmp = db_query("SELECT ID FROM Quotes WHERE chrKEY='".$_POST['key']."'","Getting Quote",1);
	
		$table = 'WorkOrders'; # added so not to forget to change the insert AND audit

		$q = "INSERT INTO ". $table ." SET 
			chrKEY = '". makekey() ."',
			chrWorkOrder = '". encode($_POST['chrWorkOrder']) ."',
			idPerson = '". $_POST['idPerson'] ."',
			idQuote = '".$tmp['ID']."',
			txtNotes = '". encode($_POST['txtNotes']) ."',
			dDefaultUnitPrice = '". $_POST['dDefaultUnitPrice'] ."',
			idStatus=1,
			dtCreated=now(),
			dtModified=now()
		";
		
		# if there database insertion is successful	
		if(db_query($q,"Insert into ". $table)) {

			// This is the code for inserting the Audit Page
			// Type 1 means ADD NEW RECORD, change the TABLE NAME also
			global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
			$newID = mysqli_insert_id($mysqli_connection);
	
			$prefix = db_query("SELECT chrPrefix FROM Divisions WHERE ID=".$quote['idDivision'],"Getting Prefix",1);
	
			db_query("UPDATE ".$table." SET chrWorkOrder='".$prefix['chrPrefix']."-".$newID."' WHERE ID='".$newID."'","Inserting Name");
		
			$q = "INSERT INTO Audit SET 
				idType=1, 
				idRecord='". $newID ."',
				txtNewValue='".$prefix['chrPrefix']."-".$newID."',
				dtDateTime=now(),
				chrTableName='". $table ."',
				idPerson='". $_SESSION['idPerson'] ."'
			";
			db_query($q,"Insert audit");
			//End the code for History Insert 
		
			$_SESSION['InfoMessage'] = $prefix['chrPrefix']."-".$newID." has been successfully added to the Database.";
			header("Location: ". $_POST['moveTo']);
			die();
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occured while trying to add the work order '. $prefix['chrPrefix'].'-'.$newID);
		}
	}
	
	$title = 'Add Work Order';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('idPerson', "You must select an Employee.")) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('chrWorkOrder').focus()";

	$section = 'quotes';
	if ($_SESSION['QorW'] == 'q') {
		$leftlink = "viewquotes";
	} else { $leftlink = "workorders"; }
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Add Work Order to Quote".(isset($_REQUEST['key']) ? ":":""); // Title of this page. (REQUIRED)
	$banner_icon = "icons-quotes.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	if(isset($_REQUEST['key'])) {
		$banner_xtra = $quote['chrQuote']; // Extra information for Page. (NOT REQUIRED)
	}
	$banner_instructions = 'Please fill in all the fields and press the "Add Another" to add another Work Order or "Add and Continue" to return to the Quote View.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_quotes.php');
?>
	<form action="" method="post" id="idForm" onsubmit="return error_check()">
	<table cellpadding="0" cellspacing="0" border="0" class="optionsTop" style="width:810">
<?
	optionsBar();
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
<?
	if(!isset($_REQUEST['key'])) {
?>
					<div class='FormName'>Select Quote <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'>
						<select id='key' name='key'>
							<option value="">-Select Quote-</option>
<?
				$q = "SELECT Q.ID, Q.chrKEY, Q.chrQuote, C.chrCustomer
						FROM Quotes AS Q
						JOIN Customers AS C ON Q.idCustomer=C.ID
						WHERE !Q.bDeleted AND !C.bDeleted
						ORDER BY chrCustomer, chrQuote";
			
				$quotes = db_query($q,"Getting Quotes");
				while ($row = mysqli_fetch_assoc($quotes)) {
?>
							<option value="<?=$row['chrKEY']?>"><?=$row['chrCustomer']?> (<?=$row['chrQuote']?>)</option>
<?			
				}
?>
						</select>
					</div>
	
<?	
	}
?>
					<div class='FormName'>Employee Name <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'>
						<select id='idPerson' name='idPerson'>
							<option value="">-Select Employee-</option>
<?
	$q = "SELECT P.ID, P.chrFirst, P.chrLast
			FROM SiteAccess
			JOIN People AS P ON SiteAccess.idPerson=P.ID
			WHERE !P.bDeleted
			ORDER BY P.chrLast, P.chrFirst";

	$people = db_query($q,"Getting all People with Site Access");
				while ($row = mysqli_fetch_assoc($people)) {
?>
							<option value="<?=$row['ID']?>"<?=($_SESSION['idPerson'] == $row['ID'] ? ' selected="selected" ' : "")?>><?=$row['chrLast']?>, <?=$row['chrFirst']?></option>
<?			
				}
?>
						</select>
					</div>

					<div class='FormName'>Default Unit Price</div>
					<div class='FormField'><input type="text" name=dDefaultUnitPrice id="dDefaultUnitPrice" maxlength="20" /></div>
					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<div class='FormName'>Notes</div>
					<div class='FormField'><textarea name="txtNotes" id="txtNotes" cols="40" rows="8" wrapping="virtual" ></textarea></div>

				</td>
			</tr>				
		</table>
	</div>
	<table cellpadding="0" cellspacing="0" border="0" class="optionsBottom" style="width:810">
<?
	optionsBar();
?> 
	</table>
	<input type='hidden' name='moveTo' id='moveTo' />
<?
	if(isset($_REQUEST['key'])) {
?>
	<input type='hidden' name='key' value="<?=$_REQUEST['key']?>" />
<?
	} 
?>
	</form>	
<?
	include($BF. "includes/bottom.php");

function optionsBar() {
global $BF;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="right"><input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addworkorder.php<?=(isset($_REQUEST['key']) ? "?key=".$_REQUEST['key'] : "")?>';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='<?=(isset($_REQUEST['key']) ? 'viewquote.php?key='.$_REQUEST['key']:'workorders.php')?>';" /></td>
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}

?>
