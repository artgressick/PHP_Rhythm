/* Mozilla? */
if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", showHide, false);
}

if (/WebKit/i.test(navigator.userAgent)) { // sniff
    var _timer = setInterval(function() {
        if (/loaded|complete/.test(document.readyState)) {
            showHide(); // call the onload handler
        }
    }, 10);
}

/* for other browsers */
window.onload = showHide;

	
function showHide() {
    if (arguments.callee.done) return;
    arguments.callee.done = true;

	var divs = document.getElementsByTagName("div");
	var i=0;
	while(i < divs.length) {
		if(divs[i].className == "showHideTitle") {
			divs[i].onclick = function() {
				if(document.getElementById(this.id+"box").style.display == "") {
					document.getElementById(this.id+"box").style.display = "none";
				} else {
					document.getElementById(this.id+"box").style.display = "";
				}
			};
		}
		i++;
	}
}	

function showHideLink(val) {
	if(document.getElementById(val).style.display == "") {
		document.getElementById(val).style.display = "none";
	} else {
		document.getElementById(val).style.display = "";
	}
}