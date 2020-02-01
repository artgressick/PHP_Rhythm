<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	if(isset($_POST['chrSearch'])) {

		# This is for the sorting of the rows and columns.  We must set the default order and name
		include($BF. 'components/list/sortList.php');
		if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = " dtCreated DESC, chrThread"; } # This sets the default column order.  Asc by default.
		
		$add_join = "";
		$add_where = "";
		
		if($_POST['SearchIn'] == 1) {
			$add_where .=" 
				AND LCASE(chrThread) LIKE '%".strtolower(encode($_POST['chrSearch']))."%' 
			 ";
		} else if ($_POST['SearchIn'] == 2) {
			$add_where .=" 
				AND (LCASE(chrThread) LIKE '%".strtolower(encode($_POST['chrSearch']))."%' OR
					 LCASE(ps.txtPost) LIKE '%".strtolower(encode($_POST['chrSearch']))."%')
			 ";
			$add_join .= " 
				LEFT JOIN Posts AS ps ON ps.idThread=th.ID
			 ";
		} else if ($_POST['SearchIn'] == 3) {
			$tmp = explode(" ",encode($_POST['chrSearch']));
			
			$qt = "";
			foreach ($tmp as $name) {
				if($qt != "") { $qt .= " OR "; }			
				$qt .= "LCASE(chrFirst) like '%".strtolower($name)."%' OR LCASE(chrLast) like '%".strtolower($name)."%'"; 
			}
			
			$ids = db_query("SELECT GROUP_CONCAT(ID) AS IDs FROM People WHERE !bDeleted AND (".$qt.")","Getting People IDs",1);
						
			$add_where  .=" 
				AND (th.idPersonCreated IN (".$ids['IDs'].") OR ps.idPersonCreated IN (".$ids['IDs'].")) 
				GROUP BY th.ID 
			 ";
			$add_join .= " 
				LEFT JOIN Posts AS ps ON ps.idThread=th.ID
			 ";
		}
		
		
		$q="SELECT th.ID, th.chrKEY, th.chrThread, th.dtCreated, pp.chrFirst, pp.chrLast
				,(SELECT COUNT(ID) FROM Posts WHERE Posts.idThread=th.ID AND !Posts.bDeleted) AS intPosts
				,(SELECT dtCreated FROM Posts WHERE Posts.idThread=th.ID AND !Posts.bDeleted ORDER BY Posts.dtCreated DESC LIMIT 1) as dtLastPost
				,(SELECT CONCAT(chrLast,', ', chrFirst) 
					FROM Posts
					JOIN People ON Posts.idPersonCreated=People.ID 
					WHERE Posts.idThread=th.ID ORDER BY Posts.dtCreated DESC LIMIT 1) as chrLastPostBy
			FROM Groups AS gp
			JOIN Topics AS tp ON tp.idGroup=gp.ID
			JOIN Threads AS th ON th.idTopic=tp.ID 
			JOIN People pp ON th.idPersonCreated=pp.ID
			". $add_join ." 
			WHERE !gp.bDeleted AND !tp.bDeleted AND gp.bShow AND tp.bShow  
			". $add_where ." 
			ORDER by ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];
		
		$results = db_query($q,"Getting Search Results");
	}
	
	
	$title = "Search Discussion Groups";	
	include($BF .'includes/meta.php');

	if(!isset($results)) {	
?>
	<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
	<script language="javascript" type='text/javascript'>
		var totalErrors = 0;
		function error_check() {
			if(totalErrors != 0) { reset_errors(); }  
			
			totalErrors = 0;
	
			if(errEmpty('chrSearch', "You must enter something to search for.")) { totalErrors++; }
	
			if(document.getElementById('SearchIn1').checked == false && document.getElementById('SearchIn2').checked == false && document.getElementById('SearchIn3').checked == false) {
				errCustom('',"You must choose what to search in.");
				totalErrors++;
			}
	
	
		
			return (totalErrors == 0 ? true : false);
		}
	</script>
<?	
	}	
	
	$section = 'discussions';
	$leftlink = "Search";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Search Discussion Groups"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-discussions.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	if(isset($results)) {
		$total_results = mysqli_num_rows($results);
	}
	$banner_xtra = (isset($total_results)?"( '<strong>".$_POST['chrSearch']."</strong>' returned ".$total_results." record".($total_results==0 || $total_results>1?"s":"")." )":""); // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = (!isset($results)?'Complete the form and click "Submit Search"':""); // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_discussion.php');

	if(isset($_SESSION['InfoMessage'])) { 
?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<?
	unset($_SESSION['InfoMessage']); } 

	if(isset($results)) { // Show Results
?>	
	<table cellpadding="0" cellspacing="0" border="0" width="830" class="green830">
		<tr>
			<td class="top">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="title" width="100%">Search Results</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
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
				while ($row = mysqli_fetch_assoc($results)) { 
					$link = 'window.location.href="thread.php?key='.$row['chrKEY'].'"'; 
?>
					<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
						onmouseover='RowHighlight_green("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
						<td onclick='<?=$link?>' style="width:100%;"><?=$row['chrThread']?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;text-align:center;"><?=number_format($row['intPosts'])?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;"><?=$row['chrLast']?>, <?=$row['chrFirst']?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;"><?=date('n/j/Y g:ia',strtotime($row['dtCreated']))?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;text-align:center;"><?=($row['chrLastPostBy'] != "" ? $row['chrLastPostBy'] : "N/A")?></td>
						<td onclick='<?=$link?>' style="white-space:nowrap;text-align:center;"><?=($row['chrLastPostBy'] != "" ? date('n/j/Y g:ia',strtotime($row['dtLastPost'])) : "N/A")?></td>
					</tr>
<?
				} 

				if($count==0) {
?>
					<tr>
						<td colspan="6" style="text-align:center; height:20px;">No Results Found</td>
					</tr>
<?				
				}
?>
				</table>
			</td>
		</tr>
		<tr>
			<td class="bottom"></td>
		</tr>
	</table>	
<?	
	} else { // Show Start Search Page
	$post = 0;
?>
	<form action="" method="post" id="idForm" onsubmit="return error_check()">	
	<table cellpadding="0" cellspacing="0" border="0" width="830" class="green830">
		<tr>
			<td class="top">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="title">Search Form</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding:10px;">
			
				<div id="errors"></div>
			
				<div class='FormName'>Search For: <span class='FormRequired'>(Required)</span></div>
				<div class='FormField'><input type="text" name="chrSearch" id="chrSearch" size="50" value="<?=$post['chrSearch']?>"/></div>

<?  // Might add Later
//				<div class='FormName'>Search Type: <span class='FormRequired'>(Required)</span></div>
//				<div class='FormField'><input type="radio" name="SearchType" id="SearchType1" value="All" checked="checked" /> Entire String &nbsp;&nbsp;&nbsp; <input type="radio" name="SearchType" id="SearchType2" value="Any" /> Any Words</div>
?>

				<div class='FormName'>Search In: <span class='FormRequired'>(Required)</span></div>
				<div class='FormField'><input type="radio" name="SearchIn" id="SearchIn1" value="1"<?=($post['SearchIn'] == 1 || $post['SearchIn']=="" ? ' checked="checekd"' : "")?> /> Title &nbsp;&nbsp; 
										<input type="radio" name="SearchIn" id="SearchIn2" value="2"<?=($post['SearchIn'] == 2 ? ' checked="checekd"' : "")?> /> Titles AND Replies &nbsp;&nbsp;
										<input type="radio" name="SearchIn" id="SearchIn3" value="3"<?=($post['SearchIn'] == 3 ? ' checked="checekd"' : "")?> /> Poster Name &nbsp;&nbsp;
				</div>

				<div style="padding-top:10px;"><input type="submit" name="submit" value="Submit Search">
			</td>
		</tr>
		<tr>
			<td class="bottom"></td>
		</tr>
	</table>
	</form>







<?
	}

include($BF .'includes/bottom.php'); ?>