<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	if(!isset($_REQUEST['key'])) {
		 errorPage('Invalid Division');
	}

	# Getting all original data from this record
	$info = db_query("SELECT * FROM Divisions WHERE chrKEY='". $_REQUEST['key'] ."'","getting division info",1);
	if($info['ID'] == "") { errorPage('Invalid Division'); }

	// If a post occured
	if(isset($_POST['chrDivision'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
	
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'Divisions';
		$mysqlStr = '';
		$audit = '';

		$_POST['chrPrefix'] = strtoupper($_POST['chrPrefix']);

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrDivision',$info['chrDivision'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrPrefix',$info['chrPrefix'],$audit,$table,$info['ID']);
		
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { 
			$_SESSION['InfoMessage'] = $_POST['chrDivision']. " has been successfully updated in the Database.";
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['ID']);
		} else {
			$_SESSION['InfoMessage'] = "No Changes have been made to ".$info['chrDivision'];
		}
		
		
		header("Location: divisions.php");
		die();	
	}
	
	$title = 'Edit Customer';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('chrDivision', "You must enter a Division Name.")) { totalErrors++; }
		if(errEmpty('chrPrefix', "You must enter a Prefix Name.")) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('chrDivision').focus()";

	$section = 'admin';
	$leftlink = "divisions";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Edit Division:"; // Title of this page. (REQUIRED)
	$banner_icon = ""; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $info['chrDivision']; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please update the information below and press the "Update Information" when you are done making changes.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_admin.php');
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

					<div class='FormName'>Division Name <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrDivision" id="chrDivision" maxlength="150" size="50" value='<?=$info['chrDivision']?>' /></div>

					<div class='FormName'>Work Order Prefix <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrPrefix" id="chrPrefix" maxlength="2" size="5"  value='<?=$info['chrPrefix']?>' /></div>
					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<!-- Left blank for now -->

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
