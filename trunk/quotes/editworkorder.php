<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');
	
	if(!isset($_REQUEST['key'])) {
		 errorPage('Invalid Work Order');
	}

	# Getting all original data from this record
	$info = db_query("SELECT WorkOrders.*, Quotes.chrKEY as chrQuoteKEY FROM WorkOrders JOIN Quotes ON WorkOrders.idQuote=Quotes.ID WHERE WorkOrders.chrKEY='". $_REQUEST['key'] ."'","getting Work Order info",1);
	if($info['ID'] == "") { errorPage('Invalid Work Order'); }

	// If a post occured
	if(isset($_POST['idPerson'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
	
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'WorkOrders';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idStatus',$info['idStatus'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idPerson',$info['idPerson'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'txtNotes',$info['txtNotes'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'dDefaultUnitPrice',$info['dDefaultUnitPrice'],$audit,$table,$info['ID']);
		
		
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { 
			$_SESSION['InfoMessage'] = $info['chrWorkOrder']. " has been successfully updated in the Database.";
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['ID']);
			db_query("UPDATE ".$table." SET dtModified=NOW() WHERE ID=".$info['ID'],"Setting dtModified"); 
		} else {
			$_SESSION['InfoMessage'] = "No Changes have been made to ".$_POST['chrWorkOrder'];
		}
		
		if ($_SESSION['QorW'] == 'q') {
			header("Location: viewquote.php?key=".$info['chrQuoteKEY']);
			die();
		} else { 
			header("Location: workorders.php");
			die();
		}

	}
	
	$title = 'Edit Work Order';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('idPerson', "You must select an Employee.")) { totalErrors++; }
		if(errEmpty('idStatus', "You must select a Status.")) { totalErrors++; }
	
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
	$banner_title = "Edit Work Order:"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-quotes.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $info['chrWorkOrder']; // Extra information for Page. (NOT REQUIRED)
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
							<option value="<?=$row['ID']?>"<?=($info['idPerson'] == $row['ID'] ? ' selected="selected" ' : "")?>><?=$row['chrLast']?>, <?=$row['chrFirst']?></option>
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

					<div class='FormName'>Default Unit Price</div>
					<div class='FormField'><input type="text" name=dDefaultUnitPrice id="dDefaultUnitPrice" maxlength="20" value="<?=$info['dDefaultUnitPrice']?>" /></div>
					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<div class='FormName'>Notes</div>
					<div class='FormField'><textarea name="txtNotes" id="txtNotes" cols="40" rows="8" wrapping="virtual" ><?=$info['txtNotes']?></textarea></div>
					
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
