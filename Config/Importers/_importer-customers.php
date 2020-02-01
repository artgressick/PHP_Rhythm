<?
	$BF = "";
	$auth_not_required = true;
	require($BF ."_lib.php");
	
	$locales = array();
	$results = db_query("SELECT ID,idCountry,chrLocaleShort FROM Locales","getting locales");
	while($row = mysqli_fetch_assoc($results)) {
		$locales[$row['chrLocaleShort']]['ID'] = $row['ID'];
		$locales[$row['chrLocaleShort']]['idCountry'] = $row['idCountry'];
	}
	
	ini_set("auto_detect_line_endings", 1);

	$tmp = array();
	$filename = 'tblClients';
	$handle = fopen($filename, "r");
	
	$q1 = "";
	$q2 = "";
	$cnt = 0;
	while ($data = fgets($handle)) {
		if($cnt++ == 0) { continue; }
		$tmp = explode("|",str_replace('"',"",$data));
		$cnt2 = 0;
		foreach($tmp as $v) {
			if(preg_match("/\s$/",$v,$matches)) {
				$tmp[$cnt2] = rtrim($v);
			}
			$cnt2++;
		}

		
		$locale = strtoupper($tmp[4]);
		
		if(isset($tmp[6]) && $tmp[6] != "") {
			db_query("INSERT INTO CustomerNumbers SET chrKEY='".makeKey()."',bPrimary=1,idCustomer='". $tmp[0] ."',idType='2',dtCreated=now(),chrCustomerNumber='".$tmp[6]."'","adding office phone");
		}
		if(isset($tmp[7]) && $tmp[7] != "") {
			db_query("INSERT INTO CustomerNumbers SET chrKEY='".makeKey()."',idCustomer='". $tmp[0] ."',idType='6',dtCreated=now(),chrCustomerNumber='".$tmp[7]."'","adding fax phone");
		}
		
		$q1 .= "('". $tmp[0] ."','".makeKey()."',now(),'". addslashes($tmp[1]) ."','". addslashes($tmp[8]) ."','". addslashes($tmp[9]) ."'),";
		$q2 .= "('".makeKey()."','1','". $tmp[0] ."','". $locales[$locale]['ID'] ."','". $locales[$locale]['idCountry'] ."','1',now(),'". addslashes($tmp[2]) ."','". addslashes($tmp[3]) ."','". addslashes($tmp[5]) ."'),";
		
	}
	
	db_query("INSERT INTO Customers (ID,chrKEY,dtCreated,chrCustomer,chrCPerson,chrCEmail) VALUES ". substr($q1,0,-1),"insert customer");
	db_query("INSERT INTO CustomerAddresses (chrKEY,bPrimary,idCustomer,idLocale,idCountry,idCustomerAddressType,dtCreated,chrAddress1,chrCity,chrPostalCode) VALUES ". substr($q2,0,-1),"Insert addresses");

	fclose($handle);
?>