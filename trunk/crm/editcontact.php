<?php
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');


	$info = db_query("SELECT People.chrKEY,People.ID,People.chrFirst,People.chrLast,People.chrEmail,PeopleAddresses.ID as idPersonAddress,
		  PeopleAddresses.idLocale,PeopleAddresses.idCountry,PeopleAddresses.chrCity,PeopleAddresses.chrPostalCode,
		    PeopleAddresses.chrAddress1,PeopleAddresses.chrAddress2,PeopleAddresses.chrAddress3,PeopleAddresses.idAddressType,
		  PeopleNumbers.idPhoneType,PeopleNumbers.chrPersonNumber,PeopleNumbers.ID as idPersonNumber,
		  PeopleEmails.chrPersonEmail, PeopleEmails.ID as idPersonEmail,
		  PeopleIms.chrPersonIm, PeopleIms.idImType,PeopleIms.ID as idPersonIm
		FROM People 
		JOIN PeopleAddresses ON PeopleAddresses.idPerson=People.ID AND PeopleAddresses.bPrimary
		JOIN PeopleNumbers ON PeopleNumbers.idPerson=People.ID AND PeopleNumbers.bPrimary
		JOIN PeopleEmails ON PeopleEmails.idPerson=People.ID AND PeopleEmails.bPrimary
		JOIN PeopleIms ON PeopleIms.idPerson=People.ID AND PeopleIms.bPrimary
		WHERE !People.bDeleted AND People.chrKEY='". $_REQUEST['key'] ."'
	","getting people info",1);
	
	if(isset($_POST['chrFirst'])) { // When doing isset, use a required field.  Faster than the php count funtion.
	
		
		// Set the basic values to be used.
		//   $table = the table that you will be connecting to to check / make the changes
		//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
		$table = 'People';
		$mysqlStr = '';
		$audit = '';

		// "List" is a way for php to split up an array that is coming back.  
		// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
		//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
		//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
		//    ...  This also will ONLY add changes to the audit table if the values are different.
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrFirst',$info['chrFirst'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrLast',$info['chrLast'],$audit,$table,$info['ID']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrEmail',$info['chrEmail'],$audit,$table,$info['ID']);
			
		// if nothing has changed, don't do anything.  Otherwise update / audit.
		if($mysqlStr != '') { list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['ID']); }



		$table = 'PeopleAddresses';
		$mysqlStr = '';
		$audit = '';

		list($mysqlStr,$audit) = set_strs($mysqlStr,'idLocale',$info['idLocale'],$audit,$table,$info['idPersonAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idCountry',$info['idCountry'],$audit,$table,$info['idPersonAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idAddressType',$info['idAddressType'],$audit,$table,$info['idPersonAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrAddress1',$info['chrAddress1'],$audit,$table,$info['idPersonAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrAddress2',$info['chrAddress2'],$audit,$table,$info['idPersonAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrAddress3',$info['chrAddress3'],$audit,$table,$info['idPersonAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrCity',$info['chrCity'],$audit,$table,$info['idPersonAddress']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrPostalCode',$info['chrPostalCode'],$audit,$table,$info['idPersonAddress']);
		if($mysqlStr != '') { list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['idPersonAddress']); }


		$table = 'PeopleNumbers';
		$mysqlStr = '';
		$audit = '';

		list($mysqlStr,$audit) = set_strs($mysqlStr,'idPhoneType',$info['idPhoneType'],$audit,$table,$info['idPersonNumber']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrPersonNumber',$info['chrPersonNumber'],$audit,$table,$info['idPersonNumber']);
		if($mysqlStr != '') { list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['idPersonNumber']); }



		$table = 'PeopleIms';
		$mysqlStr = '';
		$audit = '';

		list($mysqlStr,$audit) = set_strs($mysqlStr,'chrPersonIm',$info['chrPersonIm'],$audit,$table,$info['idPersonIm']);
		list($mysqlStr,$audit) = set_strs($mysqlStr,'idImType',$info['idImType'],$audit,$table,$info['idPersonIm']);
		if($mysqlStr != '') { list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['idPersonIm']); }


		
		db_query("DELETE FROM PeopleNumbers WHERE !bPrimary AND idPerson=". $info['ID'],"deleting old phones");
		if($_POST['intPhones'] > 1 || $_POST['chrPersonNumber1'] != "") {
			$q = "";
			$i = 1;
			while($i <= $_POST['intPhones']) {
				if($_POST['chrPersonNumber'.$i] != "") {
					$q .= "('". makekey() ."',0,'". $info['ID'] ."','". $_POST['idPhoneType'.$i] ."',now(),'".encode($_POST['chrPersonNumber'.$i]) ."'),";
				}
				$i++;
			}
			if($q != "") {
				db_query("INSERT INTO PeopleNumbers (chrKEY,bPrimary,idPerson,idPhoneType,dtCreated,chrPersonNumber) VALUES ".substr($q,0,-1),"inserting phones");
			}
		}
			

		db_query("DELETE FROM PeopleAddresses WHERE !bPrimary AND idPerson=". $info['ID'],"deleting old addresses");
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
				db_query("INSERT INTO PeopleAddresses (chrKEY,bPrimary,idPerson,idLocale,idCountry,
				  idAddressType,dtCreated,chrAddress1,chrAddress2,chrAddress3,chrCity,chrPostalCode) VALUES ".substr($q,0,-1)
				,"inserting addresses");
			}
		}

		db_query("DELETE FROM PeopleEmails WHERE !bPrimary AND idPerson=". $info['ID'],"deleting old emails");
		if($_POST['intEmails'] > 1 || $_POST['chrPersonEmail1'] != "") {
			$q = "";
			$i = 1;
			while($i <= $_POST['intEmails']) {
				if($_POST['chrPersonEmail'.$i] != "") {
					$q .= "('". makekey() ."',0,'". $info['ID'] ."',now(),'".encode($_POST['chrPersonEmail'.$i]) ."'),";
				}
				$i++;
			}
			if($q != "") {
				db_query("INSERT INTO PeopleEmails (chrKEY,bPrimary,idPerson,dtCreated,chrPersonEmail) VALUES ".substr($q,0,-1),"inserting phones");
			}
		}

		db_query("DELETE FROM PeopleIms WHERE !bPrimary AND idPerson=". $info['ID'],"deleting old ims");
		if($_POST['intEmails'] > 1 || $_POST['chrPersonIm1'] != "") {
			$q = "";
			$i = 1;
			while($i <= $_POST['intIms']) {
				if($_POST['chrPersonIm'.$i] != "") {
					$q .= "('". makekey() ."',0,'". $info['ID'] ."','". $_POST['idImType'.$i] ."',now(),'".encode($_POST['chrPersonIm'.$i]) ."'),";
				}
				$i++;
			}
			if($q != "") {
				db_query("INSERT INTO PeopleIms (chrKEY,bPrimary,idPerson,idImType,dtCreated,chrPersonIm) VALUES ".substr($q,0,-1),"inserting im");
			}
		}


		
		header("Location: contacts.php");
		die();	
	}
	
		
	$ptresults = db_query("SELECT ID,chrPhoneType FROM PhoneTypes WHERE !bDeleted ORDER BY intOrder,chrPhoneType","phone Types");
	$phoneTypes = "<select name='idPhoneType' id='idPhoneType'><option value=''>-Select Phone Types-</option>";
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
	
	$title = 'Edit Person';
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
		if(errEmpty('chrEmail', "You must enter an Email Address")) { totalErrors++; }
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
	$banner_title = "Edit Person"; // Title of this page. (REQUIRED)
	$banner_icon = "icons-company.png"; // Icon for this page, Size MUST be 40x40 pixels. (NOT REQUIRED)
	$banner_xtra = $info['chrFirst']." ".$info['chrLast']; // Extra information for Page. (NOT REQUIRED)
	$banner_instructions = 'Please edit all the fields and press the "Update Information" button to edit the information for this person.'; // Instructions or description. (NOT REQUIRED)

	include($BF .'includes/left_crm.php');
?>
	<form action="" method="post" id="idForm" onsubmit="return error_check()">
	

	<div class='marginbottom10'>
		<input type='submit' value='Update Information' /><span style='color: red;'>*</span> All red Asterix fields are required.</span>
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
						<div class='FormField'><input type="text" name="chrFirst" id="chrFirst" style='width: 200px;' maxlength="200" value='<?=$info["chrFirst"]?>' /></div>

						<div class='FormName'><span class='red'>*</span> Last Name</div>
						<div class='FormField'><input type="text" name="chrLast" id="chrLast" style='width: 200px;' maxlength="200" value='<?=$info["chrLast"]?>' /></div>
					</div>
	
					<div><img src="../images/cap_bottom-270-blue.gif" /></div>
				</div>





				<div class='bluebox'>
					<table cellspacing="0" cellpadding="0" class='bluetop270'>
						<tr>
							<td class='colorBoxIcon'><img src='<?=$BF?>images/nano-people2.gif' alt='people2' /></td>
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
								<td><input type='text' name='chrPersonIm' id='chrPersonIm' size='13' value='<?=$info['chrPersonIm']?>' /></td>
								<td><?=str_replace("value='".$info['idImType']."'","value='".$info['idImType']."' selected",$imTypes)?></td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT ID,idImType,chrPersonIm FROM PeopleIms WHERE !bDeleted AND idPerson='". $info['ID'] ."' AND !bPrimary ORDER BY ID","getting ims"); ?>
					<div class='colorBoxPadding270' id='moreIms' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;' id='imlist'>
							<tr>
								<td colspan='2' class='FormName'>Additional IM(s)</td>
							</tr>
<?	$i = 1;
	if(mysqli_num_rows($results)) {
		while($row = mysqli_fetch_assoc($results)) {
			$tmpIms = str_replace('idImType','idImType'.$i,$imTypes);
			$tmpIms = str_replace("value='".$row['idImType']."'","value='".$row['idImType']."' selected='selected'",$tmpIms);
?>
							<tr>
								<td><input type='text' name='chrPersonIm<?=$i?>' id='chrPersonIm<?=$i?>' size='13' value='<?=$row["chrPersonIm"]?>' /></td>
								<td><?=$tmpIms?></td>
							</tr>
<?			$i++;
		}
	} else { ?>
							<tr>
								<td><input type='text' name='chrPersonIm1' id='chrPersonIm1' size='13' /></td>
								<td><?=str_replace('idImType','idImType1',$imTypes)?></td>
							</tr>	
<?	} ?>	
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
								<td><input type='text' name='chrEmail' id='chrEmail' size='30' value='<?=$info["chrEmail"]?>' /></td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT * FROM PeopleEmails WHERE !bDeleted AND idPerson='". $info['ID'] ."' AND !bPrimary ORDER BY ID","getting Email"); ?>
					<div class='colorBoxPadding270' id='moreEmails' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;' id='emaillist'>
							<tr>
								<td class='FormName'>Additional Email(s)</td>
							</tr>
<?	$i = 1;
	if(mysqli_num_rows($results)) {
		while($row = mysqli_fetch_assoc($results)) {?>
							<tr>
								<td><input type='text' name='chrPersonEmail<?=$i?>' id='chrPersonEmail<?=$i?>' size='30' value='<?=$row["chrPersonEmail"]?>' /></td>
							</tr>
<?			$i++;
		}
	} else { ?>
							<tr>
								<td><input type='text' name='chrPersonEmail1' id='chrPersonEmail1' size='30' /></td>
							</tr>	
<?	} ?>	
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
								<td><input type='text' name='chrPersonNumber' id='chrPersonNumber' size='13' value='<?=$info["chrPersonNumber"]?>' /></td>
								<td><?=str_replace("value='".$info['idPhoneType']."'","value='".$info['idPhoneType']."' selected",$phoneTypes)?></td>
							</tr>
						</table>
					</div>

<?	$results = db_query("SELECT * FROM PeopleNumbers WHERE !bDeleted AND idPerson='". $info['ID'] ."' AND !bPrimary ORDER BY ID","getting Numbers"); ?>
					<div class='colorBoxPadding270' id='moreNumbers' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px;' id='phonelist'>
							<tr>
								<td class='FormName'>Additional Number(s)</td>
							</tr>
<?	$i = 1;
	if(mysqli_num_rows($results)) {
		while($row = mysqli_fetch_assoc($results)) {
			$tmpPhones = str_replace('idPhoneType','idPhoneType'.$i,$phoneTypes);
			$tmpPhones = str_replace("value='".$row['idPhoneType']."'","value='".$row['idPhoneType']."' selected='selected'",$tmpPhones);
?>
							<tr>
								<td><input type='text' name='chrPersonNumber<?=$i?>' id='chrPersonNumber<?=$i?>' size='13' value='<?=$row["chrPersonNumber"]?>' /></td>
								<td><?=$tmpPhones?></td>
							</tr>
<?			$i++;
		}
	} else { ?>
							<tr>
								<td><input type='text' name='chrPersonNumber1' id='chrPersonNumber1' size='13' /></td>
								<td><?=str_replace('idPhoneType','idPhoneType1',$phoneTypes)?></td>
							</tr>	
<?	} ?>	
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
									<?=str_replace("value='".$info['idAddressType']."'","value='".$info['idAddressType']."' selected='selected'",$custAddrTypes)?>
								</td>

							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Street</span></div>
									<input type='text' name='chrAddress1' id='chrAddress1' size='30' value='<?=$info["chrAddress1"]?>' /><br />
									<input type='text' name='chrAddress2' id='chrAddress2' size='30' value='<?=$info["chrAddress2"]?>' /><br />
									<input type='text' name='chrAddress3' id='chrAddress3' size='30' value='<?=$info["chrAddress3"]?>' />
								</td>
							</tr>
							<tr>
								<td>
									<div class='FormName'><span class='red'>*</span> City</span></div>
									<input type='text' name='chrCity' id='chrCity' size='13' value='<?=$info["chrCity"]?>' />
								</td>
								<td>
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<input type='text' name='chrPostalCode' id='chrPostalCode' size='13' value='<?=$info["chrPostalCode"]?>' />
								</td>

							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Locales</span></div>
									<?=str_replace("value='".$info['idLocale']."'","value='".$info['idLocale']."' selected='selected'",$locales)?>
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Countries</span></div>
									<?=str_replace("value='".$info['idCountry']."'","value='".$info['idCountry']."' selected='selected'",$countries)?>

								</td>
							</tr>
						</table>
					</div>					

<?	$results = db_query("SELECT * FROM PeopleAddresses WHERE !bDeleted AND idPerson='". $info['ID'] ."' AND !bPrimary ORDER BY ID","getting addresses"); ?>
					<div class='colorBoxPadding270' id='moreAddresses' style='display: <?=(mysqli_num_rows($results) ? 'normal' : 'none')?>;'>
						<table cellspacing="0" cellpadding="0" style='width: 250px; margin-left: 5px;' id='addresslist'>
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
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Address Types</div>
									<?=$tmpAddType?>
								</td>

							</tr>
							<tr>
								<td colspan='2'>
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
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<input type='text' name='chrPostalCode<?=$i?>' id='chrPostalCode<?=$i?>' size='10' value='<?=$row['chrPostalCode']?>' />
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Locales</span></div>
									<?=$tmplocales?>
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Countries</span></div>
									<?=$tmpcountries?>

								</td>
							</tr>
							</tbody>
<?			$i++;
		}
	} else { ?>
<tr>
								<td colspan='2'>
									<div class='FormName'><span class='red'>*</span> Address Types</div>
									<?=str_replace('idAddressType','idAddressType1',$custAddrTypes)?>
								</td>

							</tr>
							<tr>
								<td colspan='2'>
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
									<div class='FormName'><span class='red'>*</span> Postal Code</span></div>
									<input type='text' name='chrPostalCode1' id='chrPostalCode1' size='10' />
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


<?	} ?>
						</table>
					</div>					
	
					<div class='marginleft5'><a href='javascript:addAddress()'><img src='<?=$BF?>images/button-addanother.gif' alt='add another' /></a><input type='hidden' name='intAddresses' id='intAddresses' value='1' /></div>
					<div><img src="../images/cap_bottom-270-blue.gif" /></div>
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
