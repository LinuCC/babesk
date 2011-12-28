<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>BaBeSK</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />


	<link rel="stylesheet" href="../smarty/templates/administrator/css/general.css" type="text/css" />	 
	
	
	<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/effects.js"></script>
<script type="text/javascript" src="../js/stereotabs.js"></script>
    


<script type="text/javascript">
//<![CDATA[
Event.observe(window, 'load', loadTabs, false);
function loadTabs() {
var tabs = new tabset('container'); // name of div to crawl for tabs and panels
tabs.autoActivate($('tab_first')); // name of tab to auto-select if none exists in the url
}
//]]>
</script>
    
</head>

<body onload="x = document.getElementsByTagName('input')[0];if (x.value == '') x.focus();">
<div id="header">
    <div id="top">
        <h3><a href="index.php?{$sid}">BaBeSK Admin Bereich</a></h3>
        <p>Sie sind eingeloggt als {$username}</p>
        <a href="index.php?action=logout">Ausloggen</a>
    </div>
</div>
<div id="main">
<div id="content">

