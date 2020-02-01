function startAjax() {
	var ajax = false;
	try { ajax = new XMLHttpRequest(); // Firefox, Opera 8.0+, Safari
	} catch (e) {
	    // Internet Explorer
	    try { ajax = new ActiveXObject("Msxml2.XMLHTTP");
	    } catch (e) {
			try { ajax = new ActiveXObject("Microsoft.XMLHTTP");
	        } catch (e) {
	        	alert("Your browser does not support AJAX!");
	        }
	    }
	}
	return ajax;
}

function deleteButtonPopups(id,message,chrKEY) {
	return "<span class='deleteImage'><a href=\"javascript:warning("+id+", '"+message+"','"+chrKEY+"');\" title='Delete "+ message +"'><img id='deleteButton"+ id +"' src='../images/button_delete.png' alt='delete button' onmouseover='this.src=\"../images/button_delete_on.png\"' onmouseout='this.src=\"../images/button_delete.png\"' /></a></span>";
}


function repaintmini(fatherTable) {
	var menuitems = window.opener.document.getElementById(fatherTable).getElementsByTagName("tr");
	var j = 0;
	for (var i=1; i<menuitems.length; i++) {
		if(menuitems[i].style.display != "none") {
			((j%2) == 0 ? menuitems[i].className = "ListLineEven" : menuitems[i].className = "ListLineOdd");
			j += 1;
		}		
	}
}

function repaint(fatherTable) {
	var menuitems = document.getElementById(fatherTable).getElementsByTagName("tr");
	var j = 0;
	for (var i=1; i<menuitems.length; i++) {
		if(menuitems[i].style.display != "none") {
			((j%2) == 0 ? menuitems[i].className = "ListLineEven" : menuitems[i].className = "ListLineOdd");
			j += 1;
		}		
	}
}

//dtn: Function added to get rid of the first line in the sort columns if there are no values in the sort table yet.
//		Ex: "There are no People in this table" ... that gets erased and replaced with a real entry
function noRowClear(fatherTable) {
	var val = window.opener.document.getElementById(fatherTable).getElementsByTagName("tr");
	if(val.length <= 2) { 
		if(val[1]) {
			if(val[1].innerHTML.length < 100) {
				var tmp = val[0].innerHTML
				window.opener.document.getElementById(fatherTable).innerHTML = "";
				window.opener.document.getElementById(fatherTable).innerHTML = tmp;
			}
		}
	}
}

function revert() {
	document.getElementById('overlaypage').style.display = "none";
	document.getElementById('warning').style.display = "block";
}

//dtn: This is the warning window.  It sets up the gay overlay background with the window in the middle asking if you are sure you want to deleted whatever.
function warning(id,val1,chrKEY) {
	var height = (document.height > window.innerHeight ? document.height : window.innerHeight);
	document.getElementById('gray').style.height = height + "px";
	document.getElementById('message').style.top = window.pageYOffset+"px";
	
	document.getElementById('delName').innerHTML = val1;
	document.getElementById('idDel').value = id;
	document.getElementById('chrKEY').value = chrKEY;
	document.getElementById('overlaypage').style.display = "block";
}

function showNotice(id,tbl) {
	document.getElementById(tbl + 'tr' + id).style.display = "none";
	
	repaint(tbl);
	revert();
}

function delItem(address,type) {
	var chrKEY = document.getElementById('chrKEY').value;
	var id = document.getElementById('idDel').value;
	var tbl = document.getElementById('table').value;
	ajax = startAjax();
	
	if(ajax) {
		if(type=='customer') {
			ajax.open("GET", address + "&idPerson=" + id);
		} else {
			ajax.open("GET", address + "&idCustomer=" + id);
		}
	
		ajax.onreadystatechange = function() { 
			if(ajax.readyState == 4 && ajax.status == 200) { 
				showNotice(id,tbl);
			} 
		} 
		ajax.send(null); 
	}
} 


function postInfo(url, parameters) {
	ajax = startAjax(); // Call the function to start the Ajax
	
	ajax.open('POST', url, true);
	ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	ajax.setRequestHeader("Content-length", parameters.length);
	ajax.setRequestHeader("Connection", "close");
	ajax.send(parameters);
	
	ajax.onreadystatechange = function() { 
		if (ajax.readyState == 4 && ajax.status == 200) {}
	}
}
