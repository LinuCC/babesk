<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{block name=title}BaBeSK{/block}</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<link rel="stylesheet"
	href="../smarty/templates/administrator/css/general.css"
	type="text/css" />
{block name=html_head}{/block}
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://ajax.cdnjs.com/ajax/libs/json2/20110223/json2.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<script type="text/javascript" language="JavaScript">

$(document).ready(function() {
	$('body').focus();

	$('.errorContainerClose').on('click', function(event) {
		$('#errorContainer').hide('highlight', 500);
	});

	$('.messageContainerClose').on('click', function(event) {
		$('#messageContainer').hide('highlight', 500);
	});
});
</script>
</head>

<body>
	<div id="header">
		<div id="top">
	{nocache}
	{block name=header}
			<h3>
				<a href="index.php?{$sid}">BaBeSK Admin Bereich</a>
			</h3>
			<p>Sie sind eingeloggt als {$_ADMIN_USERNAME}</p>
			<a href="index.php?action=logout">Ausloggen</a>
	{/block}
	{/nocache}
		</div>
	</div>

	<!-- Error-Output, if any exist -->
	{if isset($_userErrorOutput)}
	<div id="errorContainer">
		<ul>
		{foreach $_userErrorOutput as $error}
			<li>
				<p>{$error}</p>
			</li>
		{/foreach}
		</ul>
		<a class="errorContainerClose" href="#">Schließen</a>
	</div>
	{/if}

	<!-- Message-Output, if any exist -->
	{if isset($_userMsgOutput)}
	<div id="messageContainer">
	<ul>
		{foreach $_userMsgOutput as $msg}
			<li><p>{$msg}</p></li>
			<a class="messageContainerClose" href="#">Schließen</a>
		{/foreach}
	</ul>
	</div>
	{/if}

	<div id="main" class="clearfix">
	    <div id="content">{block name=search}{/block}</div>
		<div id="content">{block name=content}{/block}</div>
	</div>
	<div id="footer">
		<p>{block name=signature}<div id="footer">
    <p>BaBeSK {$babesk_version} &copy; 2011 Lessing Gymnasium Uelzen</p>
</div>{/block}</p>
		{block name=footer}{/block}
		{block name=links}<br /><br /><a style="font-size:120%" href="index.php">Zur&uuml;ck zum Hauptmen&uuml;</a>{/block}
	</div>
</body>
</html>
