<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	$title = 'Customers';	
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "Customers.chrCustomer"; } # This sets the default column order.  Asc by default.

	if (!isset($_SESSION['char_customers']) && !isset($_REQUEST['chrChr'])) {  // Default
		$_SESSION['char_customers'] = "A";
		$_REQUEST['chrChr'] = "A";
	} else if (isset($_REQUEST['chrChr']) && $_REQUEST['chrChr'] != "") {
		$_SESSION['char_customers'] = $_REQUEST['chrChr'];
	}
	
	if(!isset($_REQUEST['chrSearch'])) { $_REQUEST['chrSearch'] = ""; }

	if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == "Search ALL") {
		$_SESSION['char_customers'] = "";
		$_REQUEST['chrChr'] = "";
	}

	$q = "SELECT Customers.ID, Customers.chrKEY, Customers.chrCustomer, chrCity, chrCountryShort, chrLocaleShort
			FROM Customers 
			JOIN CustomerAddresses ON CustomerAddresses.idCustomer=Customers.ID
			JOIN Locales ON Locales.ID=CustomerAddresses.idLocale
			JOIN Countries ON Countries.ID=CustomerAddresses.idCountry
			WHERE !Customers.bDeleted AND CustomerAddresses.bPrimary ";
		
	if($_REQUEST['chrSearch'] != "") {
		$searchstr = str_replace(" ","%",$_REQUEST['chrSearch']);
		$q2 = " chrCustomer LIKE '%".encode($searchstr)."%' OR ";
		if(preg_match('/ /',$_REQUEST['chrSearch'],$matches)) {
			$search = explode(" ", $_REQUEST['chrSearch']);
			foreach ($search as $k) {
				$q2 .= " chrCustomer LIKE '%".encode($k)."%' OR ";
			}
		}
		$q2 = substr($q2,0,-3);
		$q .= " AND ( ".$q2." ) ";
	} 
		
	if($_SESSION['char_customers'] != "") {
		$q .= " AND chrCustomer LIKE '".$_SESSION['char_customers']."%' ";
	}
		
	$q .= " ORDER BY ". $_REQUEST['sortCol'] ." ". $_REQUEST['ordCol'];

	$results = db_query($q,"getting customers");

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script><?	

	$section = 'crm';
	$leftlink = "viewcustomer";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Customers"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-company.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = "(".mysqli_num_rows($results)." results)"; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = "Choose a client from the list below.  Click on the column header to sort the list by that column."; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_crm.php');

	# This is the include file for the overlay to show that the delete is working.
	$TableName = "Customers"; #  This is the Database table that you will be setting the bDeleted statuses on.
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
			<?  sortList('Customer Name', 'chrCustomer'); 
				sortList('Country', 'chrCountryShort'); 
				sortList('Locale', 'chrLocaleShort'); 
			?>
			<th class='options'>Edit</th>
			<th class='options'><img src="<?=$BF?>images/options.gif"></th>
		</tr>
<? $count=0;	
	while ($row = mysqli_fetch_assoc($results)) { 
		$link = 'window.location.href="viewcustomer.php?key='.$row['chrKEY'].'"'; 
?>
			<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
				onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
				<td onclick='<?=$link?>'><?=$row['chrCustomer']?></td>
				<td onclick='<?=$link?>'><?=$row['chrCountryShort']?></td>
				<td onclick='<?=$link?>'><?=$row['chrLocaleShort']?></td>
				<td class='options'><a title="Edit <?=$row['chrCustomer']?>" href='editcustomer.php?key=<?=$row['chrKEY']?>'><img src='<?=$BF?>images/edit.png' alt='edit' /></a></td>
				<td class='options'><?=deleteButton($row['ID'],$row['chrCustomer'],$row['chrKEY'])?></td>
			</tr>
<?	} 
if($count == 0) { ?>
			<tr>
				<td align="center" colspan='3' height="20">No Customers to Display</td>
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
<?
	include($BF .'includes/bottom.php'); 

function optionsBar() {
global $BF;
global $form;
?>
		<tr>
			<td class="leftCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
			<td class="addbutton"><a title="Add Customer" href="addcustomer.php"><img src="<?=$BF?>images/plus_add.gif" alt="Add Customer" /></a></td>
			<td class="left">
				<table cellpadding="0" border="0" class='Tabs'>
					<tr>
						<td class='<?=("%"==$_SESSION['char_customers']?"Current":"")?>'><a href='index.php?chrChr=%&sortCol=<?=$_REQUEST['sortCol']?>&ordCol=<?=$_REQUEST['ordCol']?>' style="padding: 0 5px;">ALL</a></td>
<? 
					$char = 65;
					$end = 90;
					while ($char <= $end ) {
						$chrChr = chr($char++);
?>
						<td class='<?=($chrChr==$_SESSION['char_customers']?"Current":"")?>'><a href='index.php?chrChr=<?=$chrChr?>&sortCol=<?=$_REQUEST['sortCol']?>&ordCol=<?=$_REQUEST['ordCol']?>' style="padding: 0 5px;"><?=$chrChr?></a></td>
<?
					}
?>
					</tr>
				</table>
			</td>
			<td class="right">
				<input type="text" id="chrSearch<?=$form?>" name="chrSearch" placeholder="Search Users" value='<?=$_REQUEST['chrSearch']?>' size="15" onchange="javascript:document.getElementById('chrSearch<?=($form==1?"2":"1")?>').value = document.getElementById('chrSearch<?=($form==1?"1":"2")?>').value" />
				<input type='submit' name='submit' value='Search ALL' /> 
				<input type='submit' name='submit'<?=($_SESSION['char_customers'] == "" || $_SESSION['char_customers'] == "%" ? ' style="display:none;"' : "")?> value='Search Within "<?=$_SESSION['char_customers']?>"' /></td>
			<td class="rightCap"><img src="<?=$BF?>images/title_fade_round_spacer.png" alt="image" /></td>
		</tr>
<?
}
?>