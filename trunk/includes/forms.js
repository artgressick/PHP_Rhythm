//dtn:  Set up the Ajax connections
function startAjax() {
	var ajax = false;
	try { 
		ajax = new XMLHttpRequest(); // Firefox, Opera 8.0+, Safari
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

var cssBGcolor='white';
var cssErrColor='#FFDFE6';
function setColorDefault(name) { document.getElementById(name).style.background = cssBGcolor; }
function setColorError(name) { document.getElementById(name).style.background = cssErrColor; }
function reset_errors() { if(document.getElementById('errors').value != '') { document.getElementById('errors').innerHTML = ""; } }
function setErrorMsg(message) { document.getElementById('errors').innerHTML += "<div class='ErrorMessage'>" + message + "</div>"; }





















function errEmpty(name,message) {
	setColorDefault(name);
	if(document.getElementById(name).value == '') {
		setErrorMsg(message); 
		setColorError(name); 
		return 1;
	} 
	return 0;
}

function errNumeric(name,message) {
	setColorDefault(name);
	var filter = /^\d+$/;
	if(!filter.test(document.getElementById(name).value)) {
		setErrorMsg(message); 
		setColorError(name); 
		return 1;
	} 
	return 0;
}


function errEmail(name,type,message) {
	setColorDefault(name);
	switch(type) {
		case '': filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/; break;
		case 'Apple': filter = /^([a-zA-Z0-9_\.\-])+\@(apple.com|euro.apple.com|asia.apple.com)$/; break;
    }
	if(!filter.test(document.getElementById(name).value)) {
		setErrorMsg(message); 
		setColorError(name); 
		return 1;
	} 
	return 0;
}

function errEmailExists(name,message) {
	setColorDefault(name);
	ajax = startAjax();

	if(ajax) {
		ajax.open("GET", "ajax_emailcheck.php?chrEmail="+ document.getElementById(name).value);
	
		ajax.onreadystatechange = function() { 
			if (ajax.readyState == 4 && ajax.status == 200) { 
				if(ajax.responseText == 1) { 
					setErrorMsg(message);
					setColorError(name); 
					return 1;
				}
			} 
		} 
		ajax.send(null); 
	}

	return 0;
}


function errPasswordsMatch(pass1,pass2,message) {
	setColorDefault(pass1);
	setColorDefault(pass2);
	if(document.getElementById(pass1).value != document.getElementById(pass2).value) {
		setErrorMsg(message); 
		setColorError(pass1); 
		setColorError(pass2); 
		return 1;
	} 
	return 0;
}

function errPasswordsEmpty(pass1,pass2,message) {
	setColorDefault(pass1);
	setColorDefault(pass2);
	if(document.getElementById(pass1).value == "" && document.getElementById(pass2).value == "") {
		setErrorMsg(message); 
		setColorError(pass1); 
		setColorError(pass2); 
		return 1;
	} 
	return 0;
}

function errPostalCode(name,country,message) {
	setColorDefault(name);
	var filter = '';
	switch(country) {
		case 'US': filter = /^\d{5}$|^\d{5}-\d{4}$/; break;
		case 'MA': filter = /^\d{5}$/; break;
		case 'CA': filter = /^[A-Za-z]\d[A-Za-z] \d[A-Za-z]\d$/; break;
		case 'DU': filter = /^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/; break;
		case 'FR': filter = /^\d{5}$/; break;
		case 'Monaco':filter = /^(MC-)\d{5}$/; break;
    }
	if(!filter.test(document.getElementById(name).value)) {
		setErrorMsg(message); 
		setColorError(name); 
		return 1;
	} 
	return 0;
}

function errDate(name,country,message) {
	setColorDefault(name);
	var filter = '';
	switch(country) {
		case 'US': filter = /^([0]?[1-9]|[1][012])\/([0]?[1-9]|[12]\d|[3][01])\/\d{4}$/; break;
		case 'CA': filter = /^([0]?[1-9]|[12]\d|[3][01])\/([0]?[1-9]|[1][012])\/\d{4}$/; break;
		case 'UK': filter = /^([0]?[1-9]|[12]\d|[3][01])\/([0]?[1-9]|[1][012])\/\d{4}$/; break;
    }
    
	if(!filter.test(document.getElementById(name).value)) {
		setErrorMsg(message); 
		setColorError(name); 
		return 1;
	} 
	return 0;
}

function errPhone(name,country,message) {
	setColorDefault(name);
	var filter = '';
	switch(country) {
		case 'US': filter = /^(\(?\d{3}\)?[-\s.]?)?\d{3}[-\s.]\d{4}$/; break;
		case 'CA': filter = /^(\(?\d{3}\)?[-\s.]?)?\d{3}[-\s.]\d{4}$/; break;
    }
    
	if(!filter.test(document.getElementById(name).value)) {
		setErrorMsg(message); 
		setColorError(name); 
		return 1;
	} 
	return 0;
}

function errURL(name,message) {
	setColorDefault(name);
	var tmp = document.getElementById(name).value.toLowerCase();
	var filter = /^(((ht|f)tp(s?))\:\/\/)([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(\/\S*)?$/;
	if(!filter.test(tmp)) {
		setErrorMsg(message); 
		setColorError(name); 
		return 1;
	} 
	return 0;
}

function errIP(name,message) {
	setColorDefault(name);
	var filter = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
	if(!filter.test(document.getElementById(name).value)) {
		setErrorMsg(message); 
		setColorError(name); 
		return 1;
	} 
	return 0;
}

function errCustom(name,message,extra) {
	if (extra == 'tinyMCE') {
		tinyMCE.getInstanceById(name).getWin().document.body.style.backgroundColor=cssBGcolor;
		if (tinyMCE.getContent() == '') {
			setErrorMsg(message);
			setColorError(name);
			tinyMCE.getInstanceById(name).getWin().document.body.style.backgroundColor=cssErrColor;
			return 1;
		}
		return 0;
	} else {
		setErrorMsg(message);
	}
}
