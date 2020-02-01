<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	// If a post occured
	if(isset($_POST['chrFirst'])) { // When doing isset, use a required field.  Faster than the php count funtion.

		$table = 'People'; # added so not to forget to change the insert AND audit

		$q = "INSERT INTO ". $table ." SET 
			chrKEY = '". makekey() ."',
			chrFirst = '". encode($_POST['chrFirst']) ."',
			chrLast = '". encode($_POST['chrLast']) ."',
			chrEmail = '". encode($_POST['chrPersonEmail']) ."',
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
				txtNewValue='". encode($_POST['chrFirst']." ".$_POST['chrLast']) ."',
				dtDateTime=now(),
				chrTableName='". $table ."',
				idPerson='". $_SESSION['idPerson'] ."'
			";
			db_query($q,"Insert audit");
			//End the code for History Insert 
		
		
			/* All the extra Phone Numbers */
			$q = "INSERT INTO PeopleNumbers (chrKEY,bPrimary,idPerson,idPhoneType,dtCreated,chrPersonNumber) VALUES  
				('". makekey() ."',1,'". $newID ."','". $_POST['idPhoneType'] ."',now(),'". encode($_POST['chrPersonNumber']) ."'),";
			
			if($_POST['intPhones'] > 1 || $_POST['chrPersonNumber1'] != "") {
				$i = 1;
				while($i <= $_POST['intPhones']) {
					if($_POST['chrPersonNumber'.$i] != "") {
						$q .= "('". makekey() ."',0,'". $newID ."','". $_POST['idPhoneType'.$i] ."',now(),'".encode($_POST['chrPersonNumber'.$i]) ."'),";
					}
					$i++;
				}
			}
			db_query(substr($q,0,-1),"inserting phones");
		

			/* All the extra Addresses */
			$q = "INSERT INTO PeopleAddresses (chrKEY,bPrimary,idPerson,idLocale,idCountry,idAddressType,dtCreated,chrAddress1,chrAddress2,chrAddress3,chrCity,chrPostalCode) VALUES  
				('". makekey() ."',1,'". $newID ."','". $_POST['idLocale'] ."','". $_POST['idCountry'] ."','". $_POST['idAddressType'] ."',now(),'". encode($_POST['chrAddress1']) ."','". encode($_POST['chrAddress2']) ."','". encode($_POST['chrAddress3']) ."','". encode($_POST['chrCity']) ."','". encode($_POST['chrPostalCode']) ."'),";
			
			if($_POST['intAddresses'] > 1 || $_POST['chrAddress1-1'] != "") {
				$i = 1;
				while($i <= $_POST['intAddresses']) {
					if($_POST['chrAddress1-'.$i] != "") {
						$q .= "('". makekey() ."',0,'". $newID ."','". $_POST['idLocale'.$i] ."','". $_POST['idCountry'.$i] ."','". encode($_POST['idAddressType'.$i]) ."',now(),'". encode($_POST['chrAddress1-'.$i]) ."','". encode($_POST['chrAddress2-'.$i]) ."','". encode($_POST['chrAddress3-'.$i]) ."','". encode($_POST['chrCity'.$i]) ."','". encode($_POST['chrPostalCode'.$i]) ."'),";
					}
					$i++;
				}
			}
			db_query(substr($q,0,-1),"inserting addresses");

			/* All the IMs */
			$q = "INSERT INTO PeopleIms (chrKEY,bPrimary,idPerson,idImType,dtCreated,chrPersonIm) VALUES  
				('". makekey() ."',1,'". $newID ."','". $_POST['idImType'] ."',now(),'". encode($_POST['chrPersonIm']) ."'),";
			
			if($_POST['intIms'] > 1 || $_POST['chrPersonIm1'] != "") {
				$i = 1;
				while($i <= $_POST['intIms']) {
					if($_POST['chrPersonIm'.$i] != "") {
						$q .= "('". makekey() ."',0,'". $newID ."','". encode($_POST['idImType'.$i]) ."',now(),'". encode($_POST['chrPersonIm'.$i]) ."'),";
					}
					$i++;
				}
			}
			db_query(substr($q,0,-1),"inserting Ims");
		
		
			/* All the Emails */
			$q = "INSERT INTO PeopleEmails (chrKEY,bPrimary,idPerson,dtCreated,chrPersonEmail) VALUES  
				('". makekey() ."',1,'". $newID ."',now(),'". encode($_POST['chrPersonEmail']) ."'),";
			
			if($_POST['intIms'] > 1 || $_POST['chrPersonEmail1'] != "") {
				$i = 1;
				while($i <= $_POST['intEmails']) {
					if($_POST['chrPersonEmail'.$i] != "") {
						$q .= "('". makekey() ."',0,'". $newID ."',now(),'". encode($_POST['chrPersonEmail'.$i]) ."'),";
					}
					$i++;
				}
			}
			db_query(substr($q,0,-1),"inserting Emails");
		
		
		
			$_SESSION['InfoMessage'] = $_POST['chrFirst'] ." ". $_POST['chrLast'] ." has been successfully added to the Database.";
			header("Location: ". $_POST['moveTo']);
			die();
			
			
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occured while trying to add the customer "'. $_POST['chrCustomer'] .'".');
		}
	}
	
	$ptresults = db_query("SELECT ID,chrPhoneType FROM PhoneTypes WHERE !bDeleted ORDER BY intOrder,chrPhoneType","phone Types");
	$phoneTypes = "<select name='idPhoneType' id='idPhoneType' style='width: 140px;'><option value=''>-Select Phone Types-</option>";
	while($row = mysqli_fetch_assoc($ptresults)) {
		$phoneTypes .= "<option value='". $row['ID'] ."'>". $row['chrPhoneType'] ."</option>";
	}
	$phoneTypes .= "</select>";
	
	$imresults = db_query("SELECT ID,chrImType FROM ImTypes WHERE !bDeleted ORDER BY intOrder,chrImType","IM Types");
	$imTypes = "<select name='idImType' id='idImType' style='width: 140px;'><option value=''>-Select IM Types-</option>";
	while($row = mysqli_fetch_assoc($imresults)) {
		$imTypes .= "<option value='". $row['ID'] ."'>". $row['chrImType'] ."</option>";
	}
	$imTypes .= "</select>";

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
				var td2 = document.createElement('td');
				var td3 = document.createElement('td');
	
				var inputph = document.createElement('input');
					inputph.name = 'chrPersonNumber'+(id);
					inputph.size = 13;
					
				var tmpSel = "<?=($phoneTypes)?>".replace(/idPhoneType/g, 'idPhoneType'+id);
				
				var selphtype = document.createElement('div');
					selphtype.name = 'idPhoneType'+(id);
					selphtype.innerHTML = tmpSel;
					
				td2.appendChild(inputph);
				td3.appendChild(selphtype);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tbdy.appendChild(tr);
				table.appendChild(tbdy);
		
				document.getElementById('intPhones').value = id;
			}
		}
	}	

	function addIm() {
		if(document.getElementById('moreIms').style.display == "none") { 
			document.getElementById('moreIms').style.display = "";
		} else {
			var id = (parseInt(document.getElementById('intIms').value) + 1);
			if(!document.getElementById(id)) {
				var table = document.getElementById('imlist');
		
				var tbdy = document.createElement("tbody");
				var tr = document.createElement('tr');
				var td1 = document.createElement('td');
				var td2 = document.createElement('td');
	
				var inputim = document.createElement('input');
					inputim.name = 'chrPersonIm'+(id);
					inputim.size = 13;
					
				var tmpSel = "<?=($imTypes)?>".replace(/idImType/g, 'idImType'+id);
				
				var selphtype = document.createElement('div');
					selphtype.name = 'idImType'+(id);
					selphtype.innerHTML = tmpSel;
					
				td1.appendChild(inputim);
				td2.appendChild(selphtype);
				tr.appendChild(td1);
				tr.appendChild(td2);
				tbdy.appendChild(tr);
				table.appendChild(tbdy);
		
				document.getElementById('intIms').value = id;
			}
		}
	}	

	function addEmail() {
		if(document.getElementById('moreEmails').style.display == "none") { 
			document.getElementById('moreEmails').style.display = "";
		} else {
			var id = (parseInt(document.getElementById('intEmails').value) + 1);
			if(!document.getElementById(id)) {
				var table = document.getElementById('emaillist');
		
				var tbdy = document.createElement("tbody");
				var tr = document.createElement('tr');
				var td = document.createElement('td');
	
				var inputemail = document.createElement('input');
					inputemail.name = 'chrPersonEmail'+(id);
					inputemail.size = 30;
					
				td.appendChild(inputemail);
				tr.appendChild(td);
				tbdy.appendChild(tr);
				table.appendChild(tbdy);
		
				document.getElementById('intEmails').value = id;
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
				var tr5 = document.createElement('tr');

				var td1 = document.createElement('td');
					td1.colSpan = 2;
				var td2 = document.createElement('td');
					td2.colSpan = 2;
				var td3 = document.createElement('td');
				var td4 = document.createElement('td');
					td4.colSpan = 2;
				var td5 = document.createElement('td');
					td5.colSpan = 2;
				var td6 = document.createElement('td');
	
	
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
					inputcity.size = 13;
				var inputpostal = document.createElement('input');
					inputpostal.name = 'chrPostalCode'+(id);
					inputpostal.size = 13;
					
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
				tr3.appendChild(td5);
				tr4.appendChild(td4);
				tr5.appendChild(td6);
				
				tbdy.appendChild(tr1);
				tbdy.appendChild(tr2);
				tbdy.appendChild(tr3);
				tbdy.appendChild(tr4);
				tbdy.appendChild(tr5);
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

		if(errEmpty('chrFirst', "You must enter a First Name")) { totalErrors++; }
		if(errEmpty('chrLast', "You must enter a Last Name")) { totalErrors++; }
		if(errEmpty('chrPersonEmail', "You must enter an Email Address")) { totalErrors++; }
		if(errEmpty('chrPersonNumber', "You must enter an Phone Number")) { totalErrors++; }
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
	$bodyParams = "document.getElementById('chrFirst').focus()";

	$section = 'crm';
	$leftlink = "viewcustomer";
	include($BF .'includes/top.php');
	// Banner Information
	$banner_title = "Add CContact"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-company.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = ""; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please fill in all the fields and press the "Add Another" to add another contact or "Add and Continue" to return to the contacts list.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_crm.php');
?>
	<form action="" method="post" id="idForm" onsubmit="return error_check()">
	

	<div class='marginbottom10'>
	<input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addcustomer.php';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='index.php';" /> <span style='font-size: 10px;'><span style='color: red;'>*</span> All red Asterix fields are required.</span>
	</div>

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
						<div class='FormName'><span class='red'>*</span> First Name</div>
						<div class='FormField'><input type="text" name="chrFirst" id="chrFirst" style='width: 200px;' maxlength="200" /></div>

						<div class='FormName'><span class='red'>*</span> Last Name</div>
						<div class='FormField'><input type="text" name="chrLast" id="chrLast" style='width: 200px;' maxlength="200" /></div>
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
								<td><input type='text' name='chrPersonIm' id='chrPersonIm' size='13' /></td>
								<td> <?=$imTypes?></td>
							</tr>
						</table>
					</div>

					<div class='colorBoxPadding270' id='moreIms' style='display: none;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;' id='imlist'>
							<tr>
								<td colspan='2' class='FormName'>Additional IM(s)</td>
							</tr>
							<tr>
								<td><input type='text' name='chrPersonIm1' id='chrPersonIm1' size='13' /></td>
								<td><?=str_replace('idImType','idImType1',$imTypes)?></td>
							</tr>
						</table>
	

					</div>					
					<div class='marginleft5'><a href='javascript:addIm()'><img src='<?=$BF?>images/button-addanother.gif' alt='add another' /></a><input type='hidden' name='intIms' id='intIms' value='1' /></div>
					<div><img src="../images/cap_bottom-270-blue.gif" /></div>
				</div>


			</td>
			<td width="10"><!-- gutter --></td>
			<td valign="top" width="270">

				<div class='greenbox'>
					<table cellspacing="0" cellpadding="0" class='greentop270'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-email.gif' alt='telephone' /></td>
							<td class='colorBoxTitle'>Contact Email Address(es)</td>
						</tr>
					</table>


					<div class='colorBoxPadding270'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;'>
							<tr>
								<td class='FormName'><span class='red'>*</span> Primary Email</td>
							</tr>
							<tr>
								<td><input type='text' name='chrPersonEmail' id='chrPersonEmail' size='30' /></td>
							</tr>
						</table>
					</div>

					<div class='colorBoxPadding270' id='moreEmails' style='display: none;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;' id='emaillist'>
							<tr>
								<td class='FormName'>Additional Email(s)</td>
							</tr>
							<tr>
								<td><input type='text' name='chrPersonEmail1' id='chrPersonEmail1' size='30' /></td>
							</tr>
						</table>
	

					</div>					
					<div class='marginleft5'><a href='javascript:addEmail()'><img src='<?=$BF?>images/button-addanother.gif' alt='add another' /></a><input type='hidden' name='intEmails' id='intEmails' value='1' /></div>
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
								<td><input type='text' name='chrPersonNumber' id='chrPersonNumber' size='13' /></td>
								<td> <?=$phoneTypes?></td>
							</tr>
						</table>
					</div>

					<div class='colorBoxPadding270' id='moreNumbers' style='display: none;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;' id='phonelist'>
							<tr>
								<td colspan='2' class='FormName'>Additional Number(s)</td>
							</tr>
							<tr>
								<td><input type='text' name='chrPersonNumber1' id='chrPersonNumber1' size='13' /></td>
								<td><?=str_replace('idPhoneType','idPhoneType1',$phoneTypes)?></td>
							</tr>
						</table>
	

					</div>					
					<div class='marginleft5'><a href='javascript:addPhone()'><img src='<?=$BF?>images/button-addanother.gif' alt='add another' /></a><input type='hidden' name='intPhones' id='intPhones' value='1' /></div>
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
									<div class='FormName'><span class='red'>*</span> Address Types</div>
									<?=$custAddrTypes?>
								</td>

							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Street</span></div>
									<input type='text' name='chrAddress1' id='chrAddress1' size='30' /><br />
									<input type='text' name='chrAddress2' id='chrAddress2' size='30' /><br />
									<input type='text' name='chrAddress3' id='chrAddress3' size='30' />
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'><span class='red'>*</span> City</span></div>
									<input type='text' name='chrCity' id='chrCity' size='13' />
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<input type='text' name='chrPostalCode' id='chrPostalCode' size='13' />
								</td>

							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Locales</span></div>
									<?=$locales?>
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Countries</span></div>
									<?=$countries?>

								</td>
							</tr>
						</table>
					</div>					

					<div class='colorBoxPadding270' id='moreAddresses' style='display: none;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px; margin-left: 5px;' id='addresslist'>
							<tr>
								<td colspan='2' class='FormName'>Additional Address(es)</td>
							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Address Types</div>
									<?=str_replace('idAddressType','idAddressType1',$custAddrTypes)?>
								</td>

							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Street</span></div>
									<input type='text' name='chrAddress1-1' id='chrAddress1-1' size='30' /><br />
									<input type='text' name='chrAddress2-1' id='chrAddress2-1' size='30' /><br />
									<input type='text' name='chrAddress3-1' id='chrAddress3-1' size='30' />
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'><span class='red'>*</span> City</span></div>
									<input type='text' name='chrCity1' id='chrCity1' size='13' />
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<input type='text' name='chrPostalCode1' id='chrPostalCode1' size='13' />
								</td>

							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Locales</span></div>
									<?=str_replace('idLocale','idLocale1',$locales)?>
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Countries</span></div>
									<?=str_replace('idCountry','idCountry1',$countries)?>

								</td>
							</tr>
						</table>
					</div>					
	
					<div class='marginleft5'><a href='javascript:addAddress()'><img src='<?=$BF?>images/button-addanother.gif' alt='add another' /></a><input type='hidden' name='intAddresses' id='intAddresses' value='1' /></div>
					<div><img src="../images/cap_bottom-270-blue.gif" /></div>
				</div>




			</td>

		</tr>	
	</table>
	
	<div class='margintop10;'>
	<input type='submit' value='Add Another' onclick="document.getElementById('moveTo').value='addcontact.php';" /> &nbsp;&nbsp; <input type='submit' value='Add and Continue' onclick="document.getElementById('moveTo').value='contacts.php';" /> <span style='font-size: 10px;'><span style='color: red;'>*</span> All red Asterix fields are required.</span>
		<input type='hidden' name='moveTo' id='moveTo' />
	</div>

	</form>	
<?
	include($BF. "includes/bottom.php");
?>
