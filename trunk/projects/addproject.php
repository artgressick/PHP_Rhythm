<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	// If a post occured
	if(isset($_POST['chrProject'])) { // When doing isset, use a required field.  Faster than the php count funtion.

		$table = 'Projects'; # added so not to forget to change the insert AND audit

		$q = "INSERT INTO ". $table ." SET 
			chrKEY = '". makekey() ."',
			chrProject = '". encode($_POST['chrProject']) ."',
			idPerson = ". $_SESSION['idPerson'] .",
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
				txtNewValue='". encode($_POST['chrProject']) ."',
				dtDateTime=now(),
				chrTableName='". $table ."',
				idPerson='". $_SESSION['idPerson'] ."'
			";
			db_query($q,"Insert audit");
			//End the code for History Insert 
	
			$_SESSION['InfoMessage'] = $_POST['chrProject']. " has been successfully added to the Database.";
			header("Location: ". $_POST['moveTo']);
			die();
			
			
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occured while trying to add the customer "'. $_POST['chrCustomer'] .'".');
		}
	}
	
	$title = 'Add Project';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('chrProject', "You must enter a Project Name.")) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
	
</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('chrProject').focus()";

	$section = 'projects';
	$leftlink = "viewprojects";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Add Projects"; // Title of this page. (REQUIRED)
	$banner_icon = "projects/images/icons-project.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".mysqli_num_rows($results)." results)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "To add a new project enter the information below and click continue"; // Instructions or description. (NOT REQUIRED)
	include($BF .'projects/includes/left_project.php');
	
?>
	<form action="" method="post" id="idForm" onsubmit="return error_check()">
	

	<div class='marginbottom10'>
	<input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addproject.php';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='index.php';" /> <span style='font-size: 10px;'><span style='color: red;'>*</span> All red Asterix fields are required.</span>
	</div>

	<div id='errors' style='width: 830px;'></div>
	<table width="830" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" width="410">

				<div class='bluebox'>
					<table cellspacing="0" cellpadding="0" class='bluetop'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-tasks.gif' alt='Project' /></td>
							<td class='colorBoxTitle'>Project Information</td>
						</tr>
					</table>

					<div class='colorBoxPadding'>
						<div class='FormName'><span class='red'>*</span> Project Name</div>
						<div class='FormField'><input type="text" name="chrProject" id="chrProject" style='width: 325px;' maxlength="200" /></div>
					</div>
	
					<div><img src="../images/cap_bottom-410-blue.gif" /></div>
				</div>

			</td>
			<td width="10"><!-- gutter --></td>
			<td valign="top" width="410">

			<!-- This is left blank until we get more information -->

			</td>
		</tr>	
	</table>
	
	<div class='margintop10;'>
	<input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addproject.php';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='index.php';" /> <span style='font-size: 10px;'><span style='color: red;'>*</span> All red Asterix fields are required.</span>
		<input type='hidden' name='moveTo' id='moveTo' />
	</div>

	</form>	
<?
	include($BF. "includes/bottom.php");
?>
