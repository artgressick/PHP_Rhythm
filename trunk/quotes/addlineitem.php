<?
	$BF = "../"; #This is the BASE FOLDER.
	$AT = "site"; #This is the AUTH TYPE.  This sets which component of the site you are using. Check the _lib for valid options.
	require($BF. '_lib.php');

	if(!isset($_REQUEST['key'])) {
		 errorPage('Invalid WorkOrder');
	} else {
	
		$q = "SELECT ID, chrWorkOrder, dDefaultUnitPrice FROM WorkOrders WHERE !bDeleted AND chrKEY='".$_REQUEST['key'] ."'";
		
		$workorder = db_query($q,"Does Work Order Exist",1);
		
		if($workorder['ID'] == "") { errorPage('Invalid Work Order'); }
			
	}

	// If a post occured
	if(isset($_POST['txtDescription'])) { // When doing isset, use a required field.  Faster than the php count funtion.

		$table = 'LineItems'; # added so not to forget to change the insert AND audit

		$q = "INSERT INTO ". $table ." SET 
			chrKEY = '". makekey() ."',
			idPerson='". $_POST['idPerson'] ."',
			idWorkOrder='". $workorder['ID'] ."',
			intMiles='". $_POST['intMiles'] ."',
			dbQuantity='". ($_POST['dbQuantity'] == "" ? 0 : $_POST['dbQuantity']) ."',
			dbUnitPrice='". $_POST['dbUnitPrice'] ."',
			dtCreated='".date('Y/m/d',strtotime($_POST['dtDate']))." 12:00:00',
			tBegin='". date('H:i:s',strtotime($_POST['dtBegin'])) ."',
			tEnd='". date('H:i:s',strtotime($_POST['dtEnd'])) ."',
			txtDescription='". encode($_POST['txtDescription']) ."'
		";
		
		# if there database insertion is successful	
		if(db_query($q,"Insert into ". $table)) {

			// This is the code for inserting the Audit Page
			// Type 1 means ADD NEW RECORD, change the TABLE NAME also
			global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
			$newID = mysqli_insert_id($mysqli_connection);
		
			$q = "INSERT INTO Audit SET 
				idType=1, 
				idRecord='". $newID ."',
				txtNewValue='". encode($_POST['txtDescription']) ."',
				dtDateTime=now(),
				chrTableName='". $table ."',
				idPerson='". $_SESSION['idPerson'] ."'
			";
			db_query($q,"Insert audit");
			//End the code for History Insert 
		
			header("Location: workorder.php?key=". $_REQUEST['key']);
			die();
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occured while trying to add the Line Item.');
		}
	}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta http-equiv="REFRESH" CONTENT="1200; URL=logout.asp">
<meta HTTP-EQUIV="Expires" CONTENT="Fri, 26 Mar 1998 23:59:59 GMT">
<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
<meta name="ProgId" content="FrontPage.Editor.Document"><TITLE>techIT - Work Orders Management</TITLE>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function TimeToQuantity () {
	if(document.getElementById('dtBegin').value != "" && document.getElementById('dtEnd').value != "") {
			
		var tBegin = document.getElementById('dtBegin').value;
		var tEnd = document.getElementById('dtEnd').value;
	
		tBegin = tBegin.replace(/:/g,"");
		tEnd = tEnd.replace(/:/g,"");

		var filter = /^\d+$/;
	
		if (filter.test(tBegin) && filter.test(tEnd)) { 

			if(tEnd > tBegin) {
			
				if(tBegin.length == 3) { 
					var tB1 = tBegin.substring(0,1);
					var tB2 = tBegin.substring(1,3);
				} else {
					var tB1 = tBegin.substring(0,2);
					var tB2 = tBegin.substring(2,4);
				} 				

				if(tEnd.length == 3) { 
					var tE1 = tEnd.substring(0,1);
					var tE2 = tEnd.substring(1,3);
				} else {
					var tE1 = tEnd.substring(0,2);
					var tE2 = tEnd.substring(2,4);
				} 				

				
				if(tB2 != 0) {
					tB2 = Math.round((tB2 / 60) * 100);
					if(tB2 < 10) { tB2 = "0" + tB2; }
					tBegin = tB1 + "" + tB2;
				}
	
				if(tE2 != 0) {
					tE2 = Math.round((tE2 / 60) * 100);
					if(tE2 < 10) { tE2 = "0" + tE2; }
					tEnd = tE1 + "" + tE2;
				}

				document.getElementById('dbQuantity').value = Math.round(100*((tEnd - tBegin) / 100))/100;
			}
		}
	}
}

function YY_checkform() { //v4.71
//copyright (c)1998,2002 Yaromat.com
  var a=YY_checkform.arguments,oo=true,v='',s='',err=false,r,o,at,o1,t,i,j,ma,rx,cd,cm,cy,dte,at;
  for (i=1; i<a.length;i=i+4){
    if (a[i+1].charAt(0)=='#'){r=true; a[i+1]=a[i+1].substring(1);}else{r=false}
    o=MM_findObj(a[i].replace(/\[\d+\]/ig,""));
    o1=MM_findObj(a[i+1].replace(/\[\d+\]/ig,""));
    v=o.value;t=a[i+2];
    if (o.type=='text'||o.type=='password'||o.type=='hidden'){
      if (r&&v.length==0){err=true}
      if (v.length>0)
      if (t==1){ //fromto
        ma=a[i+1].split('_');if(isNaN(v)||v<ma[0]/1||v > ma[1]/1){err=true}
      } else if (t==2){
        rx=new RegExp("^[\\w\.=-]+@[\\w\\.-]+\\.[a-zA-Z]{2,4}$");if(!rx.test(v))err=true;
      } else if (t==3){ // date
        ma=a[i+1].split("#");at=v.match(ma[0]);
        if(at){
          cd=(at[ma[1]])?at[ma[1]]:1;cm=at[ma[2]]-1;cy=at[ma[3]];
          dte=new Date(cy,cm,cd);
          if(dte.getFullYear()!=cy||dte.getDate()!=cd||dte.getMonth()!=cm){err=true};
        }else{err=true}
      } else if (t==4){ // time
        ma=a[i+1].split("#");at=v.match(ma[0]);if(!at){err=true}
      } else if (t==5){ // check this 2
            if(o1.length)o1=o1[a[i+1].replace(/(.*\[)|(\].*)/ig,"")];
            if(!o1.checked){err=true}
      } else if (t==6){ // the same
            if(v!=MM_findObj(a[i+1]).value){err=true}
      }
    } else
    if (!o.type&&o.length>0&&o[0].type=='radio'){
          at = a[i].match(/(.*)\[(\d+)\].*/i);
          o2=(o.length>1)?o[at[2]]:o;
      if (t==1&&o2&&o2.checked&&o1&&o1.value.length/1==0){err=true}
      if (t==2){
        oo=false;
        for(j=0;j<o.length;j++){oo=oo||o[j].checked}
        if(!oo){s+='* '+a[i+3]+'\n'}
      }
    } else if (o.type=='checkbox'){
      if((t==1&&o.checked==false)||(t==2&&o.checked&&o1&&o1.value.length/1==0)){err=true}
    } else if (o.type=='select-one'||o.type=='select-multiple'){
      if(t==1&&o.selectedIndex/1==0){err=true}
    }else if (o.type=='textarea'){
      if(v.length<a[i+1]){err=true}
    }
    if (err){s+='* '+a[i+3]+'\n'; err=false}
  }
  if (s!=''){alert('The required information is incomplete or contains errors:\t\t\t\t\t\n\n'+s)}
  document.MM_returnValue = (s=='');
}
//-->
</script>
</head>
<body onLoad="document.form1.dtDate.focus()">
<form method="post" action="" name="form1">
  <div align="center">
    <center>
      <table width="780" border="0" cellspacing="0" cellpadding="5">
        <tr>
          <td colspan="2"><strong><font size="3" face="Arial, Helvetica, sans-serif">Add Line Item to Work Order: <?=$workorder['chrWorkOrder']?></font></strong></td>
        </tr>
        <tr bgcolor="#c0c0c0">
          <td colspan="2"><font size="1" face="Arial, Helvetica, sans-serif">Please enter the information below to add your line item. All information in <font color="#0000FF"><strong>Blue</strong></font> is required. </font></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td width="50%"><font size="2" face="Arial, Helvetica, sans-serif"><strong><font color="#0000FF">Date</font></strong><br>
              <input name="dtDate" type="text" id="dtDate" size="14" maxlength="10" value="<?=date('n/d/Y')?>">
          </font></td>
          <td width="50%"><font size="2" face="Arial, Helvetica, sans-serif"><strong><font color="#0000FF">Engineer</font></strong><br>
						<select id='idPerson' name='idPerson'>
							<option value="">- Please Choose -</option>
<?	$results = db_query("SELECT P.ID, P.chrFirst, P.chrLast FROM SiteAccess JOIN People AS P ON SiteAccess.idPerson=P.ID WHERE !P.bDeleted ORDER BY P.chrLast, P.chrFirst","getting people");

	while($row = mysqli_fetch_assoc($results)) { ?>
							<option<?=$row['ID'] == $_SESSION['idPerson'] ? ' selected="selected"' : ''?> value="<?=$row['ID']?>"><?=$row['chrLast']?>, <?=$row['chrFirst']?></option>
<?	} ?>
						</select>
          </font></td>
        </tr>
        <tr bgcolor="#eeeeee">
          <td colspan="2"><font size="2" face="Arial, Helvetica, sans-serif"><strong>Time Tracking </strong> <font size="1">(This will automatically insert the time in the Quantity Fields)</font> </font></td>
        </tr>
        <tr bgcolor="#eeeeee">
          <td width="50%"><font face="Arial, Helvetica, sans-serif" size="2">Begin Time <font size="1">(Military Time Only)</font><br>
            <input name="dtBegin" type="text" id="dtBegin" size="12" maxlength="10" onchange="TimeToQuantity();" /> 
            </font></td>
          <td width="50%"><font face="Arial, Helvetica, sans-serif" size="2">End Time <font size="1">(Military Time Only)</font><br>
            <input name="dtEnd" type="text" id="dtEnd" size="12" maxlength="10"  onchange="TimeToQuantity();" /> 
          </font></td>
        </tr>
        <tr>
          <td width="50%"><strong><font color="#0000FF" size="2" face="Arial, Helvetica, sans-serif">Quantity</font></strong><font face="Arial, Helvetica, sans-serif" size="2"><br>
              <input type="text" name="dbQuantity" id="dbQuantity" size="10">
          </font></td>
          <td width="50%"><font face="Arial, Helvetica, sans-serif" size="2"><strong><font color="#0000FF">Unit Price</font></strong> <font size="1">(please do not add $, we will automatically add it.)</font><br>
		  	$ <input type="text" name="dbUnitPrice" size="10" value="<?=$workorder['dDefaultUnitPrice']?>">
          </font></td>
        </tr>
        <tr>
          <td width="50%"><font size="2" face="Arial, Helvetica, sans-serif">Travel Mileage</font><font size="1" face="Arial, Helvetica, sans-serif"> (optional) <br>
            <input name="intMiles" type="text" id="intMiles" size="6" maxlength="4">
          miles</font></td>
          <td width="50%"><font size="1" face="Arial, Helvetica, sans-serif">&nbsp;</font></td>
        </tr>
        <tr>
          <td colspan="2"><font face="Arial, Helvetica, sans-serif" size="2">Item Description<br>
              <input name="txtDescription" id="txtDescription" type="text" value="" size="75" maxlength="300">
          </font></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><font face="Arial" size="2">
            <input name="B1" type="submit" onClick="YY_checkform('form1','dtDate','#q','0','Please enter the date for the line item.','dbUnitPrice','#q','0','Please enter the Unit Price.','txtDescription','#q','0','Please enter a description for the line item.','idPerson','#q','1','Please choose an engineer.');return document.MM_returnValue" value="Submit">
            <input type="hidden" name="key" value="<?=$_REQUEST['key']?>">
            <input type="reset" value="Reset" name="B2">
          </font></td>
        </tr>
      </table>
    </center>
  </div>
</form>
</body>
</html>
