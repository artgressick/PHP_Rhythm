<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	if(!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
		 errorPage('Invalid Entry');
	}

	# Getting all original data from this record
	$info = db_query("SELECT * FROM SiteAccess WHERE ID=". $_REQUEST['id'],"getting access_site info",1);
	if($info['ID'] == "") { errorPage('Invalid Entry'); }
	$person = db_query("SELECT chrFirst,chrLast FROM People WHERE ID=". $info['idPerson'],"getting person info",1);

	// If a post occured
	if(isset($_POST['idDivision'])) { // When doing isset, use a required field.  Faster than the php count funtion.
		
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'SiteAccess';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idManager',$info['idManager'],$audit,$table,$_POST['id']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idDivision',$info['idDivision'],$audit,$table,$_POST['id']);
		
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') {
			$_SESSION['InfoMessage'] = $person['chrFirst'] ." ". $person['chrLast'] ." has been successfully updated in the Database.";
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $_POST['id']);
		} else {
			$_SESSION['InfoMessage'] = "No Changes have been made to ".$person['chrFirst']." ".$person['chrLast'];
		}
		header("Location: index.php");
		die();	
	}
	
	$title = 'Edit Site User';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('idDivision', "You must select a Division")) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('idDivision').focus()";

	$section = 'admin';
	$leftlink = "siteusers";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Edit Site User:"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-person.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $person['chrFirst']." ".$person['chrLast']; // Extra information for Page. (NOT REQUIRED)
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
		<table width="100%" class="twoCol800" id="twoCol800" cellpadding="0" cellspacing="0">
			<tr>
				<td class="left">

					<div class='FormName'>Person</div>
					<div class='FormDisplay'><?=$person['chrFirst']?> <?=$person['chrLast']?></div>
					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<div class='FormName'>Manager/Direct Report</div>
					<div class='FormField'>
						<select id='idManager' name='idManager'>
						<option value=''>-Select Manager-</option>
<?	$q = "SELECT ID,chrFirst,chrLast
		FROM People
		WHERE !bDeleted AND People.ID != '". $info['idPerson'] ."'
			AND People.ID IN (SELECT ppl.ID FROM People as ppl JOIN SiteAccess ON SiteAccess.idPerson=ppl.ID WHERE !bDeleted)
		ORDER BY chrLast,chrFirst
	";
	$results = db_query($q,"getting managers");
	while($row = mysqli_fetch_assoc($results)) { ?>
							<option<?=($row['ID'] == $info['idManager'] ? ' selected="selected"' : '')?> value='<?=$row['ID']?>'><?=$row['chrLast']?>, <?=$row['chrFirst']?></option>
<?	} ?>
						</select>
					</div>

					<div class='FormName'>Division <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'>						
						<select id='idDivision' name='idDivision'>
<?	$q = "SELECT ID,chrDivision FROM Divisions WHERE !bDeleted ORDER BY chrDivision";
	$results = db_query($q,"getting divisions");
	while($row = mysqli_fetch_assoc($results)) { ?>
							<option<?=($row['ID'] == $info['idDivision'] ? ' selected="selected"' : '')?> value='<?=$row['ID']?>'><?=$row['chrDivision']?></option>
<?	} ?>
						</select>
					</div>

				</td>
			</tr>				
		</table>
	</div>
	<table cellpadding="0" cellspacing="0" border="0" class="optionsBottom" style="width:810">
<?
	optionsBar();
?> 
	</table>
	<input type='hidden' name='id' value='<?=$_REQUEST['id']?>' />
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
