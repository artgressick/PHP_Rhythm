<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	if(!isset($_REQUEST['key'])) {
		 errorPage('Invalid Currency');
	}

	# Getting all original data from this record
	$info = db_query("SELECT * FROM Currencies WHERE chrKEY='". $_REQUEST['key'] ."'","getting Currency info",1);
	if($info['ID'] == "") { errorPage('Invalid Currency'); }

	// If a post occured
	if(isset($_POST['chrCurrency'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
	
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'Currencies';
		$mysqlStr = '';
		$audit = '';

		// Prime variables before they go into the database
		$_POST['chrShort'] = strtoupper(encode($_POST['chrShort']));
		$_POST['chrSymbol'] = htmlentities($_POST['chrSymbol'],ENT_QUOTES,"UTF-8");

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrCurrency',$info['chrCurrency'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrShort',$info['chrShort'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrSymbol',$info['chrSymbol'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'bShow',$info['bShow'],$audit,$table,$info['ID']);
		
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { 
			$_SESSION['InfoMessage'] = $_POST['chrCurrency']. " has been successfully updated in the Database.";
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['ID']);
		} else {
			$_SESSION['InfoMessage'] = "No Changes have been made to ".$info['chrCurrency'];
		}
		
		
		header("Location: currencies.php");
		die();	
	}
	
	$title = 'Edit Currency';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('chrCurrency', "You must enter a Currency Name.")) { totalErrors++; }
		if(errEmpty('chrShort', "You must enter a Currency Name.")) { totalErrors++; }
		if(errEmpty('chrSymbol', "You must enter a Currency Name.")) { totalErrors++; }

		if(document.getElementById('bShow0').checked == false && document.getElementById('bShow1').checked == false) {
			errCustom('',"You must choose either to Show or not to Show this Currency.");
			totalErrors++;
		}


	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('chrCurrency').focus()";

	$section = 'admin';
	$leftlink = "currencies";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Edit Currency:"; // Title of this page. (REQUIRED)
	$banner_icon = ""; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $info['chrCurrency']; // Extra information for Page. (NOT REQUIRED)
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

					<div class='FormName'>Currency Name <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrCurrency" id="chrCurrency" maxlength="150" size="50" value='<?=$info['chrCurrency']?>' /></div>

					<div class='FormName'>ISO 4217 Name (Short Name) <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrShort" id="chrShort" maxlength="5" size="10" value='<?=$info['chrShort']?>' /></div>
					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<div class='FormName'>Symbol <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrSymbol" id="chrSymbol" maxlength="3" size="10" value='<?=$info['chrSymbol']?>' /></div>

					<div class='FormName'>Show this IM Type <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="radio" name="bShow" id="bShow0" value="0" <?=(!$info['bShow']?'checked="checked"':"")?> /> No &nbsp;&nbsp;&nbsp; <input type="radio" name="bShow" id="bShow1" value="1" <?=($info['bShow']?'checked="checked"':"")?> /> Yes</div>

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
