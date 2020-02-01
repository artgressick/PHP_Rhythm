<?php
	$BF = "../";
	$title = "Popup - People";
	require($BF. '_lib.php');
	include($BF. 'includes/meta.php');

	// This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrLast, chrFirst"; }
	
	$q = "SELECT ID, chrKEY, chrFirst, chrLast, chrEmail
			FROM People 
			WHERE !bDeleted ";
	if($_REQUEST['chrSearch'] != "") {
		$searchstr = str_replace(" ","%",$_REQUEST['chrSearch']);
		$q2 = " chrLast LIKE '%".encode($searchstr)."%' OR chrFirst LIKE '%".encode($searchstr)."%' OR chrEmail LIKE '%".encode($searchstr)."%' ";
		if(preg_match('/ /',$_REQUEST['chrSearch'],$matches)) {
			$search = explode(" ", $_REQUEST['chrSearch']);
			foreach ($search as $k) {
				$q2 .= " chrLast LIKE '%".encode($k)."%' OR chrFirst LIKE '%".encode($k)."%' OR chrEmail LIKE '%".encode($k)."%' ";
			}
		}
		$q2 = substr($q2,0,-3);
		$q .= " AND ( ".$q2." ) ";
	} 
	$q .= " ORDER BY " . $_REQUEST['sortCol'] . " " . $_REQUEST['ordCol'];
	$result = db_query($q,"Getting all people");

?>
<script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/popup.js"></script>
<script type="text/javascript">
function associate(id,fname,lname,email,key) {

	noRowClear("<?=$_REQUEST['tbl']?>");
	var tbl = window.opener.document.getElementById("<?=$_REQUEST['tbl']?>").innerHTML;

	var post = 0;
	if(!window.opener.document.getElementById("<?=$_REQUEST['tbl']?>" +"tr"+id) ) {
		window.opener.document.getElementById("<?=$_REQUEST['tbl']?>").innerHTML = tbl + "<tr id='<?=$_REQUEST['tbl']?>tr"+ id +"'onmouseover='RowHighlight(\"<?=$_REQUEST['tbl']?>tr"+ id +"\");' onmouseout='UnRowHighlight(\"<?=$_REQUEST['tbl']?>tr"+ id +"\");'> " +
		"<td onclick='location.href=\"../people/details.php?id="+ id +"\"' style='cursor: pointer;'>"+ fname +"</td> " +
		"<td onclick='location.href=\"../people/details.php?id="+ id +"\"' style='cursor: pointer;'>"+ lname +"</td> " +
		"<td onclick='location.href=\"../people/details.php?id="+ id +"\"' style='cursor: pointer;'>"+ email +"</td> " +
		
		"<td class='options'>"+ deleteButtonPopups(id,fname+" "+lname,key) +"</td>";	
		
		post = 1;
	} else {
		if(window.opener.document.getElementById("<?=$_REQUEST['tbl']?>" +"tr"+id).style.display == "none") {
			window.opener.document.getElementById("<?=$_REQUEST['tbl']?>" +"tr"+id).style.display = "";
			post = 1;
		}
	}

	if(post == 1) {
		repaintmini("<?=$_REQUEST['tbl']?>");

		var poststr = "idCustomer=<?=$_REQUEST['idCustomer']?>" +
			"&idPerson=" + id + 
        	"&postType=" + encodeURI( "insert" );
      	postInfo('ajax_contacts.php', poststr);
      	
	}
}


</script>
<?
	include($BF. 'includes/top_popup.php');
?>

	<form action="" method="post">
	
	<table class='title' cellpadding="0" cellspacing="0" style='width: 100%;'>
		<tr>
			<td style = "color: white;">Add People Popup</td>
			<td style='text-align: right;'><input type='text' id='chrSearch' name='chrSearch' /> <input type='submit' name='search' value='Search' /></td>
		</tr>
	</table>
	<div class='instructions'>Click on the person to add it to the page.</div>
	
	<div class='innerbody'>	

	<table id='List' class='List' style='width: 98%;' cellpadding="0" cellspacing="0">
		<tr>
			<?  sortList('First Name', 'chrFirst');
			    sortList('Last Name', 'chrLast');
				sortList('Email Address', 'chrEmail');
		 	?>
		</tr>
<?  $count=0;	
	while ($row = mysqli_fetch_assoc($result)) { ?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2?'ListLineOdd':'ListLineEven')?>' 
			onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				<td style='cursor: pointer;' onclick="associate(<?=$row['ID']?>,'<?=$row['chrFirst']?>','<?=$row['chrLast']?>','<?=$row['chrEmail']?>','<?=$row['chrKEY']?>')"><?=$row['chrFirst']?></td>
				<td style='cursor: pointer;' onclick="associate(<?=$row['ID']?>,'<?=$row['chrFirst']?>','<?=$row['chrLast']?>','<?=$row['chrEmail']?>','<?=$row['chrKEY']?>')"><?=$row['chrLast']?></td>
				<td style='cursor: pointer;' onclick="associate(<?=$row['ID']?>,'<?=$row['chrFirst']?>','<?=$row['chrLast']?>','<?=$row['chrEmail']?>','<?=$row['chrKEY']?>')"><?=$row['chrEmail']?></td>
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