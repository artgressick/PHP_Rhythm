<?
	$BF = "";
	$auth_not_required = true;
	require($BF ."_lib.php");
	
	ini_set("auto_detect_line_endings", 1);

	$tmp = array();
	$filename = 'tblLineItems';
	$handle = fopen($filename, "r");
	
	$q1 = "";
	$q2 = "";
	$cnt = 0;
	$cntSet = 1;
	$data = fgets($handle);
	while ($data = fgets($handle)) {
		$cnt++;
		$data = str_replace('"',"",$data);
		$tmp = explode("|",$data);
		$cnt2 = 0;
		foreach($tmp as $v) {
			if(preg_match("/\s$/",$v,$matches)) {
				$tmp[$cnt2] = rtrim($v);
			}
			$cnt2++;
		}
		
		$q1 .= "('". $tmp[0] ."','".makeKey()."','". $tmp[1] ."','". $tmp[2] ."','". $tmp[3] ."','". ($tmp[4] != "" ? date('H:i:s',strtotime($tmp[4])) : '') ."','". ($tmp[5] != "" ? date('H:i:s',strtotime($tmp[5])) : '') ."','". $tmp[6] ."','". addslashes($tmp[7]) ."','". (isset($tmp[8]) && $tmp[8] != "" ? $tmp[8] : "") ."','". (isset($tmp[9]) && $tmp[9] != "" ? $tmp[9] : "") ."'),";

		if(($cnt % 50) == 0 && $cnt > 0) {
			db_query("INSERT INTO LineItems (ID,chrKEY,idPerson,idWorkOrder,dbQuantity,tBegin,tEnd,dtCreated,txtDescription,dbUnitPrice,intMiles) VALUES ". substr($q1,0,-1),"adding set of 50 #".$cntSet);
			$q1 = "";
			echo $cntSet .") set of 50 added<br />";
			$cntSet++;
		}
	}
	

	fclose($handle);
?>