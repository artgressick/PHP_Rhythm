<?
	$BF = ""; #This is the BASE FOLDER.  This should be located at the top of every page with the proper set of '../'s to find the root folder 
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	$q = "SELECT chrFirst, chrLast, chrEmail FROM People WHERE ID='".$_SESSION['idPerson']."'";
	$info = db_query($q,"Getting Person Info",1);
	
		// If a post occured
	if(isset($_POST['chrEmail'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
	
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'People';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr, 'chrFirst',$info['chrFirst'],$audit,$table,$_SESSION['idPerson']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrLast',$info['chrLast'],$audit,$table,$_SESSION['idPerson']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrEmail',$info['chrEmail'],$audit,$table,$_SESSION['idPerson']);
		
		if($_POST['chrPassword'] != "" && $_POST['chrPassword'] == $_POST['chrPassword2']) {
			list($mysqlStr,$audit) = set_strs_password($mysqlStr,'chrPassword',$info['chrPassword'],$audit,$table,$_SESSION['idPerson']);
		}
		
			
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { 
			$_SESSION['InfoMessage'] = "Your profile has been successfully updated in the Database.";
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $_SESSION['idPerson']);
			
			$_SESSION['chrFirst'] = $_POST['chrFirst'];
			$_SESSION['chrLast'] = $_POST['chrLast'];
			$_SESSION['chrEmail'] = $_POST['chrEmail'];

		 } else {
		 	$_SESSION['InfoMessage'] = "No Changes have been made to your profile";
		 }
		
		header("Location: index.php");
		die();	
	}


	


	$title = 'My Profile';	
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('chrFirst', "You must enter a First Name.")) { totalErrors++; }
		if(errEmpty('chrLast', "You must enter a Last name")) { totalErrors++; }
		if(errEmpty('chrEmail',"You must enter a E-mail Address")) { 
			totalErrors++; 
		} else {
			if(errEmail('chrEmail','','This is not a valid Email Address')) { totalErrors++; }
		}
		if(errPasswordsMatch('chrPassword','chrPassword2',"Passwords must match")) { totalErrors++; }
		
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	$section = '';
	$leftlink = "myprofile";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Edit My Profile"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-person.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please update the information below and press the "Update Information" when you are done making changes.'; // Instructions or description. (NOT REQUIRED)
	include($BF .'includes/left_home.php');
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

					<div class='FormName'>First Name <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrFirst" id="chrFirst" maxlength="200" value='<?=$info['chrFirst']?>' /></div>

					<div class='FormName'>Last Name <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrLast" id="chrLast" maxlength="200" value='<?=$info['chrLast']?>' /></div>

					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<div class='FormName'>E-mail Address <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrEmail" id="chrEmail" maxlength="100" value='<?=$info['chrEmail']?>' /></div>

					<div class='FormName'>Password <span class='FormRequired'>(Only Required if Changing)</span></div>
					<div class='FormField'><input type="password" name="chrPassword" id="chrPassword" maxlength="30" /></div>

					<div class='FormName'>Confirm Password <span class='FormRequired'>(Only Required if Changing)</span></div>
					<div class='FormField'><input type="password" name="chrPassword2" id="chrPassword2" maxlength="30" /></div>
					
				</td>
			</tr>				
		</table>
	</div>
		<table cellpadding="0" cellspacing="0" border="0" class="optionsBottom" style="width:810">
<?
	optionsBar();
?> 
	</table>
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
