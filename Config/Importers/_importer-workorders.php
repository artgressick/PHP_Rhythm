<?
	$BF = "";
	$auth_not_required = true;
	require($BF ."_lib.php");
	
	ini_set("auto_detect_line_endings", 1);

	$locales = array();
	$results = db_query("SELECT ID,idCountry,chrLocaleShort FROM Locales","getting locales");
	while($row = mysqli_fetch_assoc($results)) {
		$locales[$row['chrLocaleShort']]['ID'] = $row['ID'];
		$locales[$row['chrLocaleShort']]['idCountry'] = $row['idCountry'];
	}

	$tmp = array();
	$filename = 'tblWorkOrders';
	$handle = fopen($filename, "r");
	
	$q1 = "";
	$q2 = "";
	$cnt = 0;
	$data = fgets($handle);
	while ($data = fgets($handle)) {
		if(preg_match("/^\d+\|/",$data,$matches)) {
			if($cnt++ > 0) { 
				$q2 .= "'),";
			}
			# Since there are so many records, after every 50 records, do a DB insert for both the Quote and the WO.
			if(($cnt % 50) == 0 && $cnt != 0) {
				db_query("INSERT INTO Quotes (ID,chrKEY,idCustomer,idStatus,dtCreated,chrQuote) VALUES ". substr($q1,0,-1),"add Quote");
				db_query("INSERT INTO WorkOrders (ID,chrKEY,idQuote,idPerson,idStatus,dtCreated,chrWorkOrder,txtNotes) VALUES ". substr($q2,0,-1),"add workorders");
				$q1 = "";
				$q2 = "";
			}

			$data = str_replace('"',"",$data);
			$tmp = explode("|",$data);
			$cnt2 = 0;
			# Needed to take the items that were split out and find if there were a ton of spaces after them...
			foreach($tmp as $v) {
				# If there ARE and spaces after the last actual letter ... 
				if(preg_match("/\s$/",$v,$matches)) {
					# Trim/Strip all extra white spaces at the end.
					$tmp[$cnt2] = rtrim($v);
				}
				$cnt2++;
			}
			
			$q1 .= "('". $tmp[0] ."','".makeKey()."','". $tmp[3] ."','". $tmp[4] ."','". substr($tmp[1],0,19) ."', '". addslashes($tmp[5]) ."-". $tmp[0] ."'),";
			$q2 .= "('". $tmp[0] ."','".makeKey()."','". $tmp[0] ."','". $tmp[2] ."','". $tmp[4] ."','". substr($tmp[1],0,19) ."', '". addslashes($tmp[5]) ."-". $tmp[0] ."','". addslashes(str_replace("\n"," ",$tmp[6]));
			
		} else {
			if($cnt != 0) {
				$tmp2 = str_replace('"',"",$data);
				$tmp2 = rtrim($tmp2);
				$q2 .= " ".addslashes(str_replace("\n"," ",$tmp2));
			}
		}
	}
	
	db_query("INSERT INTO Quotes (ID,chrKEY,idCustomer,idStatus,dtCreated,chrQuote) VALUES ". substr($q1,0,-1),"add Quote");
	if($cnt++ > 0) { $q2 .= "'),"; }
	db_query("INSERT INTO WorkOrders (ID,chrKEY,idQuote,idPerson,idStatus,dtCreated,chrWorkOrder,txtNotes) VALUES ". substr($q2,0,-1),"add workorders");

	fclose($handle);
?>