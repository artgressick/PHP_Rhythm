<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');


	$info = db_query("SELECT People.chrKEY,People.ID,People.chrFirst,People.chrLast,People.chrEmail,PeopleAddresses.ID as idPersonAddress,
		  PeopleAddresses.idLocale,PeopleAddresses.idCountry,PeopleAddresses.chrCity,PeopleAddresses.chrPostalCode,
		    PeopleAddresses.chrAddress1,PeopleAddresses.chrAddress2,PeopleAddresses.chrAddress3,PeopleAddresses.idAddressType,
		  PeopleNumbers.idPhoneType,PeopleNumbers.chrPersonNumber,PeopleNumbers.ID as idPersonNumber,
		  PeopleEmails.chrPersonEmail, PeopleEmails.ID as idPersonEmail,
		  PeopleIms.chrPersonIm, PeopleIms.idImType,PeopleIms.ID as idPersonIm,
		  AddressTypes.chrAddressType,PhoneTypes.chrPhoneType,ImTypes.chrImType,Locales.chrLocale,Countries.chrCountry
		FROM People 
		JOIN PeopleAddresses ON PeopleAddresses.idPerson=People.ID AND PeopleAddresses.bPrimary
		LEFT JOIN AddressTypes ON AddressTypes.ID=PeopleAddresses.idAddressType
		LEFT JOIN Locales ON Locales.ID=PeopleAddresses.idLocale
		LEFT JOIN Countries ON Countries.ID=PeopleAddresses.idCountry
		JOIN PeopleNumbers ON PeopleNumbers.idPerson=People.ID AND PeopleNumbers.bPrimary
		LEFT JOIN PhoneTypes ON PhoneTypes.ID=PeopleNumbers.idPhoneType
		JOIN PeopleEmails ON PeopleEmails.idPerson=People.ID AND PeopleEmails.bPrimary
		JOIN PeopleIms ON PeopleIms.idPerson=People.ID AND PeopleIms.bPrimary
		LEFT JOIN ImTypes ON ImTypes.ID=PeopleIms.idImType
		WHERE !People.bDeleted AND People.chrKEY='". $_REQUEST['key'] ."'
	","getting people info",1);
	
	$title = 'Edit Person';
	include($BF .'includes/meta.php');


?><script language="JavaScript" type='text/javascript' src="<?=$BF?>includes/popup.js"></script><?	

	# This is for the sorting of the rows and columns.  We must set the default order and name
	include($BF. 'components/list/sortList.php'); 
	if(!isset($_REQUEST['sortCol'])) { $_REQUEST['sortCol'] = "chrCustomer"; } # This sets the default column order.  Asc by default.


	$section = 'crm';
	$leftlink = "viewcustomer";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "View Person"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-company.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $info['chrFirst']." ".$info['chrLast']; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'View page for this contact.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_crm.php');
?>

	<div id='errors' style='width: 830px;'></div>
	<table width="830" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" width="270">




				<div class='bluebox'>
					<table cellspacing="0" cellpadding="0" class='bluetop270'>
						<tr>
							<td class='colorBoxTitle'>Contact Information</td>
						</tr>
					</table>

					<div class='colorBoxPadding270'>
						<div class='FormName'>First Name</div>
						<div class='FormDisplay'><?=$info["chrFirst"]?> <?=$info["chrLast"]?></div>
					</div>
	
					<div><img src="../images/cap_bottom-270-blue.gif" /></div>
				</div>



				<div class='bluebox'>
					<table cellspacing="0" cellpadding="0" class='bluetop270'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-people2.gif' alt='ims' /></td>
							<td class='colorBoxTitle'>Contact Instant Messanger(s)</td>
						</tr>
					</table>
					

					<div class='colorBoxPadding270'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;'>
							<tr>
								<td class='FormName'>Primary IM</td>
								<td class='FormName'>IM Type</td>
							</tr>
							<tr>
								<td class='FormDisplay'><?=$info['chrPersonIm']?></td>
								<td class='FormDisplay'><?=$info['chrImType']?></td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT chrPersonIm,chrImType
			FROM PeopleIms 
			JOIN ImTypes ON ImTypes.ID=PeopleIms.idImType
			WHERE !PeopleIms.bDeleted AND PeopleIms.idPerson='". $info['ID'] ."' AND !PeopleIms.bPrimary 
			ORDER BY PeopleIms.ID
		","getting ims"); ?>
					<div class='colorBoxPadding270' id='moreIms' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;' id='imlist'>
							<tr>
								<td colspan='2' class='FormName'>Additional IM(s)</td>
							</tr>
<?	$i = 1;
	if(mysqli_num_rows($results)) {
		while($row = mysqli_fetch_assoc($results)) { ?>
							<tr>
								<td class='FormDisplay'><?=$row["chrPersonIm"]?></td>
								<td class='FormDisplay'><?=$row['chrImType']?></td>
							</tr>
<?			$i++;
		}
	} ?>	
						</table>
	

					</div>		
					<div><img src="../images/cap_bottom-270-blue.gif" /></div>
			
				</div>


			</td>
			<td width="10"><!-- gutter --></td>
			<td valign="top" width="270">

				<div class='greenbox'>
					<table cellspacing="0" cellpadding="0" class='greentop270'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-email.gif' alt='email' /></td>
							<td class='colorBoxTitle'>Contact Email Address(es)</td>
						</tr>
					</table>


					<div class='colorBoxPadding270'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;'>
							<tr>
								<td class='FormName'><span class='red'>*</span> Primary Email</td>
							</tr>
							<tr>
								<td class='FormDisplay'><?=$info["chrEmail"]?></td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT chrPersonEmail
			FROM PeopleEmails 
			WHERE !PeopleEmails.bDeleted AND PeopleEmails.idPerson='". $info['ID'] ."' AND !PeopleEmails.bPrimary 
			ORDER BY PeopleEmails.ID
		","getting ims"); ?>

					<div class='colorBoxPadding270' id='moreEmails' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;' id='emaillist'>
							<tr>
								<td class='FormName'>Additional Email(s)</td>
							</tr>
<?	$i = 1;
	if(mysqli_num_rows($results)) {
		while($row = mysqli_fetch_assoc($results)) {?>
							<tr>
								<td class='FormDisplay'><?=$row["chrPersonEmail"]?></td>
							</tr>
<?			$i++;
		}
	} ?>	
						</table>
	

					</div>
					<div><img src="../images/cap_bottom-270-green.gif" /></div>
				</div>



				<div class='greenbox'>
					<table cellspacing="0" cellpadding="0" class='greentop270'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-calls.gif' alt='telephone' /></td>
							<td class='colorBoxTitle'>Contact Phone Number(s)</td>
						</tr>
					</table>



					<div class='colorBoxPadding270'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;'>
							<tr>
								<td class='FormName'><span class='red'>*</span> Primary Number</td>
								<td class='FormName'><span class='red'>*</span> Number Type</td>
							</tr>
							<tr>
								<td class='FormDisplay'><?=$info["chrPersonNumber"]?></td>
								<td class='FormDisplay'><?=$info['chrPhoneType']?></td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT chrPersonNumber,chrPhoneType
			FROM PeopleNumbers 
			JOIN PhoneTypes ON PhoneTypes.ID=PeopleNumbers.idPhoneType
			WHERE !PeopleNumbers.bDeleted AND PeopleNumbers.idPerson='". $info['ID'] ."' AND !PeopleNumbers.bPrimary 
			ORDER BY PeopleNumbers.ID
		","getting phones"); ?>
					<div class='colorBoxPadding270' id='moreNumbers' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;' id='phonelist'>
							<tr>
								<td class='FormName'>Additional Number(s)</td>
							</tr>
<?	$i = 1;
	if(mysqli_num_rows($results)) {
		while($row = mysqli_fetch_assoc($results)) {
?>
							<tr>
								<td class='FormDisplay'><?=$row["chrPersonNumber"]?></td>
								<td class='FormDisplay'><?=$row["chrPhoneType"]?></td>
							</tr>
<?			$i++;
		}
	} ?>	
						</table>
	

					</div>
					<div><img src="../images/cap_bottom-270-green.gif" /></div>

				</div>




			</td>
			
			<td width="10"><!-- gutter --></td>
			
			<td valign="top" width="270">



				<div class='bluebox'>
					<table cellspacing="0" cellpadding="0" class='bluetop270'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-tasks.gif' alt='telephone' /></td>
							<td class='colorBoxTitle'>Contact Address(es)</td>
						</tr>
					</table>


					<div class='colorBoxPadding270'>
						<table cellspacing="0" cellpadding="0" style='width: 250px; margin-left: 5px;'>
							<tr>
								<td colspan='2'>
									<div class='FormName'>Address Types</div>
									<div class='FormDisplay'><?=$info['chrAddressType']?></div>
								</td>

							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'>Street</div>
									<div class='FormDisplay'><?=$info["chrAddress1"]?></div>
									<div class='FormDisplay'><?=$info["chrAddress2"]?></div>
									<div class='FormDisplay'><?=$info["chrAddress3"]?></div>
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'>City</div>
									<div class='FormDisplay'><?=$info["chrCity"]?></div>
								</td>
								<td>
									<div class='FormName'>Postal Code</div>
									<div class='FormDisplay'><?=$info["chrPostalCode"]?></div>
								</td>

							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'>Locales</div>
									<div class='FormDisplay'><?=$info["chrLocale"]?></div>
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'>Countries</div>
									<div class='FormDisplay'><?=$info["chrCountry"]?></div>
								</td>
							</tr>
						</table>
					</div>					

<?	$results = db_query("SELECT chrAddress1,chrAddress2,chrAddress3,chrCity,chrPostalCode,chrAddressType,chrLocale,chrCountry
			FROM PeopleAddresses 
			JOIN AddressTypes ON AddressTypes.ID=PeopleAddresses.idAddressType
			JOIN Locales ON Locales.ID=PeopleAddresses.idLocale
			JOIN Countries ON Countries.ID=PeopleAddresses.idCountry
			WHERE !PeopleAddresses.bDeleted AND PeopleAddresses.idPerson='". $info['ID'] ."' AND !PeopleAddresses.bPrimary 
			ORDER BY PeopleAddresses.ID
		","getting addresses"); ?>

					<div class='colorBoxPadding270' id='moreAddresses' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px; margin-left: 5px;' id='addresslist'>
<?	if(mysqli_num_rows($results)) {
		$i = 1;
		while($row = mysqli_fetch_assoc($results)) {
?>
							<tbody id='addrtbd<?=$i?>'>

							<tr>
								<td colspan='2'>
									<div class='FormName'>Address Types</div>
									<?=$row['chrAddressType']?>
								</td>

							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'>Street</div>
									<div class='FormDisplay'><?=$row['chrAddress1']?></div>
									<div class='FormDisplay'><?=$row['chrAddress2']?></div>
									<div class='FormDisplay'><?=$row['chrAddress3']?></div>
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'>City</div>
									<div class='FormDisplay'><?=$row['chrCity']?></div>
								</td>
								<td>
									<div class='FormName'>Postal Code</div>
									<div class='FormDisplay'><?=$row['chrPostalCode']?></div>
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'>Locales</div>
									<div class='FormDisplay'><?=$row['chrLocales']?></div>
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'>Countries</div>
									<div class='FormDisplay'><?=$row['chrCountry']?></div>
								</td>
							</tr>
							</tbody>
<?			$i++;
		}
	} ?>
						</table>
					</div>					
					<div><img src="../images/cap_bottom-270-blue.gif" /></div>
	
				</div>


			</td>

		</tr>	
	</table>



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
						<input type='hidden' value='customer_assoc' id='table' name='table' />
					</div>
					<div style='margin-top: 20px; '><strong>Are you sure you want to do this? It cannot be undone!</strong><br />
						<input type='button' value='Yes' onclick="javascript:delItem('<?=$BF?>crm/ajax_contacts.php?postType=delete&tbl=Contacts&idPerson=<?=$info['ID']?>','person');" /> &nbsp;&nbsp; <input type='button' value='No' onclick="javascript:revert();" />
					</div>
				</div>
			</div>
		</div>
	</div>
	

<div>Customers <input type='button' value='+' onclick='javascript:newwin=window.open("<?=$BF?>crm/popup-addcustomer.php?idPerson=<?=$info['ID']?>&tbl=customer_assoc","new","width=600,height=400,resizable=1"); newwin.focus();' />
 </div>

<table id='customer_assoc' class='List' style='width: 830px;' cellpadding="0" cellspacing="0">
	<tr>
<?		$extras = 'key='.$_REQUEST['key']; ?>
		<th>Customer</th>
		<th class='options'><img src="<?=$BF?>images/options.gif"></th>
	</tr>		

<?	$results = db_query("SELECT Customers.ID,Contacts.chrKEY,chrCustomer FROM Customers JOIN Contacts ON Contacts.idCustomer=Customers.ID WHERE Contacts.idPerson='". $info['ID'] ."'","getting contacts");
	$count=0;
	while ($row=mysqli_fetch_assoc($results)) {
		$link='';
?>
	<tr id='customer_assoctr<?=$row['ID']?>' class='<?=($count++%2?'ListLineOdd':'ListLineEven')?>' 
		onmouseover='RowHighlight("customer_assoctr<?=$row['ID']?>");' onmouseout='UnRowHighlight("customer_assoctr<?=$row['ID']?>");'>
		<td onclick='<?=$link?>'><?=$row['chrCustomer']?></td>
		<td class='options'><?=deleteButton($row['ID'],$row['chrCustomer'],$row['chrKEY'])?></td>
	</tr>
<?	} 
	if($count == 0) {
?>
	<tr id='customer_assoctr0'>
		<td colspan='4' style='text-align: center; display: normal;' height="20">No Customers to display</td>
	</tr>
<?
	}
?>
</table>


<?
	include($BF. "includes/bottom.php");
?>
