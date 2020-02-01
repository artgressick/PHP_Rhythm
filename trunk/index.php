<?
	$BF = ""; #This is the BASE FOLDER.  This should be located at the top of every page with the proper set of '../'s to find the root folder 
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	$title = '';	
	include($BF .'includes/meta.php');

	$section = '';
	$leftlink = "";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Welcome to Rhythm Business Portal"; // Title of this page. (REQUIRED)
	$banner_icon = ""; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = ""; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_home.php');

?>
<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>	

<?	include($BF .'includes/bottom.php'); ?>