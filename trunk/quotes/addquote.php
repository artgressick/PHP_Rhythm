<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	// If a post occured
	if(isset($_POST['chrQuote'])) { // When doing isset, use a required field.  Faster than the php count funtion.

		$table = 'Quotes'; # added so not to forget to change the insert AND audit

		$q = "INSERT INTO ". $table ." SET 
			chrKEY = '". makekey() ."',
			chrQuote = '". encode($_POST['chrQuote']) ."',
			idCustomer = '". $_POST['idCustomer'] ."',
			idDivision = '".$_POST['idDivision']."',
			idCurrency = '".$_POST['idCurrency']."',
			idContact = '".$_POST['idContact']."',
			dBegin = '". date('Y-m-d',strtotime($_POST['dBegin'])) ."',
			dEnd = '". date('Y-m-d',strtotime($_POST['dEnd'])) ."',
			chrExternalPO = '".encode($_POST['chrExternalPO'])."',
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
			
			// Insert First WorkOrder from quote
			
			db_query("INSERT INTO WorkOrders SET
						chrKEY = '". makekey() ."',
						idPerson = '". $_SESSION['idPerson'] ."',
						idQuote = '". $newID ."',
						idStatus=1,
						dtCreated=now(),
						dtModified=now()","Inserting First Work Order Automatically");
		
			global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
			$newID2 = mysqli_insert_id($mysqli_connection);

			$prefix = db_query("SELECT chrPrefix FROM Divisions WHERE ID=".$_POST['idDivision'],"Getting Prefix",1);

			db_query("UPDATE WorkOrders SET chrWorkOrder='".$prefix['chrPrefix']."-".$newID2."' WHERE ID='".$newID2."'","Inserting Name");
		
		
			$q = "INSERT INTO Audit SET 
				idType=1, 
				idRecord='". $newID ."',
				txtNewValue='". encode($_POST['chrQuote']) ."',
				dtDateTime=now(),
				chrTableName='". $table ."',
				idPerson='". $_SESSION['idPerson'] ."'
			";
			db_query($q,"Insert audit");
			//End the code for History Insert 
		
			$_SESSION['InfoMessage'] = $_POST['chrQuote']. " has been successfully added to the Database.";
			header("Location: ". $_POST['moveTo']);
			die();
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occured while trying to add the quote "'. $_POST['chrQuote'] .'".');
		}
	}
	
	$title = 'Add Quote';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('chrQuote', "You must enter a Quote Name.")) { totalErrors++; }
		if(errEmpty('idCustomer', "You must select a Customer.")) { totalErrors++; }
		if(errEmpty('idDivision', "You must select a Division.")) { totalErrors++; }
		if(errEmpty('idCurrency', "You must select a Currency.")) { totalErrors++; }

	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('chrWorkOrder').focus()";

	$section = 'quotes';
	$leftlink = "addquote";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Add Quote"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-quotes.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please fill in all the fields and press the "Add Another" to add another Quote or "Add and Continue" to return to the Quote list.'; // Instructions or description. (NOT REQUIRED)

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

					<div class='FormName'>Job Code/Quote Name <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrQuote" id="chrQuote" maxlength="200" /></div>
					
					<div class='FormName'>Customer <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'>
						<select id='idCustomer' name='idCustomer'>
							<option value="">-Select Customer-</option>
<?
	$q = "SELECT ID, chrCustomer
			FROM Customers
			WHERE !bDeleted
			ORDER BY chrCustomer";

	$customers = db_query($q,"Getting all customers");
				while ($row = mysqli_fetch_assoc($customers)) {
?>
							<option value="<?=$row['ID']?>"><?=$row['chrCustomer']?></option>
<?			
				}
?>
						</select>
					</div>

					<div class='FormName'>Division <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'>
						<select id='idDivision' name='idDivision'>
							<option value="">-Select Division-</option>
<?
	$q = "SELECT ID, chrDivision
			FROM Divisions
			WHERE !bDeleted
			ORDER BY chrDivision";

	$divisions = db_query($q,"Getting all divisions");
				while ($row = mysqli_fetch_assoc($divisions)) {
?>
							<option value="<?=$row['ID']?>"><?=$row['chrDivision']?></option>
<?			
				}
?>
						</select>
					</div>

					<div class='FormName'>Contact Person <input type='button' value='+' onclick='javascript:newwin = window.open("popup_addcontact.php","new","width=400,height=400,resizable=1,scrollbars=1"); newwin.focus();' /></div>
					<div class='FormDisplay'><span id='chrContact'></span><input type='hidden' name='idContact' id='idContact' /></div>

					
				</td>
				<td class="gutter"></td>
				<td class="right">
					
					<div class='FormName'>Customer PO #</div>
					<div class='FormField'><input type="text" name="chrExternalPO" id="chrExternalPO" maxlength="200" /></div>

					<div class='FormName'>Currency <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'>
						<select id='idCurrency' name='idCurrency'>
<?
	$q = "SELECT ID, chrCurrency, chrShort, chrSymbol
			FROM Currencies
			WHERE !bDeleted
			ORDER BY intOrder";

	$currencies = db_query($q,"Getting all currencies");
				while ($row = mysqli_fetch_assoc($currencies)) {
?>
							<option value="<?=$row['ID']?>"><?=$row['chrSymbol']?> - <?=$row['chrCurrency']?> (<?=$row['chrShort']?>)</option>
<?			
				}
?>
						</select>
					</div>

					<div class='FormName'>Begin Date</div>
					<div class='FormField'><input type="text" name="dBegin" id="dBegin" maxlength="20" /></div>

					<div class='FormName'>End Date</div>
					<div class='FormField'><input type="text" name="dEnd" id="dEnd" maxlength="20" /></div>


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
	</form>
		
<?
	include($BF. "includes/bottom.php");

function optionsBar() {
global $BF;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="right"><input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addquote.php';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='index.php';" /></td>
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}

?>
