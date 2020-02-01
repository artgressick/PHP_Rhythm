<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	// If a post occured
	if(isset($_POST['chrCurrency'])) { // When doing isset, use a required field.  Faster than the php count funtion.
		
		$table = 'Currencies'; # added so not to forget to change the insert AND audit

		$tmp = db_query("SELECT intOrder FROM ".$table." ORDER BY intOrder DESC LIMIT 1","Getting Highest Order",1); // Gets the highest intOrder value

		$q = "INSERT INTO ". $table ." SET 
			chrKEY = '". makekey() ."',
			chrCurrency = '". encode($_POST['chrCurrency']) ."',
			chrShort = '". strtoupper(encode($_POST['chrShort'])) ."',
			chrSymbol = '". htmlentities($_POST['chrSymbol'],ENT_QUOTES,"UTF-8") ."',
			bShow = '". $_POST['bShow'] ."',
			intOrder = '". ($tmp['intOrder'] + 1) ."'
		";
		
		# if there database insertion is successful	
		if(db_query($q,"Insert into ". $table)) {

			// This is the code for inserting the Audit Page
			// Type 1 means ADD NEW RECORD, change the TABLE NAME also
			global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
			$newID = mysqli_insert_id($mysqli_connection);
		
			$q = "INSERT INTO Audit SET 
				idType=1, 
				idRecord='". $newID ."',
				txtNewValue='". encode($_POST['chrCurrency']) ."',
				dtDateTime=now(),
				chrTableName='". $table ."',
				idPerson='". $_SESSION['idPerson'] ."'
			";
			db_query($q,"Insert audit");
			//End the code for History Insert 
		
			$_SESSION['InfoMessage'] = "New Currency: " . encode($_POST['chrCurrency']) . " has been added";
			header("Location: ". $_POST['moveTo']);
			die();
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occured while trying to add this Currency.');
		}
	}
	
	$title = 'Add Currency';
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
	$banner_title = "Add Currency"; // Title of this page. (REQUIRED)
	$banner_icon = ""; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "You are adding a Currency to the database."; // Instructions or description. (NOT REQUIRED)
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
					<div class='FormField'><input type="text" name="chrCurrency" id="chrCurrency" maxlength="150" size="50" /></div>

					<div class='FormName'>ISO 4217 Name (Short Name) <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrShort" id="chrShort" maxlength="5" size="10" /></div>
					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<div class='FormName'>Symbol <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrSymbol" id="chrSymbol" maxlength="3" size="10" /></div>

					<div class='FormName'>Show this Currency <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="radio" name="bShow" id="bShow0" value="0" checked="checked" /> No &nbsp;&nbsp;&nbsp; <input type="radio" name="bShow" id="bShow1" value="1" /> Yes</div>
					
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
			<td class="right"><input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addcurrency.php';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='currencies.php';" /></td>
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}
?>
