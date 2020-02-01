<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	if(!isset($_REQUEST['key'])) {
		errorPage('Invalid Thread');
	}
	# Getting topic
	
	$q = "SELECT Th.ID, Th.chrKEY, Th.bDeleted, Th.dtCreated, Th.idPersonCreated, Th.chrThread, Th.txtDescription, Th.bShow,
						P.chrFirst, P.chrLast, P.chrKEY AS chrPersonKEY, T.chrKEY AS chrTopicKEY, T.chrTopic
						FROM Threads Th
						JOIN Topics T ON Th.idTopic=T.ID
						JOIN People P ON Th.idPersonCreated=P.ID
						WHERE Th.chrKEY='". $_REQUEST['key'] ."'
						LIMIT 1";
	
	$info = db_query($q,"getting Discussion Thread info",1);
	if($info['ID'] == "") { errorPage('Invalid Thread'); }

	// If a post occured
	if(isset($_POST['txtPost'])) { // When doing isset, use a required field.  Faster than the php count funtion.

		$table = 'Posts'; # added so not to forget to change the insert AND audit

		$q = "INSERT INTO ". $table ." SET 
			chrKEY = '". makekey() ."',
			txtPost = '". encode($_POST['txtPost']) ."',
			dtCreated = NOW(),
			idThread = '".$_POST['thread']."',
			idPersonCreated = '".$_SESSION['idPerson']."'
		";
		
		# if there database insertion is successful	
		if(db_query($q,"Insert into ". $table)) {

			$ThreadSub = db_query("SELECT P.chrFirst,P.chrLast,P.chrEmail FROM Subscriptions AS S JOIN People AS P ON S.idPerson=P.ID WHERE S.idType='1' AND S.idItem='".$info['ID']."' AND idPerson != '".$_SESSION['idPerson']."'","Checking for Subscritions to Thread");
			while($row = mysqli_fetch_assoc($ThreadSub)) {
				$info['chrSubject'] = "A reply has been made to your thread - Rhythm Business Portal";
				$info['chrEmail'] = $row['chrEmail'];
				$info['txtMsg'] = "<p>Dear ".$row['chrFirst']." ".$row['chrLast'].",</p>
						<p>This message is to notify you that ".$_SESSION['chrFirst']." ".$_SESSION['chrLast']." has made a reply to your thread: ".$info['chrThread']."</p>
						<p>Please use the following link to view the thread:</p>
						<p><a href='http://my.techitsolutions.com/discussions/thread.php?key=".$info['chrKEY']."'>http://my.techitsolutions.com/discussions/thread.php?key=".$info['chrKEY']."</a></p>
						<p>Thank you<br />Rhythm Business Portal</p>";
				// Send E-mail
				include($BF.'includes/emailer.php');
			}
			
			// This is the code for inserting the Audit Page
			// Type 1 means ADD NEW RECORD, change the TABLE NAME also
			global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
			$newID = mysqli_insert_id($mysqli_connection);
		
			$q = "INSERT INTO Audit SET 
				idType=1, 
				idRecord='". $newID ."',
				txtNewValue='". encode($_POST['chrPost']) ."',
				dtDateTime=now(),
				chrTableName='". $table ."',
				idPerson='". $_SESSION['idPerson'] ."'
			";
			db_query($q,"Insert audit");
			//End the code for History Insert 
		
			$_SESSION['InfoMessage'] = "Reply has been added successfully";
			header("Location: thread.php?key=".$_POST['key']);
			die();
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occured while trying to add this thread.');
		}
	}

	$title = "Add Reply";	
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

		if(errCustom('txtPost', "You must enter a Reply", 'tinyMCE')) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
</script>
<?	

	$section = 'discussions';
	$leftlink = "";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Add Reply"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-discussions.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".$info['chrThread'].")"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please fill in all the fields and press the "Submit" button.'; // Instructions or description. (NOT REQUIRED)

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
				</div>
				<div class="WrapperBottom"></div>
			</td>
			<td class="PostArea">
				<div class="Wrapper">
					<div id="errors"></div>
					
					<div class='FormName'>Your Reply <span class='FormRequired'>(Required)</span></div>
					<div class='FormField'><textarea id="txtPost" name="txtPost" rows="30" style="width:100%;" wrap="virtual"></textarea></div>
				
					<div>
						<input type='hidden' name='key' value="<?=$_REQUEST['key']?>" />
						<input type='hidden' name='thread' value="<?=$info['ID']?>" />
						<input type='submit' name='submit' value='Submit' />
					</div>
				</div>
				<div class="WrapperBottom"></div>
			</td>
		</tr>
	</table>
</form>


<?	include($BF .'includes/bottom.php'); ?>