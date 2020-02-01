<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');
	
	if(!isset($_REQUEST['key'])) {
		 errorPage('Invalid Quote');
	}

	# Getting all original data from this record
	$info = db_query("SELECT Quotes.*,chrFirst, chrLast 
		FROM Quotes 
		LEFT JOIN People ON People.ID=Quotes.idContact
		WHERE Quotes.chrKEY='". $_REQUEST['key']."'","getting Quote info",1);
	if($info['ID'] == "") { errorPage('Invalid Quote'); }

	// If a post occured
	if(isset($_POST['chrQuote'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
	
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'Quotes';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrQuote',$info['chrQuote'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idStatus',$info['idStatus'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idCustomer',$info['idCustomer'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idDivision',$info['idDivision'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrExternalPO',$info['chrExternalPO'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idCurrency',$info['idCurrency'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs_date($mysqlStr,'dBegin',$info['dBegin'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs_date($mysqlStr,'dEnd',$info['dEnd'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idContact',$info['idContact'],$audit,$table,$info['ID']);
		
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { 
			$_SESSION['InfoMessage'] = $_POST['chrQuote']. " has been successfully updated in the Database.";
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['ID']);
			db_query("UPDATE ".$table." SET dtModified=NOW() WHERE ID=".$info['ID'],"Setting dtModified"); 
		} else {
			$_SESSION['InfoMessage'] = "No Changes have been made to ".$_POST['chrQuote'];
		}
		
		
		header("Location: index.php");
		die();	
	}
	
	$title = 'Edit Quote';
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
		if(errEmpty('idStatus', "You must select a Status.")) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('chrQuote').focus()";

	$section = 'quotes';
	$leftlink = "viewquotes";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Edit Quote:"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-quotes.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $info['chrQuote']; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please update the information below and press the "Update Information" when you are done making changes.'; // Instructions or description. (NOT REQUIRED)
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
					<div class='FormField'><input type="text" name="chrQuote" id="chrQuote" maxlength="200" value="<?=$info['chrQuote']?>" /></div>
					
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
							<option value="<?=$row['ID']?>"<?=($info['idCustomer'] == $row['ID'] ? ' selected="selected"':"")?>><?=$row['chrCustomer']?></option>
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
							<option value="<?=$row['ID']?>"<?=($info['idDivision'] == $row['ID'] ? ' selected="selected"':"")?>><?=$row['chrDivision']?></option>
<?			
				}
?>
						</select>
					</div>

					<div class='FormName'>Status <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'>
						<select id='idStatus' name='idStatus'>
							<option value="">-Select Status-</option>
<?
	$q = "SELECT ID, chrStatus FROM StatusTypes ORDER BY ID";

	$status = db_query($q,"Getting all Status");
				while ($row = mysqli_fetch_assoc($status)) {
?>
							<option value="<?=$row['ID']?>"<?=($info['idStatus'] == $row['ID'] ? ' selected="selected" ' : "")?>><?=$row['chrStatus']?></option>
<?			
				}
?>
						</select>
					</div>

					<div class='FormName'>Contact Person <input type='button' value='+' onclick='javascript:newwin = window.open("popup_addcontact.php","new","width=400,height=400,resizable=1,scrollbars=1"); newwin.focus();' /></div>
					<div class='FormDisplay'><span id='chrContact'><?=($info['chrFirst'] ." ". $info['chrLast'])?></span><input type='hidden' name='idContact' id='idContact' value='<?=$info['idContact']?>' /></div>

					
				</td>
				<td class="gutter"></td>
				<td class="right">
					
					<div class='FormName'>Customer PO #</div>
					<div class='FormField'><input type="text" name="chrExternalPO" id="chrExternalPO" maxlength="200" value="<?=$info['chrExternalPO']?>" /></div>

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
							<option value="<?=$row['ID']?>"<?=($info['idCurrency'] == $row['ID'] ? ' selected="selected"':"")?>><?=$row['chrSymbol']?> - <?=$row['chrCurrency']?> (<?=$row['chrShort']?>)</option>
<?			
				}
?>
						</select>
					</div>


					<div class='FormName'>Begin Date</div>
					<div class='FormField'><input type="text" name="dBegin" id="dBegin" maxlength="20" value="<?=date('m/d/Y',strtotime($info['dBegin']))?>" /></div>

					<div class='FormName'>End Date</div>
					<div class='FormField'><input type="text" name="dEnd" id="dEnd" maxlength="20" value="<?=date('m/d/Y',strtotime($info['dEnd']))?>" /></div>
					
				</td>
			</tr>				
		</table>
	</div>
	<table cellpadding="0" cellspacing="0" border="0" class="optionsBottom" style="width:810">
<?
	optionsBar();
?> 
	</table>
	<input type='hidden' name='key' value='<?=$_REQUEST['key']?>' />
	</form>
		
<?
	include($BF. "includes/bottom.php");

function optionsBar() {
global $BF;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="right"><input type='submit' value='Update Information' /></td>
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}

?>
