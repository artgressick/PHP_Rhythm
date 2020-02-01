<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.

	// Title of page for top of browser
	$title = 'Customer Users';	
	
	// Left Link and Top Nav Variables
	$section = 'admin';
	$leftlink = "customeraccess";	

	// For Overlay.php include
	$TableName = "CustomerAccess"; #  This is the Database table that you will be setting the bDeleted statuses on.
	$postType = "permDelete"; # This is ONLY to be here if you want to PERMANENTLY delete a record (no bDelete)

	// Banner Information
	$banner_title = "People to Customer Access"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-person.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "This is a list of people who have access to customers."; // Instructions or description. (NOT REQUIRED)

	require($BF .'_lib.php');
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrLast"; } # This sets the default column order.  Asc by default.


	if (!isset($_SESSION['char_custaccess']) && !isset($_REQUEST['chrChr'])) {  // Default
		$_SESSION['char_custaccess'] = "A";
		$_REQUEST['chrChr'] = "A";
	} else if (isset($_REQUEST['chrChr']) && $_REQUEST['chrChr'] != "") {
		$_SESSION['char_custaccess'] = $_REQUEST['chrChr'];
	}
	
	if(!isset($_REQUEST['chrSearch'])) { $_REQUEST['chrSearch'] = ""; }

	if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == "Search ALL") {
		$_SESSION['char_custaccess'] = "";
		$_REQUEST['chrChr'] = "";
	}

	$q = "SELECT CustomerAccess.ID, chrFirst, chrLast, chrEmail, chrCustomer, People.chrKEY
		FROM CustomerAccess
		JOIN People on CustomerAccess.idPerson = People.ID
		JOIN Customers ON Customers.ID=CustomerAccess.idCustomer
		WHERE !People.bDeleted ";

	if($_REQUEST['chrSearch'] != "") {
		$searchstr = str_replace(" ","%",$_REQUEST['chrSearch']);
		$q2 = " chrLast LIKE '%".encode($searchstr)."%' OR chrFirst LIKE '%".encode($searchstr)."%' OR chrEmail LIKE '%".encode($searchstr)."%' OR ";
		if(preg_match('/ /',$_REQUEST['chrSearch'],$matches)) {
			$search = explode(" ", $_REQUEST['chrSearch']);
			foreach ($search as $k) {
				$q2 .= " chrLast LIKE '%".encode($k)."%' OR chrFirst LIKE '%".encode($k)."%' OR chrEmail LIKE '%".encode($k)."%' OR ";
			}
		}
		$q2 = substr($q2,0,-3);
		$q .= " AND ( ".$q2." ) ";
	} 
	
	if($_SESSION['char_custaccess'] != "") {
		$q .= " AND chrLast LIKE '".$_SESSION['char_custaccess']."%' ";
	}
		
	$q .= " ORDER BY ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];

	$results = db_query($q,"getting people");

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	


	include($BF .'includes/top.php');
	$banner_xtra = "(".mysqli_num_rows($results) ." results)"; // Extra information for Page. (NOT REQUIRED)
	include($BF .'includes/left_admin.php');

	# This is the include file for the overlay to show that the delete is working.
	include($BF. 'includes/overlay.php');
?>

<? 	if(isset($_SESSION['InfoMessage'])) { ?> 
		<div class='InfoMessage'><?=$_SESSION['InfoMessage']?></div> 
<? 	unset($_SESSION['InfoMessage']); } ?>		

	<form id="search" method="get" action="">
	<table cellpadding="0" cellspacing="0" border="0" class="optionsTop">
<?
	$form=1;
	optionsBar();
?> 
	</table>
	<table class='List' id='List' style='width: 100%;'  cellpadding="0" cellspacing="0">
		<tr>
			<? sortList('Last Name', 'chrLast'); ?>
			<? sortList('First Name', 'chrFirst'); ?>
			<? sortList('Email Address', 'chrEmail'); ?>
			<? sortList('Customer', 'chrCustomer'); ?>
			<th class='options'>Edit</th>
			<th class='options'><img src="<?=$BF?>images/options.gif"></th>
		</tr>
<? $count=0;	
	while ($row = mysqli_fetch_assoc($results)) { 
		$link = 'window.location.href="editcustomeraccess.php?id='.$row['ID'].'"'; 
?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
				onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				<td onclick='<?=$link?>'><?=$row['chrLast']?></td>
				<td onclick='<?=$link?>'><?=$row['chrFirst']?></td>
				<td onclick='<?=$link?>'><?=$row['chrEmail']?></td>
				<td onclick='<?=$link?>'><?=$row['chrCustomer']?></td>
				<td class='options'><a title="Edit <?=$row['chrFirst']?> <?=$row['chrLast']?>" href='editcustomeraccess.php?id=<?=$row['ID']?>'><img src='<?=$BF?>images/edit.png' alt='edit' /></a></td>
				<td class='options'><?=deleteButton($row['ID'],$row['chrFirst'] . ' ' .$row['chrLast'], $row['chrKEY'])?></td>
			</tr>
<?	} 
if($count == 0) { ?>
			<tr>
				<td align="center" colspan='5' height="20">No People having access to customers to show.</td>
			</tr>
<?	} ?>
	</table>
	<table cellpadding="0" cellspacing="0" border="0" class="optionsBottom">
<?
	$form=2;
	optionsBar();
?> 
	</table>
	</form>


<?	include($BF .'includes/bottom.php');


function optionsBar() {
global $BF;
global $form;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="addbutton"><a title="Add Person to Customer" href="addcustomeraccess.php"><img src="<?=$BF?>images/plus_add.gif" alt="Add Person to Customer" /></a></td>
			<td class="left">
				<table cellpadding="0" border="0" class='Tabs'>
					<tr>
						<td class='<?=("%"==$_SESSION['char_custaccess']?"Current":"")?>'><a href='customeraccess.php?chrChr=%&sortCol=<?=$_REQUEST['sortCol']?>&ordCol=<?=$_REQUEST['ordCol']?>' style="padding: 0 5px;">ALL</a></td>
<? 
					$char = 65;
					$end = 90;
					while ($char <= $end ) {
						$chrChr = chr($char++);
?>
						<td class='<?=($chrChr==$_SESSION['char_custaccess']?"Current":"")?>'><a href='customeraccess.php?chrChr=<?=$chrChr?>&sortCol=<?=$_REQUEST['sortCol']?>&ordCol=<?=$_REQUEST['ordCol']?>' style="padding: 0 5px;"><?=$chrChr?></a></td>
<?
					}
?>
					</tr>
				</table>
			</td>
			<td class="right">
				<input type="text" id="chrSearch<?=$form?>" name="chrSearch" placeholder="Search Users" value='<?=$_REQUEST['chrSearch']?>' size="15" onchange="javascript:document.getElementById('chrSearch<?=($form==1?"2":"1")?>').value = document.getElementById('chrSearch<?=($form==1?"1":"2")?>').value" />
				<input type='submit' name='submit' value='Search ALL' /> 
				<input type='submit' name='submit'<?=($_SESSION['char_custaccess'] == "" || $_SESSION['char_custaccess'] == "%" ? ' style="display:none;"' : "")?> value='Search Within "<?=$_SESSION['char_custaccess']?>"' /></td>
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}
?>