<?
	$BF = "";
	$auth_not_required = true;
	require($BF ."_lib.php");
	
	ini_set("auto_detect_line_endings", 1);

	$tmp = array();
	$filename = 'tblUsers';
	$handle = fopen($filename, "r");
	
	$q1 = "";
	$q2 = "";
	$cnt = 0;
	while ($data = fgets($handle)) {
		if($cnt++ == 0) { continue; }
		$data = str_replace('"',"",$data);
		$tmp = explode("|",$data);
		$cnt2 = 0;
		foreach($tmp as $v) {
			if(preg_match("/\s$/",$v,$matches)) {
				$tmp[$cnt2] = rtrim($v);
			}
			$cnt2++;
		}
		
		$q1 .= "('". $tmp[0] ."','".makeKey()."',now(), '". addslashes($tmp[1]) ."@techitsolutions.com', '". sha1($tmp[2]) ."', '". addslashes($tmp[5]) ."', '". addslashes($tmp[6]) ."'),";
		
	}
	
	db_query("INSERT INTO People (ID,chrKEY,dtCreated,chrEmail,chrPassword,chrFirst,chrLast) VALUES ". substr($q1,0,-1),"add people");

	fclose($handle);
?>