<?php
// This is needed for the Date Functions
if(phpversion() > '5.0.1') { date_default_timezone_set('America/Los_Angeles'); }

require_once($BF. 'components/JSON.php');
$json = new Services_JSON();

// The configuration file that connects us to the mysql servers
include('rhythm-conf.php');

// set up error reporting
require_once($BF. 'components/ErrorHandling/error_handler.php');
	

if(!isset($host)) {
	error_report("Include database conf failed");
	$connected = false;
} else {
	$connected = true;
	if($mysqli_connection = @mysqli_connect($host, $user, $pass)) {
		if(!@mysqli_select_db($mysqli_connection, $db)) {
			error_report("mysqli_select_db(): " . mysqli_error($mysqli_connection));
		}
	} else {
		error_report("mysqli_connect(): " . mysqli_connect_error($mysqli_connection));
	}
}
// clean up so that these variables aren't exposed through the debug console
unset($host, $user, $pass, $db);

// Set and use the session
session_name('rhythm');
session_start();

// If Logout is set in the URL bar, destroy the session and cookies.
if(isset($_REQUEST['logout'])) {
	setcookie(session_name(), "", 0, "/");
	$_SESSION = array();
	session_unset();
	session_destroy();
	if($_REQUEST['logout'] == 1) {
		header("Location: ".$BF."includes/logout.php");
	} else if($_REQUEST['logout'] == 2) {
		header("Location: ".$BF."customers/includes/logout.php");
	}
	die();
}

# Auth checking.  If "$auth_not_required" is set to true or 1, then no authcheck is even needed!
$auth_not_required = (isset($auth_not_required) && $auth_not_required != "" ? $auth_not_required : false);
if(!$auth_not_required) { auth_check($BF,$AT); }

function maintenance_page() {
?>
	<h1>We're Sorry...</h1>
	<p>Could not connect to the database server.  We could be experiencing trouble, or the site may be down for maintenance.</p>
	<p>You can press the Refresh button to see if the site is available again.</p>
<?
	die();
}

function error_report($message) {
	ob_start();
	print_r(debug_backtrace());
	$trace = ob_get_contents();
	ob_end_clean();

	$emailto = (defined('BUG_REPORT_ADDRESS') ? constant('BUG_REPORT_ADDRESS') : 'bugs@techitsolutions.com');
	mail($emailto, '[Rhythm] Error',
		"- ERROR\n----------------\n" . $message . "\n\n\n- STACK\n----------------\n" . $trace
		);

	maintenance_page();		
}


function db_query($query, $description, $fetch=0, $ignore_warnings=false, $connection=null) {

	global $mysqli_connection, $database_time;
	if($connection == null) {
		$connection = $mysqli_connection;
	}

	$begin_time = microtime(true);
	$result = mysqli_query($connection, $query);
	$end_time = microtime(true);

	$database_time += ($end_time-$begin_time);

	if(!is_bool($result)) {
		$num_rows = mysqli_num_rows($result);
		$str = $num_rows . " rows";
	} else {
		$affected = mysqli_affected_rows($connection);
		$str = $affected . " affected";
	}

	if ($result === false) {
		_error_debug(array('error' => mysqli_error($connection), 'query' => $query), "MySQL ERROR: " . $description, __LINE__, __FILE__, E_ERROR);
	} else {
		
		if(mysqli_warning_count($connection) && !$ignore_warnings) {
			$warnings = mysqli_get_warnings($connection);
			_error_debug(array('query' => $query, 'warnings' => $warnings), "MySQL WARNING(S): " . $description, __LINE__, __FILE__, E_WARNING);
		} else {
			_error_debug(array('query' => $query), "MySQL (" . $str . ", " . (round(($end_time-$begin_time)*1000)/1000) . " sec): " . $description, __LINE__, __FILE__);
		}
	}
	return(($fetch != 0 ? mysqli_fetch_assoc($result) : $result));
}


function auth_check($BF,$AT)
{
	if(strtolower($AT) == 'site') { 
		if(!isset($_SESSION['idPerson'])) {  // if this variable is set, they are already authenticated in this session
			include($BF. 'includes/auth_check_site.php');
		}
	} else if(strtolower($AT) == 'customer') { 
		if(!isset($_SESSION['idCustomer'])) {  // if this variable is set, they are already authenticated in this session
			include($BF. 'customers/includes/auth_check_customer.php');
		}
	}
}

//-----------------------------------------------------------------------------------------------
// New Functions designed by Jason Summers and written by Daniel Tisza-Nitsch
// ** These functions were created to simplify the uploading of information to the database.
//    With these functions, you can send encode/decode all quotes from a given text and ONLY the quotes.
//      This script assumes that you are setting up database tables to accept UTF-8 characters for all 
//		entities.
//-----------------------------------------------------------------------------------------------

function encode($val,$extra="") {
	$val = str_replace("'",'&#39;',stripslashes($val));
	$val = str_replace('"',"&quot;",$val);
	if($extra == "tags") { 
		$val = str_replace("<",'&lt;',stripslashes($val));
		$val = str_replace('>',"&gt;",$val);
	}
	if($extra == "amp") { 
		$val = str_replace("&",'&amp;',stripslashes($val));
	}
	return $val;
}

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


//-----------------------------------------------------------------------------------------------
// New Function designed by Jason Summers
// ** These function was created to call the error page and pass information to it.
//-----------------------------------------------------------------------------------------------
function errorPage($msg) {
	global $BF;
	if(isset($msg)) {$_SESSION['chrErrorMsg'] = $msg;}
	header("Location: ".$BF."error.php");
	die;
}


//-----------------------------------------------------------------------------------------------
// New Function designed by Daniel Tisza-Nitsch
// ** These function was created to simplify the deleting process on the list pages
//-----------------------------------------------------------------------------------------------

function deleteButton($id,$message,$chrKEY) {
	global $BF;
	?>
		<span class='deleteImage'><a href="javascript:warning(<?=$id?>, '<?=str_replace("&","&amp;",$message)?>','<?=$chrKEY?>');" title="Delete <?=$message?>"><img id='deleteButton<?=$id?>' src='<?=$BF?>images/button_delete.png' alt='delete button' onmouseover='this.src="<?=$BF?>images/button_delete_on.png"' onmouseout='this.src="<?=$BF?>images/button_delete.png"' /></a></span>
	<?
}


//-----------------------------------------------------------------------------------------------
// New Function designed by Daniel Tisza-Nitsch
// ** Random key generator.  This was make a rediculously secure key to search for values on.
//-----------------------------------------------------------------------------------------------
function makekey() {
	$email = (isset($_SESSION['chrEmail']) ? $_SESSION['chrEmail'] : 'unknown@emailadsa.com');
    return sha1(uniqid(mt_rand(1000000,9999999).$email.time(), true));
}


//-----------------------------------------------------------------------------------------------
// New Functions designed by Daniel Tisza-Nitsch and Arthur Gressick
// ** These functions were created to simplify the uploading of information to the database.
//    With these functions, you can send information to the database in one single function call
//      to insert or update information, as well as creating an audit trail for tracking.
//-----------------------------------------------------------------------------------------------


// The basic normal set trings function.  This works for almost everything.
function set_strs($str,$field_info,$info_old,$aud,$table,$id) { //This function does the additions to an update script
	$tmpStr = $tmpAud = "";
	if(encode($info_old) != encode($_POST[$field_info]) || strlen($info_old) != strlen(encode($_POST[$field_info]))) 
	{
		$tmpStr = (($str == '' ? '' : ',')." ". $field_info. "='". encode($_POST[$field_info]) ."' ");
		$tmpAud = ((($aud == '' ? '' : ',')." ('". $_SESSION['idPerson'] ."',2,'" . $id . "','". $table ."','". $field_info ."','". encode($info_old) ."','". encode($_POST[$field_info]) ."')"));
	}
	$tmp = array(($str .= $tmpStr),($aud .= $tmpAud));
	return($tmp);
}

// The checkbox functions.  This works for almost everything.
function set_strs_checkbox($str,$field_info,$info_old,$aud,$table,$id) { //This function does the additions to an update script
	$tmpStr = $tmpAud = "";
	$info_old = (($info_old == 1) ? 'on' : '');
	if($info_old != $_POST[$field_info]) {
		$tmpStr = (($str == '' ? '' : ',')." ". $field_info. "='". ($_POST[$field_info] == 'on' ? 1 : 0) ."' ");
	}
	if($info_old != $_POST[$field_info]) {
		$tmpAud = ((($aud == '' ? '' : ',')." ('". $_SESSION['idPerson'] ."',2,'" . $id . "','". $table ."','". $field_info ."','". $info_old ."','". ($_POST[$field_info] == 'on' ? 1 : 0) ."')"));
	}
	$tmp = array(($str .= $tmpStr),($aud .= $tmpAud));
	return($tmp);
}

// Sets the password fields to MD5 hashes and checks against that.  NO AUDIT for security purposes
function set_strs_password($str,$field_info,$info_old,$aud,$table,$id) { //This function does the additions to an update script
	$tmpStr = $tmpAud = "";
	$pwd = sha1($_POST[$field_info]);
	if($info_old != $pwd) {
		$tmpStr = (($str == '' ? '' : ',')." ". $field_info. "='". $pwd ."' ");
	}
	// No audit on the password.
	$tmp = array(($str .= $tmpStr),($aud .= $tmpAud));
	return($tmp);
}

// Sets the strings, but formats the input for Year-Month-Day (yyyy-mm-dd) format
function set_strs_date($str,$field_info,$info_old,$aud,$table,$id, $format='Y-m-d') { //This function does the additions to an update script
	$tmpStr = $tmpAud = "";
	if($info_old != date($format,strtotime($_POST[$field_info]))) {
		$tmpStr = (($str == '' ? '' : ',')." ". $field_info. "='". date($format,strtotime($_POST[$field_info])) ."' ");
	}
	if($info_old != $_POST[$field_info]) {
		$tmpAud = ((($aud == '' ? '' : ',')." ('". $_SESSION['idUser'] ."',2,'" . $id . "','". $table ."','". $field_info ."','". $info_old ."','". $_POST[$field_info] ."')"));
	}
	$tmp = array(($str .= $tmpStr),($aud .= $tmpAud));
	return($tmp);
}

// Sets the strings, but formats the input for Hour:min:sec (23:59:59) format
function set_strs_time($str,$field_info,$info_old,$aud,$table,$id,$format='H:i:s') { //This function does the additions to an update script
	$tmpStr = $tmpAud = "";
	if($info_old != date($format,strtotime($_POST[$field_info]))) {
		$tmpStr = (($str == '' ? '' : ',')." ". $field_info. "='". date($format,strtotime($_POST[$field_info])) ."' ");
	}
	if($info_old != $_POST[$field_info]) {
		$tmpAud = ((($aud == '' ? '' : ',')." ('". $_SESSION['idPerson'] ."',2,'" . $id . "','". $table ."','". $field_info ."','". $info_old ."','". $_POST[$field_info] ."')"));
	}
	$tmp = array(($str .= $tmpStr),($aud .= $tmpAud));
	return($tmp);
}

// Sets the strings, but formats the input for Year-Month-Day Hour:min:sec (yyyy-mm-dd 23:59:59) format
function set_strs_datetime($str,$field_info,$info_old,$aud,$table,$id,$format='Y-m-d H:i:s') { //This function does the additions to an update script
	$tmpStr = $tmpAud = "";
	if($info_old != date($format,strtotime($_POST[$field_info]))) {
		$tmpStr = (($str == '' ? '' : ',')." ". $field_info. "='". date($format,strtotime($_POST[$field_info])) ."' ");
	}
	if($info_old != $_POST[$field_info]) {
		$tmpAud = ((($aud == '' ? '' : ',')." ('". $_SESSION['idPerson'] ."',2,'" . $id . "','". $table ."','". $field_info ."','". $info_old ."','". $_POST[$field_info] ."')"));
	}
	$tmp = array(($str .= $tmpStr),($aud .= $tmpAud));
	return($tmp);
}


// This is the script that does the official uploads into the DB.
function update_record($str, $aud, $table, $id, $error=true) { //This function does the insert into the database for the Audit - Reference the set_audit_str
	if($str != "") {
		$finstr[0] = "UPDATE ". $table ." SET " . $str . "WHERE ID=". $id;
		if(!db_query($finstr[0],"Insert update statement")) {
			if($error) { errorPage('An error has occured while trying to update the "'.$table.'" table.'); }
		}
	}
	if($aud != "") {
		$finstr[1] = "INSERT INTO Audit (idPerson, idType, idRecord, chrTablename, chrColumnName, txtOldValue, txtNewValue) VALUES ". $aud;
		db_query($finstr[1],"Insert audit statement");
	}
	return($finstr);
}


