<?
	require('rhythm-conf.php');

	$connection = @mysql_connect($host, $user, $pass);
	mysql_select_db($db, $connection);
	unset($host, $user, $pass, $db);

	if(@$_POST['postType'] == "insert") {
		
		echo $q = "INSERT INTO Contacts SET 
			chrKEY='". makekey() ."',
			idPerson='". $_POST['idPerson'] ."',
			idCustomer='". $_POST['idCustomer'] ."',
			dtCreated=now()";
		mysql_query($q);

	} else if(@$_REQUEST['postType'] == "delete") {

		echo $q = "DELETE FROM Contacts WHERE idPerson='". $_REQUEST['idPerson'] ."' AND idCustomer='". $_REQUEST['idCustomer'] ."'";
		mysql_query($q);
	
	}

function makekey() {
	$email = (isset($_SESSION['chrEmail']) ? $_SESSION['chrEmail'] : 'unknown@emailadsa.com');
    return sha1(uniqid(mt_rand(1000000,9999999).$email.time(), true));
}
?>