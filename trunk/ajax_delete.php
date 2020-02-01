<?
	require('rhythm-conf.php');

	$connection = @mysql_connect($host, $user, $pass);
	mysql_select_db($db, $connection);
	unset($host, $user, $pass, $db);

	function decode($val,$extra="") {
		$val = str_replace('&quot;','"',$val);
		$val = str_replace("&#39;","'",$val);
		if($extra == "tags") { 
			$val = str_replace('&lt;',"<",$val);
			$val = str_replace("&gt;",'>',$val);
		}
		if($extra == "amp") { 
			$val = str_replace("&amp;",'&',stripslashes($val));
		}
		return $val;
	}

	
	if($_REQUEST['postType'] == "delete") {
		$total = 0;
		$q = "UPDATE ". $_REQUEST['tbl'] ." SET bDeleted=1 WHERE ID=".$_REQUEST['id']." AND chrKEY='".$_REQUEST['chrKEY']."'";
		if(mysql_query($q)) { 
			$total++;
			$q = "INSERT INTO Audit SET idPerson=".$_REQUEST['idPerson'].", idRecord=".$_REQUEST['id'].", chrTableName='". $_REQUEST['tbl'] ."', chrColumnName='bDeleted', dtDatetime=now(), 
					txtOldValue='0', txtNewValue='1', idType=3"; 
			if(mysql_query($q)) { $total += 2; }
		}
  		echo $total;
	} else 	if(@$_REQUEST['postType'] == "permDelete") {
		$total = 0;
		$q = "DELETE FROM ". $_REQUEST['tbl'] ." WHERE ID=".$_REQUEST['id'];
		if(mysql_query($q)) { 
			$total++;
			$q = "INSERT INTO Audit SET idPerson=".$_REQUEST['idPerson'].", idRecord=".$_REQUEST['id'].", chrTableName='". $_REQUEST['tbl'] ."', chrColumnName='', dtDatetime=now(), 
					txtOldValue='', txtNewValue='', idType=4"; 
			if(mysql_query($q)) { $total += 2; }
		}
  		echo $total;
  	} else if($_REQUEST['postType'] == "showhide") {
		$total = 0;
		$q = "UPDATE ". $_REQUEST['tbl'] ." SET bShow='".$_REQUEST['show']."' WHERE ID=".$_REQUEST['id']." AND chrKEY='".$_REQUEST['chrKEY']."'";
		if(mysql_query($q)) { 
			$total++;
			$q = "INSERT INTO Audit SET idPerson=".$_REQUEST['idPerson'].", idRecord=".$_REQUEST['id'].", chrTableName='". $_REQUEST['tbl'] ."', chrColumnName='bShow', dtDatetime=now(), 
					txtOldValue='".$_REQUEST['Old']."', txtNewValue='".$_REQUEST['show']."', idType=2"; 
			if(mysql_query($q)) { $total += 2; }
		}
  		echo $total;
  	} else if($_REQUEST['postType'] == "getPosts") {
		$q = "SELECT txtPost FROM ". $_REQUEST['tbl'] ." WHERE ID=".$_REQUEST['id']." AND chrKEY='".$_REQUEST['chrKEY']."'";
		$tmp = mysql_fetch_assoc(mysql_query($q));
		echo decode($tmp['txtPost']);
  	} else if($_REQUEST['postType'] == "getThreads") {
		$q = "SELECT txtDescription FROM ". $_REQUEST['tbl'] ." WHERE ID=".$_REQUEST['id']." AND chrKEY='".$_REQUEST['chrKEY']."'";
		$tmp = mysql_fetch_assoc(mysql_query($q));
		echo decode($tmp['txtDescription']);
	}


?>
