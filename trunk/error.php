<?php
	$BF = ""; #This is the BASE FOLDER.  This should be located at the top of every page with the proper set of '../'s to find the root folder 
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	$auth_not_required = true;
	require($BF. '_lib.php');

	if(isset($_POST['submit'])) {
		header('Location: index.php');
		die();
	}

	include($BF. "includes/meta.php");
	$section='';
	include($BF. "includes/top.php");
	include($BF. "includes/left.php");

?>
		<table width="924" border="0" cellpadding="0" cellspacing="0" class="home_content">
			<tr>
				<td width="100%">
					<form id='idForm' name='idForm' method='post' action=''>
					<div style="text-align:center; font-size:14px;"><strong>An Error as occured! This is usually due to missing or incomplete information.</strong></div>
<?
if(isset($_SESSION['chrErrorMsg'])) {
?>
					<div style="text-align:center; font-size:12px; padding-top:20px;">
						<strong>Error Details:</strong>
						<div class='ErrorMessage'><?=$_SESSION['chrErrorMsg']?></div>
					</div>
<?
	unset($_SESSION['chrErrorMsg']);
}
?>
					<div style="text-align:center; padding-top:20px;"><input type="button" id="back" name="back" value="Back" onclick="javascript: history.go(-1);" />&nbsp;&nbsp;&nbsp;<input type="submit" id="submit" name="submit" value="Home" /></div>
					</form>
				</td>
			</tr>
		</table>
<?
	include($BF. "includes/bottom.php");
?>