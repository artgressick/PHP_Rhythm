<?
	$BF = ""; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF .'_lib.php');
	
	$q = "SELECT Quotes.ID, Quotes.idCustomer, WorkOrders.idCustomer AS idCustomer2
			FROM Quotes
			JOIN WorkOrders ON WorkOrders.idQuote=Quotes.ID
			GROUP BY Quotes.ID";
			
	$customers = db_query($q,"Grabbing All Customers from WorkOrders");
	
	while ($row = mysqli_fetch_assoc($customers)) {
	
		if($row['idCustomer'] == "" || $row['idCustomer'] == 0) {
			db_query("UPDATE Quotes SET idCustomer=".$row['idCustomer2']." WHERE ID=".$row['ID'],"Updating Quote");
		}
	
	}
?>
			
		