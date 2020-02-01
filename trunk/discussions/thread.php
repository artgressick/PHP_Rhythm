<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	if(!isset($_REQUEST['key'])) {
		errorPage('Invalid Discussion Topic');
	}
	# Getting topic
	$info = db_query("SELECT Th.ID, Th.chrKEY, Th.bDeleted, Th.bShow, Th.dtCreated, Th.idPersonCreated, Th.chrThread, Th.txtDescription,
						P.chrFirst, P.chrLast, P.chrKEY AS chrPersonKEY, T.chrKEY AS chrTopicKEY, T.chrTopic, G.chrGroup
						FROM Threads Th
						JOIN Topics T ON Th.idTopic=T.ID
						JOIN Groups G ON T.idGroup=G.ID
						JOIN People P ON Th.idPersonCreated=P.ID
						WHERE Th.chrKEY='". $_REQUEST['key'] ."'
						LIMIT 1","getting Discussion Thread info",1);
	if($info['ID'] == "") { errorPage('Invalid Discussion Topic'); }


	$title = $info['chrTopic'];	
	include($BF .'includes/meta.php');

	$q = "SELECT Posts.ID, Posts.chrKEY, Posts.bDeleted, Posts.bShow, Posts.dtCreated, Posts.idPersonCreated, Posts.txtPost,
			P.chrFirst, P.chrLast, P.chrKEY AS chrPersonKEY
			FROM Posts
			JOIN People P ON Posts.idPersonCreated=P.ID
			WHERE Posts.idThread='".$info['ID']."' AND !Posts.bDeleted
			ORDER BY dtCreated
			";
	$results = db_query($q,"getting Posts");

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	

	$section = 'discussions';
	$leftlink = "";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = $info['chrThread']; // Title of this page. (REQUIRED)
	$banner_icon = "icons-discussions.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".$info['chrGroup']." : ".$info['chrTopic'].") (".mysqli_num_rows($results)." Replies)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = ""; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_discussion.php');

	$replylink = "javascript:window.location.href='addreply.php?key=".$info['chrKEY']."'";
	$newthread = "javascript:window.location.href='addthread.php?key=".$info['chrTopicKEY']."'";

?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>
<form name='idForm' id='idForm' action='' method="post">		
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
<?
				if($_SESSION['idPerson'] == $info['idPersonCreated']) {
?>
					<div class="Buttons">
<?
	// this is the swap code for the button --- value="<?=($info['bShow']?"Hide":"Show")
?>	
						<a href="editthread.php?key=<?=$info['chrKEY']?>"><img src="<?=$BF?>/images/discussion_bottom-edit.gif"  style="cursor: pointer;" title="Edit Your Post" alt="Edit Post" /></a><img src="<?=$BF?>/images/discussion_bottom-divider.gif" /><img src="<?=$BF?>/images/discussion_bottom-<?=($info['bShow']?"remove":"show")?>.gif" id="Threads<?=$info['ID']?>btn" alt="<?=($info['bShow']?"Remove Post":"Show Post")?>" title="<?=($info['bShow']?"Hides your Post from View.":"Shows your Post so others can see.")?>" onclick="quickhide('<?=$BF?>','<?=$info['chrKEY']?>','Threads','<?=$info['ID']?>','<?=$_SESSION['idPerson']?>');" style="cursor: pointer;" />
					</div>
<?
				} else {
?>
				<div class="WrapperBottom"></div>
<?
				}
?>
			</td>
			<td class="PostArea">
				<div class="Wrapper">
					<div class="Question"><?=$info['chrThread']?></div>
					<div id="Threads<?=$info['ID']?>" class="Post"><div><?=(!$info['bShow'] ? "<div class='Removed'>Removed by User</div>" : decode($info['txtDescription']))?></div></div>				
				</div>
				<div class="WrapperBottom"></div>
				<div class="Buttons"><input type="button" value="Post Reply" onclick="<?=$replylink?>" /> &nbsp;&nbsp; <input type="button" value="Start A New Thread" onclick="<?=$newthread?>" /></div>
			</td>
		</tr>
	</table>

	
<?
	if(mysqli_num_rows($results) > 0) {
?>
	<table cellpadding="0" cellspacing="0" border="0" style="width:830px;">

<?
	$count=0;
	while ($row = mysqli_fetch_assoc($results)) { 
?>
		<tr>
			<td class="UserInfo">
				<div class="Wrapper">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><img src="<?=$BF?>avatars/default.gif" width="50" height="50" /></td>
							<td style="font-size:10px; line-height:14px; vertical-align:top;" width="100%">
								<div style="padding-left:5px;"><a href="<?=$BF?>crm/viewcontact.php?key=<?=$row['chrPersonKEY']?>"><?=$row['chrFirst'] ." ". $row['chrLast']?></a></div>
							</td>
						</tr>
					</table>
					<div class="PostedDate">Posted <?=date('D., F j, Y - g:ia',strtotime($row['dtCreated']))?></div>
				</div>
<?
				if($_SESSION['idPerson'] == $row['idPersonCreated']) {
?>
					<div class="Buttons">
						<a href="editreply.php?key=<?=$row['chrKEY']?>"><img src="<?=$BF?>/images/discussion_bottom-edit.gif"  style="cursor: pointer;" title="Edit Your Post." alt="Edit Post" /></a><img src="<?=$BF?>/images/discussion_bottom-divider.gif" /><img src="<?=$BF?>/images/discussion_bottom-<?=($row['bShow']?"remove":"show")?>.gif" id="Posts<?=$row['ID']?>btn" alt="<?=($row['bShow']?"Remove Post":"Show Post")?>" title="<?=($row['bShow']?"Hides your Post from View.":"Shows your Post so others can see.")?>" onclick="quickhide('<?=$BF?>','<?=$row['chrKEY']?>','Posts','<?=$row['ID']?>','<?=$_SESSION['idPerson']?>');" style="cursor: pointer;" />
					</div>
<?
				} else {
?>
				<div class="WrapperBottom"></div>
<?
				}
?>

			</td>
			<td class="PostArea">
				<div class="Wrapper">
					<div id="Posts<?=$row['ID']?>" class="Post"><?=(!$row['bShow'] ? "<div class='Removed'>Removed by User</div>" : $row['txtPost'])?></div>
				</div>
				<div class="WrapperBottom"></div>
			</td>
		</tr>
<?
	}
?>
	</table>	
	<div class="PostButtons"><input type="button" value="Post Reply" onclick="<?=$replylink?>" /> &nbsp;&nbsp; <input type="button" value="Start A New Thread" onclick="<?=$newthread?>" /></div>
<?
	}
?>
</form>
<?	include($BF .'includes/bottom.php'); ?>