<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{block name=title}BaBeSK{/block}</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<!-- For the various JQuery-UI-Elements this site is using -->
<link rel="stylesheet" href="http://code.jquery.com/ui/
	1.10.3/themes/smoothness/jquery-ui.css" />

<link rel="stylesheet"
	href="../smarty/templates/administrator/css/general.css"
	type="text/css" />


{block name=html_head}{/block}
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://ajax.cdnjs.com/ajax/libs/json2/20110223/json2.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="../smarty/templates/administrator/administratorFunctions.js">
	</script>

{literal}
<script type="text/javascript" language="JavaScript">
$(document).ready(function() {
	$('body').focus();

	{/literal}
	/* Error-Output, if any exist */
	{if isset($_userErrorOutput)}
		{foreach $_userErrorOutput as $error}
			adminInterface.errorShow('{$error}');
		{/foreach}
	{/if}

	/* Message-Output, if any exist */
	{if isset($_userMsgOutput)}
		{foreach $_userMsgOutput as $msg}
			adminInterface.messageShow('{$msg}');
		{/foreach}
	{/if}

	/* Success-Output, if any exist */
	{if isset($_userSuccessOutput)}
		{foreach $_userSuccessOutput as $msg}
			adminInterface.successShow('{$msg}');
		{/foreach}
	{/if}
	{literal}

	adminInterface.messageShow('Bitte überprüfen ob der SQL-Server Transaktionen unterstützt (Tabellen müssen InnoDB als Engine haben, kein MyISAM). Neue Funktionen benutzen diese, um unter anderem Fehler zu finden.');
	adminInterface.successShow('Test');
	adminInterface.successShow('You successfully logged in!');
});
</script>
{/literal}
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

	<div id="main" class="clearfix">
		<noscript>
		<p>
			<b>Ihr Browser hat JavaScript ausgestellt. Diese Seite funktioniert nur dann
			vollständig, wenn sie Javascript aktiviert haben!</b><br />
			Ein Anleitung finden sie
			 <a href="http://www.enable-javascript.com/de/" target="_blank">hier</a>.
		</p>
		<hr />
		</noscript>

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
