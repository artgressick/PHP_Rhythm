<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	if(!isset($_REQUEST['key'])) {
		errorPage('Invalid Discussion Topic');
	}
	# Getting topic
	$info = db_query("SELECT T.ID, T.chrKEY, chrTopic, chrGroup FROM Topics T JOIN Groups G ON T.idGroup=G.ID WHERE !T.bDeleted AND !G.bDeleted AND T.bShow AND G.bShow AND T.chrKEY='". $_REQUEST['key'] ."'","getting Discussion Topic info",1);
	if($info['ID'] == "") { errorPage('Invalid Discussion Topic'); }


	$title = $info['chrTopic'];	
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php');
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = " dtLastPost DESC, dtCreated DESC, ID"; } # This sets the default column order.  Asc by default.

	$q = "SELECT T.ID, T.chrKEY, T.chrThread, T.dtCreated, P.chrKEY AS chrPersonKEY, P.chrFirst, P.chrLast,
			(SELECT COUNT(ID) FROM Posts WHERE Posts.idThread=T.ID AND !Posts.bDeleted) AS intPosts,
			(SELECT dtCreated FROM Posts WHERE Posts.idThread=T.ID AND !Posts.bDeleted ORDER BY Posts.dtCreated DESC LIMIT 1) as dtLastPost,
			(SELECT CONCAT(chrLast,', ', chrFirst) 
				FROM Posts
				JOIN People ON Posts.idPersonCreated=People.ID 
				WHERE Posts.idThread=T.ID AND !Posts.bDeleted ORDER BY Posts.dtCreated DESC LIMIT 1) as chrLastPostBy
		FROM Threads T
		JOIN People P ON T.idPersonCreated=P.ID
		WHERE T.bAnnouncement AND T.idTopic='".$info['ID']."' 
		ORDER BY dtCreated DESC";
	$announcments = db_query($q,"getting Announcements ");

	$q = "SELECT T.ID, T.chrKEY, T.chrThread, T.dtCreated, P.chrKEY AS chrPersonKEY, P.chrFirst, P.chrLast,
			(SELECT COUNT(ID) FROM Posts WHERE Posts.idThread=T.ID AND !Posts.bDeleted) AS intPosts,
			(SELECT dtCreated FROM Posts WHERE Posts.idThread=T.ID AND !Posts.bDeleted ORDER BY Posts.dtCreated DESC LIMIT 1) as dtLastPost,
			(SELECT CONCAT(chrLast,', ', chrFirst) 
				FROM Posts
				JOIN People ON Posts.idPersonCreated=People.ID 
				WHERE Posts.idThread=T.ID ORDER BY Posts.dtCreated DESC LIMIT 1) as chrLastPostBy
		FROM Threads T
		JOIN People P ON T.idPersonCreated=P.ID
		WHERE !T.bAnnouncement AND T.idTopic='".$info['ID']."' 
		ORDER BY ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];
	$results = db_query($q,"getting Threads ");

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	

	$section = 'discussions';
	$leftlink = "";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = $info['chrGroup']." : ".$info['chrTopic']; // Title of this page. (REQUIRED)
	$banner_icon = "icons-discussions.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".mysqli_num_rows($results) ." Threads)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = ""; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_discussion.php');

?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>
<form name='idForm' id='idForm' action='' method="post">

	<table width="830" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" style="width:410px;">
				<table cellpadding="0" cellspacing="0" class="dis_green">
					<tr>
						<td class="green_top">
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td style="width:20px;"><img src="<?=$BF?>images/nano-notes.gif" alt="Icon" /></td>
									<td>5 Most Recent Threads</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding:0 10px 0 10px;">
<?
				$q = "SELECT Th.chrKEY, Th.dtCreated, chrThread, chrFirst, chrLast, P.chrKEY AS chrPKEY, T.chrKEY AS chrTKEY
						FROM Threads Th
						JOIN People P ON Th.idPersonCreated=P.ID
						JOIN Topics T ON Th.idTopic=T.ID
						WHERE !T.bDeleted AND T.bShow AND !Th.bDeleted AND T.ID='".$info['ID']."'
						ORDER BY Th.dtCreated DESC
						LIMIT 5
					";
				$recent = db_query($q,"Getting Most Receet 5 Threads");
				
				if(mysqli_num_rows($recent) > 0) {
?>
							<ol>
<?
				}
				
				$count=0;
				while ($row = mysqli_fetch_assoc($recent)) {
					$count++;
?>
								<li>
									<a href="thread.php?key=<?=$row['chrKEY']?>"><?=(strlen($row['chrThread']) > 45 ? substr($row['chrThread'],0,45).".." : $row['chrThread'] )?></a> <span class='smallfont'>(<?=date('n/j/Y g:ia',strtotime($row['dtCreated']))?>)</span><br />
									<span class='smallfont'><strong>By</strong> <a href="<?=$BF?>crm/viewcontact.php?key=<?=$row['chrPKEY']?>"><?=$row['chrFirst']?> <?=$row['chrLast']?></a> <strong>on</strong> <?=date('n/j/Y g:ia',strtotime($row['dtCreated']))?></span>
					
								</li>
<?
				}
				
				if($count==0) {
?>
							<div style="text-align:center; padding:5px 0 0 0;">No-one has posted any Threads.</div>
<?
				} else {
?>	
							</ol>
<?
				}
?>
						</td>
					</tr>
					<tr>
						<td class="green_bottom"></td>
					</tr>
				</table>
			</td>
			<td style="width:10px;"><!-- Spacer --></td>
			<td valign="top" style="width:410px;">
				<table cellpadding="0" cellspacing="0" class="dis_green">
					<tr>
						<td class="green_top">
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td style="width:20px;"><img src="<?=$BF?>images/nano-notes.gif" alt="Icon" /></td>
									<td>Top 5 Most Active Threads</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding:0 10px 0 10px;">
<?
				$q = "SELECT Th.chrKEY, Th.dtCreated, chrThread, chrFirst, chrLast, P.chrKEY AS chrPKEY, T.chrKEY AS chrTKEY,
							(SELECT COUNT(ID) FROM Posts Po WHERE Po.idThread=Th.ID) as intCount,
							(SELECT CONCAT(Po.dtCreated,'|',PP.chrKey,'|', PP.chrFirst,'|', PP.chrLast) FROM Posts Po JOIN People PP ON Po.idPersonCreated=PP.ID WHERE Po.idThread=Th.ID ORDER BY Po.dtCreated DESC LIMIT 1) as chrPostData
						FROM Threads Th
						JOIN People P ON Th.idPersonCreated=P.ID
						JOIN Topics T ON Th.idTopic=T.ID
						JOIN Posts ON Posts.idThread=Th.ID
						WHERE !T.bDeleted AND T.bShow AND !Th.bDeleted AND Posts.bShow AND T.ID='".$info['ID']."'
						GROUP BY Th.ID
						ORDER BY intCount DESC, chrPostData DESC		
						LIMIT 5
					";
				$top = db_query($q,"Getting Top 5 active Threads");
				
				if(mysqli_num_rows($recent) > 0) {
?>
							<ol>
<?
				}
				
				$count=0;
				while ($row = mysqli_fetch_assoc($top)) {
					$count++;
					$rowdata = explode("|", $row['chrPostData']);	
?>
								<li>
									<a href="thread.php?key=<?=$row['chrKEY']?>"><?=(strlen($row['chrThread']) > 45 ? substr($row['chrThread'],0,45).".." : $row['chrThread'] )?></a> <span class='smallfont'>(<?=date('n/j/Y g:ia',strtotime($row['dtCreated']))?>)</span><br />
									<span class='smallfont'><strong>Last Post By</strong> <a href="<?=$BF?>crm/viewcontact.php?key=<?=$rowdata[1]?>"><?=$rowdata[2]?> <?=$rowdata[3]?></a> <strong>on</strong> <?=date('n/j/Y g:ia',strtotime($rowdata[0]))?> <strong>Total Replies:</strong> <?=number_format($row['intCount'])?></a>
									</span>
					
								</li>
<?
				}
				
				if($count==0) {
?>
							<div style="text-align:center; padding:5px 0 0 0;">No-one has posted any replies.</div>
<?
				} else {
?>	
							</ol>
<?
				}
?>
						</td>
					</tr>
					<tr>
						<td class="green_bottom"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

		
<?
	if(mysqli_num_rows($announcments) > 0) { // Do we have any announcments?
?>
	<table cellpadding="0" cellspacing="0" border="0" width="830" class="green830">
		<tr>
			<td class="top">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="add"><a title="Add Announcment" href="addthread.php?key=<?=$_REQUEST['key']?>"><img src="<?=$BF?>images/plus_add.png" alt="Add Announcment" /></td>
						<td class="title">Announcments</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class='List_green' id='List_green' style='width: 100%;'  cellpadding="0" cellspacing="0">
					<tr>
						<th>Thread</th>
						<th align="center">Replies</th>
						<th>Created By</th>
						<th>Created On</th>
						<th>Last Post By</th>
						<th>Last Post On</th>
					</tr>
<?
				$count=0;
				while ($row = mysqli_fetch_assoc($announcments)) { 
					$link = 'window.location.href="thread.php?key='.$row['chrKEY'].'"'; 
?>
					<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
						onmouseover='RowHighlight_green("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
						<td onclick='<?=$link?>'><?=$row['chrThread']?></td>
						<td onclick='<?=$link?>' style="width:50; white-space:nowrap;text-align:center;"><?=number_format($row['intPosts'])?></td>
						<td onclick='<?=$link?>' style="width:100;white-space:nowrap;"><?=$row['chrLast']?>, <?=$row['chrFirst']?></td>
						<td onclick='<?=$link?>' style="width:150;white-space:nowrap;"><?=date('n/j/Y g:ia',strtotime($row['dtCreated']))?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;text-align:center;"><?=($row['chrLastPostBy'] != "" ? $row['chrLastPostBy'] : "N/A")?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;text-align:center;"><?=($row['chrLastPostBy'] != "" ? date('n/j/Y g:ia',strtotime($row['dtLastPost'])) : "N/A")?></td>
					</tr>
<?
				} 
?>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="bottom"></td>
		</tr>
	</table>
				
<?
	}
?>
	<table cellpadding="0" cellspacing="0" border="0" width="830" class="blue830">
		<tr>
			<td class="top" style="width:100%;">
				<table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
					<tr>
						<td class="add"><a title="Start A New Thread" href="addthread.php?key=<?=$_REQUEST['key']?>"><img src="<?=$BF?>images/plus_add.png" alt="Start A New Thread" /></td>
						<td class="title">Threads / Discussions</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="width:100%;">
				<table class='List_blue' id='List_blue' style='width: 100%;'  cellpadding="0" cellspacing="0">
					<tr>
					<? $extra = "key=".$_REQUEST['key']; ?>
					<? sortList('Thread', 'chrThread','',$extra); ?>
					<? sortList('Replies', 'intPosts','text-align:center;',$extra); ?>
					<? sortList('Created By', 'chrLast','',$extra); ?>
					<? sortList('Created On', 'dtCreated','',$extra); ?>
					<? sortList('Last Post By', 'chrLastPostBy','',$extra); ?>
					<? sortList('Last Post On', 'dtLastPost','',$extra); ?>
					</tr>
<?
				$count=0;
				while ($row = mysqli_fetch_assoc($results)) { 
					$link = 'window.location.href="thread.php?key='.$row['chrKEY'].'"'; 
?>
					<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
						onmouseover='RowHighlight_blue("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
						<td onclick='<?=$link?>' style="width:100%;"><?=$row['chrThread']?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;text-align:center;"><?=number_format($row['intPosts'])?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;"><?=$row['chrLast']?>, <?=$row['chrFirst']?></td>
						<td onclick='<?=$link?>' style="width:80px;white-space:nowrap;"><?=date('n/j/Y g:ia',strtotime($row['dtCreated']))?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;text-align:center;"><?=($row['chrLastPostBy'] != "" ? $row['chrLastPostBy'] : "N/A")?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;text-align:center;"><?=($row['chrLastPostBy'] != "" ? date('n/j/Y g:ia',strtotime($row['dtLastPost'])) : "N/A")?></td>
					</tr>
<?
				} 

				if($count == 0) {
?>
					<tr>
						<td align="center" colspan='6' height="20">No Threads have been posted at this time.</td>
					</tr>
<?
				}
?>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="bottom"></td>
		</tr>
	</table>
</form>


<?	include($BF .'includes/bottom.php'); ?>