<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	if(!isset($_REQUEST['key'])) {
		errorPage('Invalid Post');
	}
	# Getting topic
	
	$q = "SELECT Posts.ID, Posts.chrKEY, Posts.txtPost, Th.chrKEY as chrThKEY, Th.chrThread, Th.bShow, Th.dtCreated, P.chrFirst, P.chrLast, P.chrKEY AS chrPersonKEY, Th.txtDescription, Posts.dtCreated AS dtPost
			FROM Posts
			JOIN Threads Th ON Posts.idThread=Th.ID
			JOIN Topics T ON Th.idTopic=T.ID
			JOIN People P ON Th.idPersonCreated=P.ID
			WHERE Posts.chrKEY='".$_REQUEST['key']."'";
	
	$info = db_query($q,"getting Discussion Thread info",1);
	if($info['ID'] == "") { errorPage('Invalid Post'); }

	// If a post occured
	if(isset($_POST['txtPost'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
	
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'Posts';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'txtPost',$info['txtPost'],$audit,$table,$info['ID']);
		
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { 
			$_SESSION['InfoMessage'] = "Post has been successfully updated in the Database.";
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['ID']);
		} else {
			$_SESSION['InfoMessage'] = "No Changes have been made to the Post";
		}
		
		header("Location: thread.php?key=".$info['chrThKEY']);
		die();	
	}
	

	$title = "Edit Reply";	
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

		if(errCustom('txtPost', "You must enter a Post", 'tinyMCE')) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	$section = 'discussions';
	$leftlink = "";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Edit Reply"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-discussions.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".$info['chrThread'].")"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please update the information below and press the "Update Information" when you are done making changes.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_discussion.php');

?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>

	<table cellpadding="0" cellspacing="0" border="0" style="width:830px;">
		<tr>
			<td class="UserInfo">
				<div class="Wrapper">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><img src="<?=$BF?>avatars/default.gif" width="50" height="50" /></td>
							<td style="font-size:10px; line-height:14px; vertical-align:top;" width="100%">
								<div style="padding-left:5px;"><a href="<?=$BF?>crm/viewcontact.php?key=<?=$info['chrPersonKEY']?>"><?=$info['chrFirst'] ." ". $info['chrLast']?></a></div>
							</td>
						</tr>
					</table>
					<div class="PostedDate">Started <?=date('D., F j, Y - g:ia',strtotime($info['dtCreated']))?></div>
				</div>
				<div class="WrapperBottom"></div>
			</td>
			<td class="PostArea">
				<div class="Wrapper">
					<div class="Question"><?=$info['chrThread']?></div>
					<div id="Threads<?=$info['ID']?>" class="Post" style="max-height:200px;"><div><?=(!$info['bShow'] ? "<div class='Removed'>Removed by User</div>" : decode($info['txtDescription']))?></div></div>				
				</div>
				<div class="WrapperBottom"></div>
			</td>
		</tr>
	</table>

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
					<div class="PostedDate">Started <?=date('D., F j, Y - g:ia',strtotime($info['dtPost']))?></div>
				</div>
				<div class="WrapperBottom"></div>
			</td>
			<td class="PostArea">
				<div class="Wrapper">
					<div id="errors"></div>
					
					<div class='FormName'>Your Reply <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><textarea id="txtPost" name="txtPost" rows="30" style="width:100%;" wrap="virtual"><?=decode($info['txtPost'])?></textarea></div>
				
					<div>
						<input type='hidden' name='key' value="<?=$_REQUEST['key']?>" />
						<input type='submit' name='submit' value='Update Information' />
					</div>
				</div>
				<div class="WrapperBottom"></div>
			</td>
		</tr>
	</table>
</form>


<?	include($BF .'includes/bottom.php'); ?>