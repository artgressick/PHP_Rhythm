<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	if(!isset($_REQUEST['key'])) {
		 errorPage('Invalid IM Type');
	}

	# Getting all original data from this record
	$info = db_query("SELECT * FROM ImTypes WHERE chrKEY='". $_REQUEST['key'] ."'","getting IM Type info",1);
	if($info['ID'] == "") { errorPage('Invalid IM Type'); }

	// If a post occured
	if(isset($_POST['chrImType'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
	
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'ImTypes';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrImType',$info['chrImType'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'bShow',$info['bShow'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'bServer',$info['bServer'],$audit,$table,$info['ID']);
		
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { 
			$_SESSION['InfoMessage'] = $_POST['chrImType']. " has been successfully updated in the Database.";
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['ID']);
		} else {
			$_SESSION['InfoMessage'] = "No Changes have been made to ".$info['chrImType'];
		}
		
		
		header("Location: imtypes.php");
		die();	
	}
	
	$title = 'Edit IM Type';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('chrImType', "You must enter a IM Type Name.")) { totalErrors++; }

		if(document.getElementById('bShow0').checked == false && document.getElementById('bShow1').checked == false) {
			errCustom('',"You must choose either to Show or not to Show this IM Type.");
			totalErrors++;
		}

		if(document.getElementById('bServer0').checked == false && document.getElementById('bServer1').checked == false) {
			errCustom('',"You must choose if this IM Type requires a Server.");
			totalErrors++;
		}

	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('chrImType').focus()";

	$section = 'admin';
	$leftlink = "imtypes";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Edit IM Type:"; // Title of this page. (REQUIRED)
	$banner_icon = ""; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $info['chrImType']; // Extra information for Page. (NOT REQUIRED)
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

					<div class='FormName'>IM Type Name <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrImType" id="chrImType" maxlength="150" size="50" value='<?=$info['chrImType']?>' /></div>
					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<div class='FormName'>Show this IM Type <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="radio" name="bShow" id="bShow0" value="0" <?=(!$info['bShow']?'checked="checked"':"")?> /> No &nbsp;&nbsp;&nbsp; <input type="radio" name="bShow" id="bShow1" value="1" <?=($info['bShow']?'checked="checked"':"")?> /> Yes</div>
					
					<div class='FormName'>Require Server Input <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="radio" name="bServer" id="bServer0" value="0" <?=(!$info['bServer']?'checked="checked"':"")?> /> No &nbsp;&nbsp;&nbsp; <input type="radio" name="bServer" id="bServer1" value="1" <?=($info['bServer']?'checked="checked"':"")?> /> Yes</div>

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
