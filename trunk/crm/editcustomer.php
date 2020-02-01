<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');


	$info = db_query("SELECT Customers.chrKEY,Customers.ID,Customers.chrCustomer,CustomerAddresses.ID as idCustomerAddress,
		  CustomerAddresses.idLocale,CustomerAddresses.idCountry,CustomerAddresses.chrCity,CustomerAddresses.chrPostalCode,
		    CustomerAddresses.chrAddress1,CustomerAddresses.chrAddress2,CustomerAddresses.chrAddress3,CustomerAddresses.idAddressType,
		  CustomerNumbers.idPhoneType,CustomerNumbers.chrCustomerNumber,CustomerNumbers.ID as idCustomerNumber,Customers.chrCEmail
		FROM Customers 
		JOIN CustomerAddresses ON CustomerAddresses.idCustomer=Customers.ID
		JOIN CustomerNumbers ON CustomerNumbers.idCustomer=Customers.ID
		WHERE !Customers.bDeleted AND CustomerAddresses.bPrimary AND CustomerNumbers.bPrimary AND Customers.chrKEY='". $_REQUEST['key'] ."'
	","getting customer info",1);
	
	if(isset($_POST['chrCustomer'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
		
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'Customers';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrCustomer',$info['chrCustomer'],$audit,$table,$info['ID'],0);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrCEmail',$info['chrCEmail'],$audit,$table,$info['ID'],0);
			
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['ID']); }

		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'CustomerAddresses';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idLocale',$info['idLocale'],$audit,$table,$info['idCustomerAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idCountry',$info['idCountry'],$audit,$table,$info['idCustomerAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idAddressType',$info['idAddressType'],$audit,$table,$info['idCustomerAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrAddress1',$info['chrAddress1'],$audit,$table,$info['idCustomerAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrAddress2',$info['chrAddress2'],$audit,$table,$info['idCustomerAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrAddress3',$info['chrAddress3'],$audit,$table,$info['idCustomerAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrCity',$info['chrCity'],$audit,$table,$info['idCustomerAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrPostalCode',$info['chrPostalCode'],$audit,$table,$info['idCustomerAddress']);
			
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['idCustomerAddress'],0); }

		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'CustomerNumbers';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idPhoneType',$info['idPhoneType'],$audit,$table,$info['idCustomerNumber']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrCustomerNumber',$info['chrCustomerNumber'],$audit,$table,$info['idCustomerNumber']);
			
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['idCustomerNumber'],0); }
		
		db_query("DELETE FROM CustomerNumbers WHERE !bPrimary AND idCustomer=". $info['ID'],"deleting old phones");
		if($_POST['intPhones'] > 1 || $_POST['chrCustomerNumber1'] != "") {
			$q = "";
			$i = 1;
			while($i <= $_POST['intPhones']) {
				if($_POST['chrCustomerNumber'.$i] != "") {
					$q .= "('". makekey() ."',0,'". $info['ID'] ."','". $_POST['idPhoneType'.$i] ."',now(),'".encode($_POST['chrCustomerNumber'.$i]) ."'),";
				}
				$i++;
			}
			if($q != "") {
				db_query("INSERT INTO CustomerNumbers (chrKEY,bPrimary,idCustomer,idPhoneType,dtCreated,chrCustomerNumber) VALUES ".substr($q,0,-1),"inserting phones");
			}
		}
			

		db_query("DELETE FROM CustomerAddresses WHERE !bPrimary AND idCustomer=". $info['ID'],"deleting old addresses");
		if($_POST['intAddresses'] > 1 || $_POST['chrAddress1-1'] != "") {
			$i = 1;
			$q = "";
			while($i <= $_POST['intAddresses']) {
				if($_POST['chrAddress1-'.$i] != "") {
					$q .= "('". makekey() ."',0,'". $info['ID'] ."','". $_POST['idLocale'.$i] ."','". $_POST['idCountry'.$i] ."','". encode($_POST['idAddressType'.$i]) ."',now(),'". encode($_POST['chrAddress1-'.$i]) ."','". encode($_POST['chrAddress2-'.$i]) ."','". encode($_POST['chrAddress3-'.$i]) ."','". encode($_POST['chrCity'.$i]) ."','". encode($_POST['chrPostalCode'.$i]) ."'),";
				}
				$i++;
			}
			if($q != "") {
				db_query("INSERT INTO CustomerAddresses (chrKEY,bPrimary,idCustomer,idLocale,idCountry,
				  idAddressType,dtCreated,chrAddress1,chrAddress2,chrAddress3,chrCity,chrPostalCode) VALUES ".substr($q,0,-1)
				,"inserting addresses");
			}
		}

		
		
		header("Location: index.php");
		die();	
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
	
	$title = 'Edit Customer';
	include($BF .'includes/meta.php');

?>
<script language="javascript" type='text/javascript'>
	function addPhone() {
		if(document.getElementById('moreNumbers').style.display == "none") { 
			document.getElementById('moreNumbers').style.display = "normal";
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
			document.getElementById('moreAddresses').style.display = "normal";
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
	$banner_title = "Edit Customer"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-company.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please edit all the fields and press the "Update Information" button to edit the information for this customer.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_crm.php');
?>
	<form action="" method="post" id="idForm" onsubmit="return error_check()">
	

	<div class='marginbottom10'>
		<input type='submit' value='Update Information' /><span style='color: red;'>*</span> All red Asterix fields are required.</span>
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
						<div class='FormField'><input type="text" name="chrCustomer" id="chrCustomer" style='width: 325px;' maxlength="200" value='<?=$info["chrCustomer"]?>' /></div>
					</div>
					<div class='colorBoxPadding'>
						<div class='FormName'><span class='red'>*</span> Customer Email</div>
						<div class='FormField'><input type="text" name="chrCEmail" id="chrCEmail" style='width: 325px;' maxlength="100" value='<?=$info["chrCEmail"]?>' /></div>
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
								<td><input type='text' name='chrCustomerNumber' id='chrCustomerNumber' size='20' value='<?=$info["chrCustomerNumber"]?>' /></td>
								<td><?=str_replace("value='".$info['idPhoneType']."'","value='".$info['idPhoneType']."' selected='selected'",$phoneTypes)?></td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT * FROM CustomerNumbers WHERE !bDeleted AND idCustomer='". $info['ID'] ."' AND !bPrimary ORDER BY ID","getting Customer Numbers"); ?>
					<div class='colorBoxPadding' id='moreNumbers' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 100%;' id='phonelist'>
							<tr>
								<td colspan='3' class='FormName'>Additional Number(s)</td>
							</tr>
<?	$i = 1;
	if(mysqli_num_rows($results)) {
		while($row = mysqli_fetch_assoc($results)) {
			$tmpPhones = str_replace('idPhoneType','idPhoneType'.$i,$phoneTypes);
			$tmpPhones = str_replace("value='".$row['idPhoneType']."'","value='".$row['idPhoneType']."' selected='selected'",$tmpPhones);
?>
							<tr>
								<td></td>
								<td><input type='text' name='chrCustomerNumber<?=$i?>' id='chrCustomerNumber<?=$i?>' size='20' value='<?=$row["chrCustomerNumber"]?>' /></td>
								<td><?=$tmpPhones?></td>
							</tr>
<?			$i++;
		}
	} else { ?>
							<tr>
								<td></td>
								<td><input type='text' name='chrCustomerNumber1' id='chrCustomerNumber1' size='20' /></td>
								<td><?=str_replace('idPhoneType','idPhoneType1',$phoneTypes)?></td>
							</tr>	
<?	} ?>	
						</table>
	

					</div>					
					<div class='marginleft5'><a href='javascript:addPhone()'><img src='<?=$BF?>images/button-addanother.gif' alt='add another' /></a><input type='hidden' name='intPhones' id='intPhones' value='<?=($i == 1 ? 1 : --$i)?>' /></div>
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
									<?=str_replace("value='".$info['idAddressType']."'","value='".$info['idAddressType']."' selected",$custAddrTypes)?>
								</td>

							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Street</span></div>
									<input type='text' name='chrAddress1' id='chrAddress1' size='30' value='<?=$info["chrAddress1"]?>' /><br />
									<input type='text' name='chrAddress2' id='chrAddress2' size='30' value='<?=$info["chrAddress2"]?>' /><br />
									<input type='text' name='chrAddress3' id='chrAddress3' size='30' value='<?=$info["chrAddress3"]?>' />
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'><span class='red'>*</span> City</span></div>
									<input type='text' name='chrCity' id='chrCity' size='10' value='<?=$info["chrCity"]?>' />
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Locales</span></div>
									<?=str_replace("value='".$info['idLocale']."'","value='".$info['idLocale']."' selected='selected'",$locales)?>
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<input type='text' name='chrPostalCode' id='chrPostalCode' size='10' value='<?=$info["chrPostalCode"]?>' />
								</td>
							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Countries</span></div>
									<?=str_replace("value='".$info['idCountry']."'","value='".$info['idCountry']."' selected='selected'",$countries)?>

								</td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT * FROM CustomerAddresses WHERE !bDeleted AND idCustomer='". $info['ID'] ."' AND !bPrimary ORDER BY ID","getting Customer addresses"); ?>

					<div class='colorBoxPadding' id='moreAddresses' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 395px; margin-left: 5px;' id='addresslist'>
<?	if(mysqli_num_rows($results)) {
		$i = 1;
		while($row = mysqli_fetch_assoc($results)) {
			$tmpAddType = str_replace('idAddressType','idAddressType'.$i,$custAddrTypes);
			$tmpAddType = str_replace("value='".$row['idAddressType']."'","value='".$row['idAddressType']."' selected='selected'",$tmpAddType);
			
			$tmplocales = str_replace('idLocale','idLocale'.$i,$locales);
			$tmplocales = str_replace("value='".$row['idLocale']."'","value='".$row['idLocale']."' selected='selected'",$tmplocales);
			
			$tmpcountries = str_replace('idCountry','idCountry'.$i,$countries);
			$tmpcountries = str_replace("value='".$row['idCountry']."'","value='".$row['idCountry']."' selected='selected'",$tmpcountries);
?>
							<tbody id='addrtbd<?=$i?>'>

							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Address Types</div>
									<?=$tmpAddType?>
								</td>

							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Street</span></div>
									<input type='text' name='chrAddress1-<?=$i?>' id='chrAddress1-<?=$i?>' size='30' value='<?=$row['chrAddress1']?>' /><br />
									<input type='text' name='chrAddress2-<?=$i?>' id='chrAddress2-<?=$i?>' size='30' value='<?=$row['chrAddress2']?>' /><br />
									<input type='text' name='chrAddress3-<?=$i?>' id='chrAddress3-<?=$i?>' size='30' value='<?=$row['chrAddress3']?>' />
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'><span class='red'>*</span> City</span></div>
									<input type='text' name='chrCity<?=$i?>' id='chrCity<?=$i?>' size='10' value='<?=$row['chrCity']?>' />
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Locales</span></div>
									<?=$tmplocales?>
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<input type='text' name='chrPostalCode<?=$i?>' id='chrPostalCode<?=$i?>' size='10' value='<?=$row['chrPostalCode']?>' />
								</td>
							</tr>
							<tr>
								<td colspan='3'>
									<div class='FormName'><span class='red'>*</span> Countries</span></div>
									<?=$tmpcountries?>

								</td>
							</tr>
							</tbody>
<?			$i++;
		}
	} else { ?>
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


<?	} ?>
						</table>
					</div>					
	
					<div class='marginleft5'><a href='javascript:addAddress()'><img src='<?=$BF?>images/button-addanother.gif' alt='add another' /></a><input type='hidden' name='intAddresses' id='intAddresses' value='<?=($i == 1 ? 1 : --$i)?>' /></div>
					<div><img src="../images/cap_bottom-410-green.gif" /></div>
				</div>



			</td>
		</tr>	
	</table>
	
	<div class='margintop10;'>
		<input type='submit' value='Update Information' /><span style='color: red;'>*</span> All red Asterix fields are required.</span>
		<input type='hidden' name='key' value='<?=$_REQUEST['key']?>' />
		<input type='hidden' name='id' value='<?=$info['ID']?>' />
	</div>

	</form>	
<?
	include($BF. "includes/bottom.php");
?>
