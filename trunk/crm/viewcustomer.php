<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	$title = 'View Customer';
	require($BF. '_lib.php');


	$info = db_query("SELECT Customers.chrKEY,Customers.ID,Customers.chrCustomer,chrPhoneType,chrAddressType, CustomerAddresses.chrCity,CustomerAddresses.chrPostalCode,
			CustomerAddresses.chrAddress1,CustomerAddresses.chrAddress2,CustomerAddresses.chrAddress3,CustomerNumbers.chrCustomerNumber,Customers.chrCEmail,
			Locales.chrLocale,Countries.chrCountry
		FROM Customers 
		JOIN CustomerAddresses ON CustomerAddresses.idCustomer=Customers.ID
		JOIN AddressTypes ON AddressTypes.ID=CustomerAddresses.idAddressType
		JOIN CustomerNumbers ON CustomerNumbers.idCustomer=Customers.ID
		JOIN PhoneTypes ON PhoneTypes.ID=CustomerNumbers.idPhoneType
		JOIN Countries ON Countries.ID=CustomerAddresses.idCountry
		JOIN Locales ON Locales.ID=CustomerAddresses.idLocale
		WHERE !Customers.bDeleted AND CustomerAddresses.bPrimary AND CustomerNumbers.bPrimary AND Customers.chrKEY='". $_REQUEST['key'] ."'
	","getting customer info",1);
	
	
	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrLast,chrFirst"; } # This sets the default column order.  Asc by default.
	
	
	include($BF .'includes/meta.php');

?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/popup.js"></script><?	

	$section = 'crm';
	$leftlink = "viewcustomer";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "View Customer"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-company.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $info['chrCustomer']; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please add and edit contacts from this page.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_crm.php');
?>
	<form action="" method="post" id="idForm" onsubmit="return error_check()">
	

	<table width="830" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" width="270">
				<div class='bluebox'>
					<table cellspacing="0" cellpadding="0" class='bluetop'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-contacts.gif' alt='customer' /></td>
							<td class='colorBoxTitle'>Customer Information</td>
						</tr>
					</table>

					<div class='colorBoxPadding'>
						<div class='FormName'>Customer Name</div>
						<div class='FormDisplay'><?=$info["chrCustomer"]?></div>
					</div>
					<div class='colorBoxPadding'>
						<div class='FormName'>Customer E-mail</div>
						<div class='FormDisplay'><a href='mailto:<?=$info["chrCEmail"]?>'><?=$info["chrCEmail"]?></a></div>
					</div>
	
					<div><img src="<?=$BF?>images/cap_bottom-410-blue.gif" /></div>
				</div>

				<div class='bluebox'>
					<table cellspacing="0" cellpadding="0" class='bluetop'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-calls.gif' alt='telephone' /></td>
							<td class='colorBoxTitle'>Customer Telephone Numbers</td>
						</tr>
					</table>
					

					<div class='colorBoxPadding'>
						<table cellspacing="0" cellpadding="0" style='width: 100%;'>
							<tr>
								<td colspan='2' class='FormName'>Primary Number</td>
								<td class='FormName'>Number Type</td>
							</tr>
							<tr>
								<td></td>
								<td><?=$info["chrCustomerNumber"]?></td>
								<td><?=$info["chrPhoneType"]?></td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT chrCustomerNumber,chrPhoneType 
							FROM CustomerNumbers 
							LEFT JOIN PhoneTypes ON PhoneTypes.ID=CustomerNumbers.idPhoneType
							WHERE !CustomerNumbers.bDeleted AND idCustomer='". $info['ID'] ."' AND !bPrimary
						","getting Customer Numbers"); ?>
					<div class='colorBoxPadding' id='moreNumbers' style='display: <?=(mysqli_num_rows($results) ? '' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 100%;' id='phonelist'>
							<tr>
								<td colspan='3' class='FormName'>Additional Number(s)</td>
							</tr>
<?	$i = 1;
	if(mysqli_num_rows($results)) {
		while($row = mysqli_fetch_assoc($results)) {?>
							<tr>
								<td></td>
								<td><?=$row["chrCustomerNumber"]?></td>
								<td><?=$row["chrPhoneType"]?></td>
							</tr>
<?			$i++;
		}
	} ?>	
						</table>
	

					</div>					
					<div><img src="<?=$BF?>images/cap_bottom-410-blue.gif" /></div>

				</div>


			</td>
			<td width="10"><!-- gutter --></td>
			<td valign="top" width="270">

				<div class='greenbox'>
					<table cellspacing="0" cellpadding="0" class='greentop'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-tasks.gif' alt='telephone' /></td>
							<td class='colorBoxTitle'>Customer Addresses</td>
						</tr>
					</table>


					<div class='colorBoxPadding'>
						<table cellspacing="0" cellpadding="0" style='width: 395px; margin-left: 5px;'>
							<tr>
								<td colspan='3'>
									<div class='FormName'>Address Types</div>
									<?=$info['chrAddressType']?>
								</td>

							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'>Street</div>
									<?=$info["chrAddress1"]?><br />
									<?=$info["chrAddress2"]?><br />
									<?=$info["chrAddress3"]?>
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'>City</span></div>
									<?=$info["chrCity"]?>
								</td>
								<td>
									<div class='FormName'>Locales</span></div>
									<?=$info['chrLocale']?>
								</td>
								<td>
									<div class='FormName'>Postal Code</span></div>
									<?=$info["chrPostalCode"]?>
								</td>
							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'>Countries</span></div>
									<?=$info['chrCountry']?>

								</td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT chrLocale,chrCountry, chrAddress1,chrAddress2,chrAddress3,chrAddressType,chrCity,chrPostalCode
		FROM CustomerAddresses
		JOIN AddressTypes ON AddressTypes.ID=CustomerAddresses.idAddressType
		JOIN Locales ON Locales.ID=CustomerAddresses.idLocale
		JOIN Countries ON Countries.ID=CustomerAddresses.idCountry
		WHERE !CustomerAddresses.bDeleted AND CustomerAddresses.idCustomer='". $info['ID'] ."' AND !CustomerAddresses.bPrimary
	","getting Customer addresses"); ?>

					<div class='colorBoxPadding' id='moreAddresses' style='display: <?=(mysqli_num_rows($results) ? '' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 395px; margin-left: 5px;' id='addresslist'>
<?	if(mysqli_num_rows($results)) {
		$i = 1;
		while($row = mysqli_fetch_assoc($results)) { ?>
							<tbody id='addrtbd<?=$i?>'>

							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Address Types</div>
									<?=$row['chrAddressType']?>
								</td>

							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Street</span></div>
									<?=$row['chrAddress1']?><br />
									<?=$row['chrAddress2']?><br />
									<?=$row['chrAddress3']?>
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'><span class='red'>*</span> City</span></div>
									<?=$row['chrCity']?>
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Locales</span></div>
									<?=$row['chrLocale']?>
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<?=$row['chrPostalCode']?>
								</td>
							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Countries</span></div>
									<?=$row['chrCountry']?>

								</td>
							</tr>
							</tbody>
<?			$i++;
		}
	} ?>
						</table>
					</div>					
	
					<div><img src="<?=$BF?>images/cap_bottom-410-green.gif" /></div>
				</div>

			</td>
		</tr>	
	</table>

	</form>	


	<div id='overlaypage' class='overlaypage'>
		<div id='gray' class='gray'></div>
		<div id='message' class='message'>
			<div class='warning' id='warning'>
				<div class='red'>WARNING!!</div>
				<div class='body'>
					<div>You are about to remove: <br />
		
						Name: <span id='delName' style='color: blue;'></span><br />
						<input type='hidden' value='' id='idDel' name='idDel' />
						<input type='hidden' value='' id='chrKEY' name='chrKEY' />
						<input type='hidden' value='people_assoc' id='table' name='table' />
					</div>
					<div style='margin-top: 20px; '><strong>Are you sure you want to do this? It cannot be undone!</strong><br />
						<input type='button' value='Yes' onclick="javascript:delItem('<?=$BF?>crm/ajax_contacts.php?postType=delete&tbl=Contacts&idCustomer=<?=$info['ID']?>','customer');" /> &nbsp;&nbsp; <input type='button' value='No' onclick="javascript:revert();" />
					</div>
				</div>
			</div>
		</div>
	</div>
	

<div>Contacts <input type='button' value='+' onclick='javascript:newwin=window.open("<?=$BF?>crm/popup-addperson.php?idCustomer=<?=$info['ID']?>&tbl=people_assoc","new","width=600,height=400,resizable=1"); newwin.focus();' />
 </div>

<table id='people_assoc' class='List' style='width: 830px;' cellpadding="0" cellspacing="0">
	<tr>
<?		$extras = 'key='.$_REQUEST['key']; ?>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Email Address</th>
		<th class='options'><img src="<?=$BF?>images/options.gif"></th>
	</tr>		

<?	$results = db_query("SELECT People.ID,Contacts.chrKEY,chrFirst,chrLast,chrEmail FROM People JOIN Contacts ON Contacts.idPerson=People.ID WHERE Contacts.idCustomer='". $info['ID'] ."'","getting contacts");
	$count=0;
	while ($row=mysqli_fetch_assoc($results)) {
		$link='';
?>
	<tr id='people_assoctr<?=$row['ID']?>' class='<?=($count++%2?'ListLineOdd':'ListLineEven')?>' 
		onmouseover='RowHighlight("people_assoctr<?=$row['ID']?>");' onmouseout='UnRowHighlight("people_assoctr<?=$row['ID']?>");'>
		<td onclick='<?=$link?>'><?=$row['chrFirst']?></td>
		<td onclick='<?=$link?>'><?=$row['chrLast']?></td>
		<td onclick='<?=$link?>'><?=$row['chrEmail']?></td>
		<td class='options'><?=deleteButton($row['ID'],$row['chrFirst']." ".$row['chrLast'],$row['chrKEY'])?></td>
	</tr>
<?	} 
	if($count == 0) {
?>
	<tr id='people_assoctr0'>
		<td colspan='4' style='text-align: center; display: normal;' height="20">No People to display</td>
	</tr>
<?
	}
?>
</table>


<?	include($BF. "includes/bottom.php"); ?>
