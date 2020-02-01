<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	// If a post occured
	if(isset($_POST['chrCustomer'])) { // When doing isset, use a required field.  Faster than the php count funtion.

		$table = 'Customers'; # added so not to forget to change the insert AND audit

		$q = "INSERT INTO ". $table ." SET 
			chrKEY = '". makekey() ."',
			chrCustomer = '". encode($_POST['chrCustomer']) ."',
			chrCEmail = '". encode($_POST['chrCEmail']) ."',
			dtCreated=now()
		";
		
		# if there database insertion is successful	
		if(db_query($q,"Insert into ". $table)) {

			// This is the code for inserting the Audit Page
			// Type 1 means ADD NEW RECORD, change the TABLE NAME also
			global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
			$newID = mysqli_insert_id($mysqli_connection);
		
			$q = "INSERT INTO Audit SET 
				idType=1, 
				idRecord='". $newID ."',
				txtNewValue='". encode($_POST['chrCustomer']) ."',
				dtDateTime=now(),
				chrTableName='". $table ."',
				idPerson='". $_SESSION['idPerson'] ."'
			";
			db_query($q,"Insert audit");
			//End the code for History Insert 
		
		
			/* All the extra Phone Numbers */
			$q = "INSERT INTO CustomerNumbers (chrKEY,bPrimary,idCustomer,idPhoneType,dtCreated,chrCustomerNumber) VALUES  
				('". makekey() ."',1,'". $newID ."','". $_POST['idPhoneType'] ."',now(),'". encode($_POST['chrCustomerNumber']) ."'),";
			
			if($_POST['intPhones'] > 1 || $_POST['chrCustomerNumber1'] != "") {
				$i = 1;
				while($i <= $_POST['intPhones']) {
					if($_POST['chrCustomerNumber'.$i] != "") {
						$q .= "('". makekey() ."',0,'". $newID ."','". $_POST['idPhoneType'.$i] ."',now(),'".encode($_POST['chrCustomerNumber'.$i]) ."'),";
					}
					$i++;
				}
			}
			db_query(substr($q,0,-1),"inserting phones");
		

			/* All the extra Addresses */
			$q = "INSERT INTO CustomerAddresses (chrKEY,bPrimary,idCustomer,idLocale,idCountry,idAddressType,dtCreated,chrAddress1,chrAddress2,chrAddress3,chrCity,chrPostalCode) VALUES  
				('". makekey() ."',1,'". $newID ."','". $_POST['idLocale'] ."','". $_POST['idCountry'] ."','". $_POST['idAddressType'] ."',now(),'". encode($_POST['chrAddress1']) ."','". encode($_POST['chrAddress2']) ."','". encode($_POST['chrAddress3']) ."','". encode($_POST['chrCity']) ."','". encode($_POST['chrPostalCode']) ."'),";
			
			if($_POST['intAddresses'] > 1 || $_POST['chrAddress1-1'] != "") {
				$i = 1;
				while($i <= $_POST['intAddresses']) {
					if($_POST['chrAddress1-'.$i] != "") {
						$q .= "('". makekey() ."',0,'". $newID ."','". $_POST['idLocale'] ."','". $_POST['idCountry'] ."','". encode($_POST['idAddressType'.$i]) ."',now(),'". encode($_POST['chrAddress1-'.$i]) ."','". encode($_POST['chrAddress2-'.$i]) ."','". encode($_POST['chrAddress3-'.$i]) ."','". encode($_POST['chrCity'.$i]) ."','". encode($_POST['chrPostalCode'.$i]) ."'),";
					}
					$i++;
				}
			}
			db_query(substr($q,0,-1),"inserting addresses");
		
		
		
		
		
			$_SESSION['InfoMessage'] = $_POST['chrCustomer']. " has been successfully added to the Database.";
			header("Location: ". $_POST['moveTo']);
			die();
			
			
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occured while trying to add the customer "'. $_POST['chrCustomer'] .'".');
		}
	}
	
	$ptresults = db_query("SELECT ID,chrPhoneType FROM PhoneTypes WHERE !bDeleted ORDER BY intOrder,chrPhoneType","phone Types");
	$phoneTypes = "<select name='idPhoneType' id='idPhoneType'><option value=''>-Select Phone Types-</option>";
	while($row = mysqli_fetch_assoc($ptresults)) {
		$phoneTypes .= "<option value='". $row['ID'] ."'>". $row['chrPhoneType'] ."</option>";
	}
	$phoneTypes .= "</select>";

	$localeresults = db_query("SELECT ID,chrLocale FROM Locales WHERE !bDeleted AND bShow ORDER BY intOrder,chrLocale","get locales");
	$locales = "<select name='idLocale' id='idLocale' style='width: 150px;'><option value=''>-Select Locale-</option>";
	while($row = mysqli_fetch_assoc($localeresults)) {
		$locales .= "<option value='". $row['ID'] ."'>". $row['chrLocale'] ."</option>";
	}
	$locales .= "</select>";

	$countryresults = db_query("SELECT ID,chrCountry FROM Countries WHERE !bDeleted AND bShow ORDER BY intOrder,chrCountry","get countries");
	$countries = "<select name='idCountry' id='idCountry'><option value=''>-Select Country-</option>";
	while($row = mysqli_fetch_assoc($countryresults)) {
		$countries .= "<option value='". $row['ID'] ."'>". $row['chrCountry'] ."</option>";
	}
	$countries .= "</select>";

	$catresults = db_query("SELECT ID,chrAddressType FROM AddressTypes WHERE !bDeleted AND bShow ORDER BY intOrder,chrAddressType","address types");
	$custAddrTypes = "<select name='idAddressType' id='idAddressType'><option value=''>-Select Address Type-</option>";
	while($row = mysqli_fetch_assoc($catresults)) {
		$custAddrTypes .= "<option value='". $row['ID'] ."'>". $row['chrAddressType'] ."</option>";
	}
	$custAddrTypes .= "</select>";
	
	$title = 'Add Customer';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript'>
	function addPhone() {
		if(document.getElementById('moreNumbers').style.display == "none") { 
			document.getElementById('moreNumbers').style.display = "";
		} else {
			var id = (parseInt(document.getElementById('intPhones').value) + 1);
			if(!document.getElementById(id)) {
				var table = document.getElementById('phonelist');
		
				var tbdy = document.createElement("tbody");
				var tr = document.createElement('tr');
				var td1 = document.createElement('td');
				var td2 = document.createElement('td');
				var td3 = document.createElement('td');
	
				var inputph = document.createElement('input');
					inputph.name = 'chrCustomerNumber'+(id);
					inputph.size = 20;
					
				var tmpSel = "<?=($phoneTypes)?>".replace(/idPhoneType/g, 'idPhoneType'+id);
				
				var selphtype = document.createElement('div');
					selphtype.name = 'idPhoneType'+(id);
					selphtype.innerHTML = tmpSel;
					
				td2.appendChild(inputph);
				td3.appendChild(selphtype);
				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tbdy.appendChild(tr);
				table.appendChild(tbdy);
		
				document.getElementById('intPhones').value = id;
			}
		}
	}	

	function addAddress() {
		if(document.getElementById('moreAddresses').style.display == "none") { 
			document.getElementById('moreAddresses').style.display = "";
		} else {

			var id = (parseInt(document.getElementById('intAddresses').value) + 1);
			if(!document.getElementById(id)) {
				var table = document.getElementById('addresslist');
		
				var tbdy = document.createElement("tbody");
				var tr1 = document.createElement('tr');
				var tr2 = document.createElement('tr');
				var tr3 = document.createElement('tr');
				var tr4 = document.createElement('tr');

				var td1 = document.createElement('td');
					td1.colSpan = 3;
				var td2 = document.createElement('td');
					td2.colSpan = 3;
				var td3 = document.createElement('td');
				var td4 = document.createElement('td');
				var td5 = document.createElement('td');
					td5.colSpan = 3;
				var td6 = document.createElement('td'); // blank spot
	
	
				var inputadd1 = document.createElement('input');
					inputadd1.name = 'chrAddress1-'+(id);
					inputadd1.size = 30;
				var inputadd2 = document.createElement('input');
					inputadd2.name = 'chrAddress2-'+(id);
					inputadd2.size = 30;
				var inputadd3 = document.createElement('input');
					inputadd3.name = 'chrAddress3-'+(id);
					inputadd3.size = 30;
				var inputcity = document.createElement('input');
					inputcity.name = 'chrCity'+(id);
					inputcity.size = 10;
				var inputpostal = document.createElement('input');
					inputpostal.name = 'chrPostalCode'+(id);
					inputpostal.size = 10;
					
				var tmpAddr = "<?=($custAddrTypes)?>".replace(/idAddressType/g, 'idAddressType'+id);
				var tmpCountry = "<?=($countries)?>".replace(/idCountry/g, 'idCountry'+id);
				var tmpLocale = "<?=($locales)?>".replace(/idLocale/g, 'idLocale'+id);
				
				var seladdrtype = document.createElement('div');
					seladdrtype.name = 'idAddressType'+(id);
					seladdrtype.innerHTML = tmpAddr;
	
				var selcountry = document.createElement('div');
					selcountry.name = 'idCountry'+(id);
					selcountry.innerHTML = tmpCountry;
	
				var sellocale = document.createElement('div');
					sellocale.name = 'idLocale'+(id);
					sellocale.innerHTML = tmpLocale;
					
				var text = document.createElement('div'); text.className = 'FormName'; text.innerHTML = "<span class='red'>*</span> Address Types";
				td1.appendChild(text);
				td1.appendChild(seladdrtype);

				var text = document.createElement('div'); text.className = 'FormName'; text.innerHTML = "<span class='red'>*</span> Street";
				td2.appendChild(text);
				td2.appendChild(inputadd1);
				td2.appendChild(inputadd2);
				td2.appendChild(inputadd3);
				

				var text = document.createElement('div'); text.className = 'FormName'; text.innerHTML = "<span class='red'>*</span> City";
				td3.appendChild(text);
				td3.appendChild(inputcity);
				var text = document.createElement('div'); text.className = 'FormName'; text.innerHTML = "<span class='red'>*</span> Locale";
				td4.appendChild(text);
				td4.appendChild(sellocale);
				var text = document.createElement('div'); text.className = 'FormName'; text.innerHTML = "<span class='red'>*</span> Postal Code";
				td5.appendChild(text);
				td5.appendChild(inputpostal);
	
				var text = document.createElement('div'); text.className = 'FormName'; text.innerHTML = "<span class='red'>*</span> Countries";
				td6.appendChild(text);
				td6.appendChild(selcountry);

				tr1.appendChild(td1);
				tr2.appendChild(td2);
				tr3.appendChild(td3);
				tr3.appendChild(td4);
				tr3.appendChild(td5);
				tr4.appendChild(td6);
				
				tbdy.appendChild(tr1);
				tbdy.appendChild(tr2);
				tbdy.appendChild(tr3);
				tbdy.appendChild(tr4);
				table.appendChild(tbdy);
		
				document.getElementById('intAddresses').value = id;
			}
		}
	}	
</script>
<script language="javascript" type='text/javascript' src="<?=$BF?>includes/forms.js"></script>
<script language="javascript" type='text/javascript'>
	var totalErrors = 0;
	function error_check() {
		if(totalErrors != 0) { reset_errors(); }  
		
		totalErrors = 0;

		if(errEmpty('chrCustomer', "You must enter a Customer Name.")) { totalErrors++; }
		if(errEmpty('chrCustomerNumber', "You must enter a Phone Number")) { totalErrors++; }
		if(errEmpty('idPhoneType', "You must choose a Phone Type")) { totalErrors++; }
		if(errEmpty('idAddressType', "You must choose an Address Type")) { totalErrors++; }
		if(errEmpty('chrAddress1', "You must enter an Address")) { totalErrors++; }
		if(errEmpty('chrCity', "You must enter a City")) { totalErrors++; }
		if(errEmpty('chrPostalCode', "You must enter a Postal Code")) { totalErrors++; }
		if(errEmpty('idLocale', "You must choose a Locale")) { totalErrors++; }
		if(errEmpty('idCountry', "You must choose a Country")) { totalErrors++; }
	
		return (totalErrors == 0 ? true : false);
	}
	

</script>
<?	

	# this is added to set the cursor into the first available text box for typing
	$bodyParams = "document.getElementById('chrCustomer').focus()";

	$section = 'crm';
	$leftlink = "viewcustomer";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Add Customer"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-company.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please fill in all the fields and press the "Add Another" to add another customer or "Add and Continue" to return to the customers list.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_crm.php');
?>
	<form action="" method="post" id="idForm" onsubmit="return error_check()">
	

	<div class='marginbottom10'>
	<input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addcustomer.php';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='index.php';" /> <span style='font-size: 10px;'><span style='color: red;'>*</span> All red Asterix fields are required.</span>
	</div>

	<div id='errors' style='width: 830px;'></div>
	<table width="830" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" width="410">




				<div class='bluebox'>
					<table cellspacing="0" cellpadding="0" class='bluetop'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-contacts.gif' alt='telephone' /></td>
							<td class='colorBoxTitle'>Customer Information</td>
						</tr>
					</table>

					<div class='colorBoxPadding'>
						<div class='FormName'><span class='red'>*</span> Customer Name</div>
						<div class='FormField'><input type="text" name="chrCustomer" id="chrCustomer" style='width: 325px;' maxlength="200" /></div>
					</div>
					<div class='colorBoxPadding'>
						<div class='FormName'><span class='red'>*</span> Customer Email</div>
						<div class='FormField'><input type="text" name="chrCEmail" id="chrCEmail" style='width: 325px;' maxlength="100" /></div>
					</div>
	
					<div><img src="../images/cap_bottom-410-blue.gif" /></div>
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
								<td colspan='2' class='FormName'><span class='red'>*</span> Primary Number</td>
								<td class='FormName'><span class='red'>*</span> Number Type</td>
							</tr>
							<tr>
								<td></td>
								<td><input type='text' name='chrCustomerNumber' id='chrCustomerNumber' size='20' /></td>
								<td> <?=$phoneTypes?></td>
							</tr>
						</table>
					</div>

					<div class='colorBoxPadding' id='moreNumbers' style='display: none;'>
						<table cellspacing="0" cellpadding="0" style='width: 100%;' id='phonelist'>
							<tr>
								<td colspan='3' class='FormName'>Additional Number(s)</td>
							</tr>
							<tr>
								<td></td>
								<td><input type='text' name='chrCustomerNumber1' id='chrCustomerNumber1' size='20' /></td>
								<td><?=str_replace('idPhoneType','idPhoneType1',$phoneTypes)?></td>
							</tr>
						</table>
	

					</div>					
					<div class='marginleft5'><a href='javascript:addPhone()'><img src='<?=$BF?>images/button-addanother.gif' alt='add another' /></a><input type='hidden' name='intPhones' id='intPhones' value='1' /></div>
					<div><img src="../images/cap_bottom-410-blue.gif" /></div>
				</div>


			</td>
			<td width="10"><!-- gutter --></td>
			<td valign="top" width="410">

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
									<div class='FormName'><span class='red'>*</span> Address Types</div>
									<?=$custAddrTypes?>
								</td>

							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Street</span></div>
									<input type='text' name='chrAddress1' id='chrAddress1' size='30' />
									<input type='text' name='chrAddress2' id='chrAddress2' size='30' />
									<input type='text' name='chrAddress3' id='chrAddress3' size='30' />
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'><span class='red'>*</span> City</span></div>
									<input type='text' name='chrCity' id='chrCity' size='10' />
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Locales</span></div>
									<?=$locales?>
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<input type='text' name='chrPostalCode' id='chrPostalCode' size='10' />
								</td>
							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Countries</span></div>
									<?=$countries?>

								</td>
							</tr>
						</table>
					</div>					

					<div class='colorBoxPadding' id='moreAddresses' style='display: none;'>
						<table cellspacing="0" cellpadding="0" style='width: 395px; margin-left: 5px;' id='addresslist'>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Address Types</div>
									<?=str_replace('idAddressType','idAddressType1',$custAddrTypes)?>
								</td>

							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Street</span></div>
									<input type='text' name='chrAddress1-1' id='chrAddress1-1' size='30' />
									<input type='text' name='chrAddress2-1' id='chrAddress2-1' size='30' />
									<input type='text' name='chrAddress3-1' id='chrAddress3-1' size='30' />
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'><span class='red'>*</span> City</span></div>
									<input type='text' name='chrCity1' id='chrCity1' size='10' />
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Locales</span></div>
									<?=str_replace('idLocale','idLocale1',$locales)?>
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<input type='text' name='chrPostalCode1' id='chrPostalCode1' size='10' />
								</td>
							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Countries</span></div>
									<?=str_replace('idCountry','idCountry1',$countries)?>

								</td>
							</tr>
						</table>
					</div>					
	
					<div class='marginleft5'><a href='javascript:addAddress()'><img src='<?=$BF?>images/button-addanother.gif' alt='add another' /></a><input type='hidden' name='intAddresses' id='intAddresses' value='1' /></div>
					<div><img src="../images/cap_bottom-410-green.gif" /></div>
				</div>



			</td>
		</tr>	
	</table>
	
	<div class='margintop10;'>
	<input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addcustomer.php';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='index.php';" /> <span style='font-size: 10px;'><span style='color: red;'>*</span> All red Asterix fields are required.</span>
		<input type='hidden' name='moveTo' id='moveTo' />
	</div>

	</form>	
<?
	include($BF. "includes/bottom.php");
?>
