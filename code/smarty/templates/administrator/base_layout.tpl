<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{block name=title}BaBeSK{/block}</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<link rel="stylesheet"
	href="../smarty/templates/administrator/css/general.css"
	type="text/css" />

</head>

<body
	onload="x = document.getElementsByTagName('input')[0];if(x)if (x.value == '') x.focus();">
	{block name=header}
	<div id="header">
		<div id="top">
			<h3>
				<a href="index.php?{$sid}">BaBeSK Admin Bereich</a>
			</h3>
			<p>Sie sind eingeloggt als {$username}</p>
			<a href="index.php?action=logout">Ausloggen</a>
		</div>
	</div>
	{/block}
	<div id="main">
		<div id="content">{block name=content}{/block}</div>
	</div>
	<div id="footer">
		<p>{block name=signature}BaBeSK LeG Uelzen{/block}</p>
		{block name=footer}{/block}
		{block name=links}<br /><br /><a style="font-size:120%" href="index.php">Zur&uuml;ck zum Hauptmen&uuml;</a>{/block}
	</div>
</body>
</html>
