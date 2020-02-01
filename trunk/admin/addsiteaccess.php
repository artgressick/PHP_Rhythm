<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	// If a post occured
	if(isset($_POST['idPerson'])) { // When doing isset, use a required field.  Faster than the php count funtion.

		$table = 'SiteAccess'; # added so not to forget to change the insert AND audit

		$q = "INSERT INTO ". $table ." SET 
			idPerson = '". $_POST['idPerson'] ."',
			idLevel = '1',
			idDivision = '". $_POST['idDivision'] ."',
			idManager = '". $_POST['idManager'] ."',
			dtCreated=now()
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
				txtNewValue='". encode($_POST['idPerson']) ."',
				dtDateTime=now(),
				chrTableName='". $table ."',
				idPerson='". $_SESSION['idPerson'] ."'
			";
			db_query($q,"Insert audit");
			//End the code for History Insert 
		
			$_SESSION['InfoMessage'] = "Site Access has been granted successfully.";

			// Grab Person Information
			$q = "SELECT chrFirst, chrLast, chrEmail, chrPassword FROM People WHERE ID='".$_POST['idPerson']."'";
			$info = db_query($q,"Getting Person Information For Email",1);
			
			if($info['chrPassword'] == "") {
				
				// Make a password for this person
				$newpass = "rhythm".mt_rand(100000, 999999);
				
				// Update their profile with the new password
				db_query("UPDATE People SET chrPassword='".sha1($newpass)."' WHERE ID='".$_POST['idPerson']."'","Set Password");
				
				$info['chrSubject'] = "Access Granted - Rhythm Business Portal";

				$info['txtMsg'] = "<p>Dear ".$info['chrFirst']." ".$info['chrLast'].",</p>
						<p>This E-mail is to notify you that you have been granted site access to the Rhythm Business Portal. 
							Please use the link below with the listed E-mail address and Password log in.</p>
						<p>The password listed is case sensitive and must be entered exactly as it appears below with no spacing. 
							Once logged in you may change your password at anytime (Recommended) by clicking on the 'My Profile' link.</p>
						<p>Login At: <a href='http://my.techitsolutions.com'>http://my.techitsolutions.com</a></p>
						<p>Email Address: ".$info['chrEmail']."</p>
						<p>Password: ".$newpass."</p>
						<p>Thank you<br />Rhythm Business Portal</p>";

				// Send E-mail
				include($BF.'includes/emailer.php');
			
				$_SESSION['InfoMessage'] .= " E-mail sent.";
			
			}

			header("Location: ". $_POST['moveTo']);
			die();
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occured while trying to add site access.');
		}
	}
	
	$title = 'Add Site User';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('idPerson', "You must select a Person")) { totalErrors++; }
		if(errEmpty('idDivision', "You must select a Division")) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('idPerson').focus()";

	$section = 'admin';
	$leftlink = "siteusers";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Add Site User"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-person.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "You are adding a person who can access the website and add information to the database."; // Instructions or description. (NOT REQUIRED)
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

					<div class='FormName'>Person <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'>
						<select id='idPerson' name='idPerson'>
							<option value="">-Select Person-</option>
<?	$q = "SELECT ID,chrFirst,chrLast
		FROM People
		WHERE !bDeleted
			AND People.ID NOT IN (SELECT ppl.ID FROM People as ppl JOIN SiteAccess ON SiteAccess.idPerson=ppl.ID WHERE !bDeleted)
		ORDER BY chrLast,chrFirst
	";
	$results = db_query($q,"getting people");
	while($row = mysqli_fetch_assoc($results)) { ?>
							<option value='<?=$row['ID']?>'><?=$row['chrLast']?>, <?=$row['chrFirst']?></option>
<?	} ?>
						</select>
					</div>
					
				</td>
				<td class="gutter"></td>
				<td class="right">

					<div class='FormName'>Manager/Direct Report</div>
					<div class='FormField'>
						<select id='idManager' name='idManager'>
							<option value="">-Select Manager-</option>
<?	$q = "SELECT ID,chrFirst,chrLast
		FROM People
		WHERE !bDeleted
			AND People.ID IN (SELECT ppl.ID FROM People as ppl JOIN SiteAccess ON SiteAccess.idPerson=ppl.ID WHERE !bDeleted)
		ORDER BY chrLast,chrFirst
	";
	$results = db_query($q,"getting managers");
	while($row = mysqli_fetch_assoc($results)) { ?>
							<option value='<?=$row['ID']?>'><?=$row['chrLast']?>, <?=$row['chrFirst']?></option>
<?	} ?>
						</select>
					</div>

					<div class='FormName'>Division <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'>						
						<select id='idDivision' name='idDivision'>
							<option value="">-Select Division-</option>
<?	$q = "SELECT ID,chrDivision FROM Divisions WHERE !bDeleted ORDER BY chrDivision";
	$results = db_query($q,"getting divisions");
	while($row = mysqli_fetch_assoc($results)) { ?>
							<option value='<?=$row['ID']?>'><?=$row['chrDivision']?></option>
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
	<input type='hidden' name='moveTo' id='moveTo' />
	</form>
	

		
<?
	include($BF. "includes/bottom.php");

function optionsBar() {
global $BF;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="right"><input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addsiteaccess.php';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='index.php';" /></td>			
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}
?>
