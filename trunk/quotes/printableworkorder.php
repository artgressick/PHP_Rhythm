<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');

	$title = 'Print Work Order';	
	include($BF .'includes/meta.php');

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 

	if(!isset($_REQUEST['key'])) {
		 errorPage('Invalid Work Order');
	}

	$q = "SELECT WorkOrders.ID, Quotes.idCustomer, WorkOrders.chrWorkOrder, WorkOrders.dtCreated, DATE_FORMAT(WorkOrders.dtCreated,'%m/%d/%Y') as dtFormated, 
			People.chrFirst,People.chrLast, Customers.ID AS idCustomer, Customers.chrCustomer,Customers.chrCPerson,Customers.chrCEmail,
			CustomerAddresses.chrCity,CustomerAddresses.chrPostalCode,CustomerAddresses.chrAddress1, 
			Locales.chrLocaleShort
		FROM WorkOrders
		JOIN People ON People.ID=WorkOrders.idPerson
		JOIN Quotes ON WorkOrders.idQuote=Quotes.ID
		JOIN Customers ON Customers.ID=Quotes.idCustomer
		JOIN CustomerAddresses ON CustomerAddresses.idCustomer=Customers.ID
		LEFT JOIN Locales ON Locales.ID=CustomerAddresses.idLocale
		WHERE WorkOrders.chrKEY='". $_REQUEST['key'] ."'";
	$info = db_query($q,"getting info",1);

	if($info['ID'] == "") { errorPage('Invalid Work Order'); }

	$q = "SELECT LineItems.ID,LineItems.intMiles,LineItems.dbQuantity,LineItems.dbUnitPrice,LineItems.txtDescription,
		  	People.chrFirst, People.chrLast,
			DATE_FORMAT(LineItems.tBegin,'%l%i') as tBeginFormated, LineItems.tBegin,DATE_FORMAT(LineItems.tEnd,'%l%i') as tEndFormated, LineItems.tEnd,
			DATE_FORMAT(LineItems.dtCreated,'%m/%d/%Y') as dtFormated, LineItems.dtCreated
		FROM LineItems
		JOIN People ON People.ID=LineItems.idPerson
		WHERE !LineItems.bDeleted AND idWorkOrder=". $info['ID'] ."
		ORDER BY dtCreated";
	$results = db_query($q,"getting LineItems");

	$q = "SELECT chrCustomerNumber,chrPhoneType 
		FROM CustomerNumbers
		JOIN PhoneTypes ON PhoneTypes.ID=CustomerNumbers.idPhoneType
		WHERE idCustomer='". $info['idCustomer']."'";
	$numbers = db_query($q,"getting numbers");
	while($row = mysqli_fetch_assoc($numbers)) { $info['chr'.$row['chrPhoneType']] = $row['chrCustomerNumber']; }
?>

<script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/overlays.js"></script>
<style type='text/css'>
.List td { font-size: 10px; border-right: 1px solid #999; }
.List .nowrap { white-space: nowrap; }
a:active,a:hover,a:visited { text-decoration: underline; color: blue; }
</style>
</head>
<body>


	<table border="0" cellspacing="0" cellpadding="0" align="center" style='width: 800px; margin: 0 auto;'>
    	<tr>
      		<td><img src="<?=$BF?>images/techitlogo.gif" width="150" height="104"></td>
            <td	style='width: 100%;'>
            	<div style='header3'>techIT Solutions</div>
                1261 White Road<br>
                Grove City, Ohio 43123<br>
                Fax (803) 649-7266
            </td>
        </tr>
    </table>
    
    <div align="center" style="width: 800px; margin: 0 auto; background: url(<?=$BF?>images/blueline.gif) right repeat-x; text-align: right;"><img src="<?=$BF?>images/workorder.gif" width="182" height="24"></div>
    
    
	<table cellspacing="0" cellpadding="0" align="center" style='width: 800px; margin: 0 auto 20px; border: 1px solid #999;'>
		<tr>
			<td style='width: 400px; vertical-align: top; font-size: 10px; border-right: 1px solid #999; padding: 5px;'>
				<div><?=$info['chrCustomer']?><br />
					<?=$info['chrCPerson']?> - <?=$info['chrCEmail']?><br />
					<?=$info['chrAddress1']?><br />
					<?=$info['chrCity']?>,&nbsp;<?=$info['chrLocaleShort']?>&nbsp;<?=$info['chrPostalCode']?><br />
					Phone:&nbsp;<?=$info['chrOffice']?>
					<? if(isset($info['chrFax']) && $info['chrFax'] != "") { ?><span style='padding-left: 30px;'>Fax: <?=$info['chrFax']?></span><?	} ?>
				</div>
			</td>
			<td style='width: 400px; vertical-align: top; font-size: 10px; padding: 5px;'>
				<div>Date:&nbsp;<?=$info['dtFormated']?>
					Order #:&nbsp;<?=$info['chrWorkOrder']?><br />
					Creator:&nbsp;<?=$info['chrFirst']?> <?=$info['chrLast']?><br />
				</div>
			</td>
		</tr>
	</table>

	<table class='List' id='List' style='width: 800px; margin: 0 auto; border-right: none;' align="center" cellpadding="0" cellspacing="0">
		<tr>
			<th>Qty</th>
			<th>Date</th>
			<th>Description</th>
			<th>Engineer</th>
			<th>Unit Price</th>
			<th>Total</th>
		</tr>
<? $count=0;
	$qty = $miles = 0;
	$dbTotalPrice = 0;
	while ($row = mysqli_fetch_assoc($results)) { 
		$qty += $row['dbQuantity'];
		$miles += $row['intMiles'];
		$dbTotalPrice += ($row['dbQuantity'] * $row['dbUnitPrice']);
?>
		<tr id='tr<?=$row['ID']?>' class='<?=($count++%2 ? 'ListLineOdd' : 'ListLineEven')?>' 
			onmouseover='RowHighlight("tr<?=$row['ID']?>");' onmouseout='UnRowHighlight("tr<?=$row['ID']?>");'>
			<td class='nowrap'><?=$row['dbQuantity']?></td>
			<td><?=$row['dtFormated']?></td>
			<td><?=$row['txtDescription']?></td>
			<td><?=$row['chrLast']?>, <?=$row['chrFirst']?></td>
			<td class='nowrap'>$<?=number_format($row['dbUnitPrice'],2)?></td>
			<td class='nowrap'>$<?=number_format(($row['dbQuantity'] * $row['dbUnitPrice']),2)?></td>
		</tr>
<?	} ?>
		<tr>
			<td style='border-top: 1px solid #999;' class='nowrap'><?=$qty?></td>
			<td style='border-top: 1px solid #999; text-align: right; height: 20px;' colspan='4'>Total</td>
			<td style='border-top: 1px solid #999;' class='nowrap'>$<?=number_format($dbTotalPrice,2)?></td>
		</tr>	
	</table>
    
    
	<div align="center" style="width: 800px; margin: 30px auto; text-align: left;">Client Signature:_________________________________</div>

    <div align="center" style="font-size: 12px; width: 800px; margin: 0 auto 10px;"><em>My signature above indicates that each item listed in the above Statement of Work has been completed to my satisfaction and in a timely manner. I am satisfied with the overall appearance and function of the equipment that has been installed, upgraded or serviced - all the expected deliverables have been received - the engineer(s) completing the service conducted themselves in a courteous and professional manner and possessed the technical expertise required to efficiently complete the required tasks.  Any concerns or discrepancies regarding this work have been clearly expressed to the engineer(s) and a contingency plan has been outlined to address these items.</em></div>

        <div align="center" style="width: 800px; margin: 0 auto; background: url(<?=$BF?>images/blueline.gif) right repeat-x; text-align: right;">&nbsp;</div>

<div align="center" style="width: 800px; margin: 10px auto 0;">"Technically Speaking....We've Got You Covered"</div>

</body>
</html>
