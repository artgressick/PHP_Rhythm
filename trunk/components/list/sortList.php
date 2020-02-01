<?
/* Outputs a column label and image to identify the current "sort" status of a column, with a link to change the current status.  The setting is stored in the session, using the table_name.  To get the current sorted column, use get_sortorder().  $style contains any CSS to attach to the TH, including width.
*/

if(!isset($BF)) { $BF = ""; }

function sortList($label, $column_name, $style='', $morecgi='', $params='') {	
	global $BF;
	$multi = "";
	$tmp = array('');
	if(preg_match('/,/', $_REQUEST['sortCol'])) { 
		$multi = $_REQUEST['sortCol'];
		$tmp = split(',',$_REQUEST['sortCol']);	
	}
	
	if(($_REQUEST['sortCol'] == $column_name) || $tmp[0] == $column_name) {
		
		if($_REQUEST['ordCol'] == 'ASC') {
			$link = '?sortCol=' . $column_name . '&ordCol=DESC' . ($morecgi!=''?'&amp;' . $morecgi:'');	
			$graphic = 'column_sorted_asc.gif';
			$image_alt = 'Ascending order image';
			$link_title = 'Sort by ' . $label . ' in ascending order';
		} else {
			$link = '?sortCol=' . $column_name . '&ordCol=ASC' . ($morecgi!=''?'&amp;' . $morecgi:'');	
			$graphic = 'column_sorted_desc.gif';
			$image_alt = 'Descending order image';
			$link_title = 'Sort by ' . $label . ' in descending order';			
		}
	} else {
		$link = '?sortCol=' . $column_name . '&ordCol=ASC' . ($morecgi!=''?'&amp;' . $morecgi:'');
		$graphic = '';
		$image_alt = 'Currently not sorted by this column';
		$link_title = 'Sort by ' . $label . ' in ascending order';
	}
?>
	<th style='<?=$style?>' onclick='location.href="<?=$_SERVER['PHP_SELF']?><?=$link?>";' class='<?=(($_REQUEST['sortCol'] == $column_name) || ($tmp[0] == $column_name) ? 'ListHeadSortOn' : '')?>' <?=$params?>>
						<a class='<?$_REQUEST['sortCol']==$column_name ? 'current' : ''?>' title='<?=$link_title?>' href='<?=$_SERVER['PHP_SELF']?><?=$link?>'><?=$label?></a>&nbsp;
	
<? if($graphic!='') { ?>
						<img src='<?=$BF?>components/list/<?=$graphic?>' alt='<?=$image_alt?>' style='padding-top: 2px;' />
<?	} ?>
		</th>
<?
}
	if(!isset($_REQUEST['ordCol'])) { $_REQUEST['ordCol'] = "ASC"; }

/* This is the Listing section, all CSS that affect Listing pages go in the area.*/
?>
<style type='text/css'>

.List { border: 1px solid #999; padding: 0; margin: 0; }
.List th { font-size: 10px; background: url(<?=$BF?>components/list/list_head.gif) repeat-x; height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; text-align: left; white-space: nowrap; }
.List td { padding: 0 5px; font-size: 11px; cursor: pointer; }
.List th a { color: #333; text-decoration: none; }
.List td a { color: black; text-decoration: none; }
.List th.ListHeadSortOn { font-size: 10px; background: url(<?=$BF?>components/list/list_head_sortedby.gif); height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; }
.List .ListLineOdd { font-size: 10px; background-color: #FFF; line-height: 20px; height: 20px; padding-left: 5px; }
.List .ListLineEven { font-size: 10px; background-color: #EEE; line-height: 20px; height: 20px; padding-left: 5px; }
.List .options { width: 0.1in; white-space: nowrap; text-align: right; } 
.List .options a { text-decoration: underline; color: green; } 
.NoResults { text-align:center; height:20px; border: 1px solid #999; border-top:none; vertical-align:middle; line-height: 20px; }

.List_green { border: 1px solid #ADC687; padding: 0; margin: 0; }
.List_green th { font-size: 10px; background: url(<?=$BF?>components/list/list_head.gif) repeat-x; height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; text-align: left; white-space: nowrap; }
.List_green td { padding: 0 5px; font-size: 11px; cursor: pointer; }
.List_green th a { color: #333; text-decoration: none; }
.List_green td a { color: black; text-decoration: none; }
.List_green th.ListHeadSortOn { font-size: 10px; background: url(<?=$BF?>components/list/list_head_sortedby.gif); height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; }
.List_green .ListLineOdd { font-size: 10px; background-color: #FFF; line-height: 20px; height: 20px; padding-left: 5px; }
.List_green .ListLineEven { font-size: 10px; background-color: #D7F1C4; line-height: 20px; height: 20px; padding-left: 5px; }
.List_green .options { width: 0.1in; white-space: nowrap; text-align: right; } 
.List_green .options a { text-decoration: underline; color: green; } 

.List_blue { border: 1px solid #5EB0E5; padding: 0; margin: 0; }
.List_blue th { font-size: 10px; background: url(<?=$BF?>components/list/list_head.gif) repeat-x; height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; text-align: left; white-space: nowrap; }
.List_blue td { padding: 0 5px; font-size: 11px; cursor: pointer; }
.List_blue th a { color: #333; text-decoration: none; }
.List_blue td a { color: black; text-decoration: none; }
.List_blue th.ListHeadSortOn { font-size: 10px; background: url(<?=$BF?>components/list/list_head_sortedby.gif); height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; }
.List_blue .ListLineOdd { font-size: 10px; background-color: #FFF; line-height: 20px; height: 20px; padding-left: 5px; }
.List_blue .ListLineEven { font-size: 10px; background-color: #CDE5F6; line-height: 20px; height: 20px; padding-left: 5px; }
.List_blue .options { width: 0.1in; white-space: nowrap; text-align: right; } 
.List_blue .options a { text-decoration: underline; color: green; } 



</style>
<script type='text/javascript'>
var highlightTmp = "";
function RowHighlight(row) {
	highlightTmp = (document.getElementById(row).style.backgroundColor != "" ? document.getElementById(row).style.backgroundColor : '');
	document.getElementById(row).style.backgroundColor = '#AFCCFF';
}
function RowHighlight_green(row) {
	highlightTmp = (document.getElementById(row).style.backgroundColor != "" ? document.getElementById(row).style.backgroundColor : '');
	document.getElementById(row).style.backgroundColor = '#ADC687';
}
function RowHighlight_blue(row) {
	highlightTmp = (document.getElementById(row).style.backgroundColor != "" ? document.getElementById(row).style.backgroundColor : '');
	document.getElementById(row).style.backgroundColor = '#5EB0E5';
}

function UnRowHighlight(row) {
	document.getElementById(row).style.backgroundColor = (highlightTmp == '' ? '' : highlightTmp);
}
// This function re-paints the list tables
function repaint() {
	var menuitems = document.getElementById('List').getElementsByTagName("tr");
	var j = 0;
	for (var i=1; i<menuitems.length; i++) {
		if(menuitems[i].style.display != "none") {
			((j%2) == 0 ? menuitems[i].className = "ListLineEven" : menuitems[i].className = "ListLineOdd");
			j += 1;
		}		
	}
}
function repaint_green() {
	var menuitems = document.getElementById('List_green').getElementsByTagName("tr");
	var j = 0;
	for (var i=1; i<menuitems.length; i++) {
		if(menuitems[i].style.display != "none") {
			((j%2) == 0 ? menuitems[i].className = "ListLineEven" : menuitems[i].className = "ListLineOdd");
			j += 1;
		}		
	}
}
function repaint_blue() {
	var menuitems = document.getElementById('List_blue').getElementsByTagName("tr");
	var j = 0;
	for (var i=1; i<menuitems.length; i++) {
		if(menuitems[i].style.display != "none") {
			((j%2) == 0 ? menuitems[i].className = "ListLineEven" : menuitems[i].className = "ListLineOdd");
			j += 1;
		}		
	}
}

</script>
