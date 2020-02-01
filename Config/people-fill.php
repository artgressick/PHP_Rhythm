<?php
	$BF = "../"; #This is the BASE FOLDER.
	$auth_not_required = true;
	require($BF. '_lib.php');

	
	
	$results = db_query("SELECT ID,
		  (SELECT count(ID) FROM PeopleAddresses WHERE PeopleAddresses.idPerson=People.ID limit 1) as addyid,
		  (SELECT count(ID) FROM PeopleEmails WHERE PeopleEmails.idPerson=People.ID limit 1) as emailid,
		  (SELECT count(ID) FROM PeopleIms WHERE PeopleIms.idPerson=People.ID limit 1) as imid,
		  (SELECT count(ID) FROM PeopleNumbers WHERE PeopleNumbers.idPerson=People.ID limit 1) as numberid
		FROM People",'getting ppl');

	while($row = mysqli_fetch_assoc($results)) {
		if($row['addyid'] == 0) { db_query("INSERT INTO PeopleAddresses SET dtCreated=now(),bPrimary=1,idPerson=". $row['ID'],"insert addy id"); }
		if($row['emailid'] == 0) { db_query("INSERT INTO PeopleEmails SET dtCreated=now(),bPrimary=1,idPerson=". $row['ID'],"insert email id"); }
		if($row['imid'] == 0) { db_query("INSERT INTO PeopleIms SET dtCreated=now(),bPrimary=1,idPerson=". $row['ID'],"insert im id"); }
		if($row['numberid'] == 0) { db_query("INSERT INTO PeopleNumbers SET dtCreated=now(),bPrimary=1,idPerson=". $row['ID'],"insert number id"); }
	}


?>