<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	if(!isset($_REQUEST['key'])) {
		errorPage('Invalid Discussion Group');
	}
	# Getting Group
	$info = db_query("SELECT G.ID, G.chrKEY, chrGroup FROM Groups G WHERE !G.bDeleted AND G.bShow AND G.chrKEY='". $_REQUEST['key'] ."'","getting Discussion Group info",1);
	if($info['ID'] == "") { errorPage('Invalid Discussion Group'); }


	$title = $info['chrGroup'];	
	include($BF .'includes/meta.php');

	$section = 'discussions';
	$leftlink = "Group".$info['ID'];
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Discussion Group: ".$info['chrGroup']; // Title of this page. (REQUIRED)
	$banner_icon = "icons-discussions.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "Please choose from the list of topics below to see all of the threads."; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_discussion.php');

?>
<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>	

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
						<td style="padding:0 10px;">
<?
				$q = "SELECT Th.chrKEY, Th.dtCreated, chrThread, chrTopic, chrFirst, chrLast, P.chrKEY AS chrPKEY, T.chrKEY AS chrTKEY
						FROM Threads Th
						JOIN People P ON Th.idPersonCreated=P.ID
						JOIN Topics T ON Th.idTopic=T.ID
						WHERE !T.bDeleted AND T.bShow AND !Th.bDeleted AND T.idGroup=".$info['ID']." 
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
									<span class='smallfont'><strong>By</strong> <a href="<?=$BF?>crm/viewcontact.php?key=<?=$row['chrPKEY']?>"><?=$row['chrFirst']?> <?=$row['chrLast']?></a> <strong>in</strong> <a href="topic.php?key=<?=$row['chrTKEY']?>"><?=(strlen($row['chrTopic']) > 20 ? substr($row['chrTopic'],0,20).".." : $row['chrTopic'] )?></a>
									</span>
					
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
						<td style="padding:0 10px;">
<?
				$q = "SELECT Th.chrKEY, Th.dtCreated, chrThread, chrTopic, chrFirst, chrLast, P.chrKEY AS chrPKEY, T.chrKEY AS chrTKEY,
							(SELECT COUNT(ID) FROM Posts Po WHERE Po.idThread=Th.ID) as intCount,
							(SELECT CONCAT(Po.dtCreated,'|',PP.chrKey,'|', PP.chrFirst,'|', PP.chrLast) FROM Posts Po JOIN People PP ON Po.idPersonCreated=PP.ID WHERE Po.idThread=Th.ID ORDER BY Po.ID DESC LIMIT 1) as chrPostData 
						FROM Threads Th
						JOIN People P ON Th.idPersonCreated=P.ID
						JOIN Topics T ON Th.idTopic=T.ID
						JOIN Posts ON Posts.idThread=Th.ID
						WHERE !T.bDeleted AND T.bShow AND !Th.bDeleted AND Posts.bShow AND T.idGroup=".$info['ID']."
						GROUP BY Th.ID
						ORDER BY intCount DESC, chrPostData DESC
						LIMIT 5
					";
				$top = db_query($q,"Getting Top 5 active Threads");
				
				if(mysqli_num_rows($top) > 0) {
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
									<span class='smallfont'><strong>Last Post By</strong> <a href="<?=$BF?>crm/viewcontact.php?key=<?=$rowdata[1]?>"><?=$rowdata[2]?> <?=$rowdata[3]?></a> <strong>on</strong> <?=date('n/j/Y g:ia',strtotime($rowdata[0]))?> <strong>Total Replies:</strong> <?=number_format($row[intCount])?></a>
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
<?
	$q = "SELECT T.ID, T.chrKEY, chrTopic, T.bShow, T.intOrder,
			(SELECT COUNT(ID) FROM Threads WHERE !bDeleted AND Threads.idTopic=T.ID) as intThreads
		FROM Topics T 
		WHERE !T.bDeleted AND T.bShow AND T.idGroup=".$info['ID']."
		ORDER BY T.intOrder,T.chrTopic
		";

	$results = db_query($q,"Getting Topics");

	if(mysqli_num_rows($results) > 0) {
?>
		<tr>
			<td valign="top" style="width:410px;">
				<table cellpadding="0" cellspacing="0" class="dis_blue">
					<tr>
						<td class="blue_top"><!-- Spacer --></td>
					</tr>
					<tr>
						<td style="padding:0px 10px;">
							<ul>
<?
		$half = round(mysqli_num_rows($results)/2);
		$count=0;
		$secondcol=0;
		while ($row = mysqli_fetch_assoc($results)) {

			if ($count++ >= $half && $count > 1 && $secondcol == 0) {
				$secondcol=1;
?>
							</ul>
						</td>
					</tr>
					<tr>
						<td class="blue_bottom"><!-- Spacer --></td>
					</tr>
				</table>	
			</td>
			<td style="width:10px;"><!-- Spacer --></td>
			<td valign="top" style="width:410px;">
				<table cellpadding="0" cellspacing="0" class="dis_blue">
					<tr>
						<td class="blue_top"><!-- Spacer --></td>
					</tr>
					<tr>
						<td style="padding:0px 10px;">
							<ul>
<?			
			} 
?>
								<li><a href="topic.php?key=<?=$row['chrKEY']?>"><?=$row['chrTopic']?></a><?=($row['intThreads'] > 0 ? " <span class='smallfont'>(".$row['intThreads']." threads)</span>" : "")?></li>
<?
		}
?>
							</ul>
						</td>
					</tr>
					<tr>
						<td class="blue_bottom"><!-- Spacer --></td>
					</tr>
				</table>		
<?
		if($secondcol == 0) {
?>
			</td>
			<td style="width:10px;"><!-- Spacer --></td>
			<td valign="top" style="width:410px;">&nbsp;
<?
		}
?>
			</td>
		</tr>	
<?
	}
?>
	</table>


<?	include($BF .'includes/bottom.php'); ?>