<?php
//-----------------------------------------------------------------------------------------------
// New script designed by Jason Summers
// ** Auto Key Inserter.  This was made to automatically populate the tables in a database with
// 		Keys from Daniel Tisza-Nitsch's makekey() function..
//	
//	To use change the file located on line 11 to the correct conf file.
//	and comment out the die() statement on line 13
//-----------------------------------------------------------------------------------------------

	require('rhythm-conf.php');  // Changes this with the project and rem out the next line
	
	die();  // added for security, disable to run.

	$db_name = $db; // Sets the database name to be used later
	$connection = mysql_connect($host, $user, $pass);
	mysql_select_db($db, $connection);
	unset($host, $user, $pass, $db);


//-----------------------------------------------------------------------------------------------
// New Function designed by Daniel Tisza-Nitsch
// ** Random key generator.  This was make a rediculously secure key to search for values on.
//-----------------------------------------------------------------------------------------------
function makekey(){
    $tm_start = array_sum(explode(' ', microtime()));            # Starts the microtime for extreme exact values
    $i = 0;
    $pass = "";
    while($i < 4) {                                                # run through this loop 4 times for a 16 char string
        $num = str_shuffle("0123456789");                          # All digits
        $lower = str_shuffle("abcdefghijklmnopqrstuvwxyz");        # All lower case letters
        $upper = str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ");        # All upper case letters
        $random = str_shuffle("~!@#$%^&*()_-=+{[}];:,<.>/?");      # lots of random characters in there too
        # This takes the shuffled values, and grabs one of each, adds the current date and time.. and the
        #   total run time of the script
        $pass .= $num[mt_rand(0, 9)].$upper[mt_rand(0, 25)].$random[mt_rand(0, 26)].$lower[mt_rand(0, 25)];
        $i++;
    }
    $secs_total = array_sum(explode(' ', microtime())) - $tm_start;
    return sha1($pass.date('Y-m-dH:m:s').$secs_total);
}


// Start script
	$tables = mysql_query("SHOW TABLES"); // Calling all tables

	while($table = mysql_fetch_assoc($tables)) {
	
		$tbl = mysql_query("DESCRIBE ".$table['Tables_in_'.$db_name]." 'chrKEY'");

		if(mysql_num_rows($tbl) > 0) {
			
			$records = mysql_query("SELECT ID FROM ".$table['Tables_in_'.$db_name]." WHERE chrKEY = '' ORDER BY ID");
			$count=0;
			while($row = mysql_fetch_assoc($records)) {
				if(mysql_query("UPDATE ".$table['Tables_in_'.$db_name]." SET chrKEY='".makekey()."' WHERE ID=".$row['ID'])) {
					$count++;
				}
			}
			echo $count." records updated with keys on table ".$table['Tables_in_'.$db_name]."<br />";
		}
	}
?>