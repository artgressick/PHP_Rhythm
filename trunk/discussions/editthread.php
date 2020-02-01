<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	if(!isset($_REQUEST['key'])) {
		errorPage('Invalid Discussion Topic');
	}
	# Getting topic
	$info = db_query("SELECT Th.ID, Th.chrKEY, Th.chrThread, Th.dtCreated, Th.txtDescription FROM Threads Th WHERE Th.chrKEY='". $_REQUEST['key'] ."'","getting Thread info",1);
	if($info['ID'] == "") { errorPage('Invalid Discussion Topic'); }

	$tmp = db_query("SELECT * FROM Subscriptions WHERE idType='1' AND idPerson='".$_SESSION['idPerson']."' AND idItem='".$info['ID']."'","getting subscription status",1);
	
	if ($tmp['idPerson'] == $_SESSION['idPerson']) {
		$info['bNotify'] = 1;
	} else {
		$info['bNotify'] = 0;
	}
	unset($tmp);
	
	// If a post occured
	if(isset($_POST['chrThread'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
	
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'Threads';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrThread',$info['chrThread'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'txtDescription',$info['txtDescription'],$audit,$table,$info['ID']);
		
		if(!isset($_POST['bNotify'])) { $_POST['bNotify'] = 0; }
		$sub_update=false;
		if ($info['bNotify'] != $_POST['bNotify']) {
			$sub_update=true;
			db_query("DELETE FROM Subscriptions WHERE idType='1' AND idPerson='".$_SESSION['idPerson']."' AND idItem='".$info['ID']."'","Clear Subscription");
			if(isset($_POST['bNotify']) && $_POST['bNotify'] == "1") {
				db_query("INSERT INTO Subscriptions SET idPerson='".$_SESSION['idPerson']."', idItem='".$info['ID']."', idType='1'","Insert into Subscriptions");
			}
		}
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '' || $sub_update==true) { 
			$_SESSION['InfoMessage'] = $_POST['chrThread']." has been successfully updated in the Database.";
			if($mysqlStr != '') {
				list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['ID']);
			}
		} else {
			$_SESSION['InfoMessage'] = "No Changes have been made to ".$_POST['chrThread'];
		}
		
		header("Location: thread.php?key=".$info['chrKEY']);
		die();	
	}

	$title = "Edit Thread";	
	include($BF .'includes/meta.php');
?>
<script type="text/javascript" src="<?=$BF?>components/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
	plugins : 'layer,advimage,advlink,emotions,contextmenu,paste,directionality,noneditable,filemanager',
	themes : 'advanced',
	languages : 'en',
	disk_cache : true,
	debug : false
});
</script>
<!-- Needs to be seperate script tags! -->
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		plugins : "layer,advimage,advlink,emotions,contextmenu,paste,directionality,noneditable,filemanager",

		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,anchor,image,cleanup,help,code,separator,sub,sup,separator,charmap,seperator,emotions,separator,forecolor",
		theme_advanced_buttons3 : "",
		
		theme_advanced_toolbar_location : "top",
		theme_advanced_path_location : "bottom",
		extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		external_link_list_url : "example_data/example_link_list.js",
		external_image_list_url : "example_data/example_image_list.js",
		file_browser_callback : "mcFileManager.filebrowserCallBack",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true,
		apply_source_formatting : true,
		
		filemanager_rootpath : "<?=realpath($BF . 'userfiles/'. $_SESSION['chrEmail'])?>/",
		filemanager_path : "<?=realpath($BF . 'userfiles/'. $_SESSION['chrEmail'])?>/",

		relative_urls : false,
		
		<!-- CHANGE URL -->
		document_base_url : "http://my.techitsolutions.com/"
	});
</script>
<!-- /tinyMCE -->
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('chrThread', "You must enter a Thread/Question Title.")) { totalErrors++; }
		if(errCustom('txtDescription', "You must enter a Description", 'tinyMCE')) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	$section = 'discussions';
	$leftlink = "";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Edit Thread"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-discussions.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".$info['chrThread'].")"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please update the information below and press the "Update Information" when you are done making changes.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_discussion.php');

?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>
<form action="" method="post" id="idForm" onsubmit="return error_check()">

	<table cellpadding="0" cellspacing="0" border="0" style="width:830px;">
		<tr>
			<td class="UserInfo">
				<div class="Wrapper">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><img src="<?=$BF?>avatars/default.gif" width="50" height="50" /></td>
							<td style="font-size:10px; line-height:14px; vertical-align:top;" width="100%">
								<div style="padding-left:5px;"><?=$_SESSION['chrFirst'] ." ". $_SESSION['chrLast']?></a></div>
							</td>
						</tr>
					</table>
					<div class="PostedDate">Posted <?=date('D., F j, Y - g:ia',strtotime($info['dtCreated']))?></div>
				</div>
				<div class="WrapperBottom"></div>
			</td>
			<td class="PostArea">
				<div class="Wrapper">
					<div id="errors"></div>
					
					<div class='FormName'>Thread/Question Title <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><input type="text" name="chrThread" id="chrThread" maxlength="255" size="80" value="<?=$info['chrThread']?>"/></div>
					
					<div class='FormName' style="padding: 10px 0px;"><input type="checkbox" name="bNotify" id="bNotify" value="1"<?=($info['bNotify']==1?' checked="checked"':"")?> /> Notify me by E-mail when someone replies to this topic.</div>
									
					<div class='FormName'>Description <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><textarea id="txtDescription" name="txtDescription" rows="30" style="width:100%;" wrap="virtual"><?=decode($info['txtDescription'])?></textarea></div>
				
					<div>
						<input type='hidden' name='key' value="<?=$_REQUEST['key']?>" />
						<input type='submit' name='submit' value='Submit' />
					</div>
				</div>
				<div class="WrapperBottom"></div>
			</td>
		</tr>
	</table>
</form>


<?	include($BF .'includes/bottom.php'); ?>