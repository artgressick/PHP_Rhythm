<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');
	
	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrLast,chrFirst"; }

	$title = 'Add Contact Popup';
	include($BF .'includes/meta.php');

?>
<script type="text/javascript">
function associate(id,fname,lname) {

	window.opener.document.getElementById("chrContact").innerHTML = fname +" "+ lname;
	window.opener.document.getElementById("idContact").value = id;

	window.close();
}
</script>
<?
	include($BF. 'includes/top_popup.php');
?>

	<form action="" method="post">
	
	<table class='title' cellpadding="0" cellspacing="0" style='width: 100%;'>
		<tr>
			<td style = "color: white;">Add People Popup</td>
			<td style='text-align: right;'><input type='text' id='chrSearch' name='chrSearch' value='<?=(isset($_POST['chrSearch']) ? $_POST['chrSearch'] : '')?>' /> <input type='submit' name='search' value='Search' /></td>
		</tr>
	</table>
	<div class='instructions'>Click on the person to add it to the page.</div>
	
	<div class='innerbody'>	

	<table id='List' class='List' style='width: 98%;' cellpadding="0" cellspacing="0">
		<tr>
			<?  sortList('First Name', 'chrFirst');
			    sortList('Last Name', 'chrLast');
		 	?>
		</tr>
<?  $results = db_query("SELECT ID,chrFirst,chrLast 
		FROM People 
		WHERE !bDeleted 
		". (isset($_POST['chrSearch']) ? " AND (chrLast LIKE '%". $_POST['chrSearch'] ."%' OR chrFirst LIKE '%". $_POST['chrSearch'] ."%' OR chrEmail LIKE '%". $_POST['chrSearch'] ."%') " : '') ."
		ORDER BY chrLast,chrFirst","getting people");
	
	$count=0;	
	while ($row = mysqli_fetch_assoc($results)) { ?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2?'ListLineOdd':'ListLineEven')?>' 
			onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				<td style='cursor: pointer;' onclick="associate(<?=$row['ID']?>,'<?=$row['chrFirst']?>','<?=$row['chrLast']?>')"><?=$row['chrFirst']?></td>
				<td style='cursor: pointer;' onclick="associate(<?=$row['ID']?>,'<?=$row['chrFirst']?>','<?=$row['chrLast']?>')"><?=$row['chrLast']?></td>
			</tr>
<?	} 
if($count == 0) { ?>
			<tr>
				<td align="center" colspan="5">No People to display</td>
			</tr>
<?	} ?>
		</table>
	
	
	</div>
	<div align='center'>
		<a href='javascript:window.close();' class='link'>Close this Window</a>
	</div>
</form>

</body>
</html>