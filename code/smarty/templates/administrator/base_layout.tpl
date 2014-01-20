<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{block name=title}BaBeSK{/block}</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<link rel="stylesheet"
	href="../include/js/jquery-ui-smoothness.css"
	type="text/css"
/>
<script src="../include/js/jquery.min.js"></script>
<script src="../include/js/json2.min.js"></script>
<script src="../include/js/jquery-ui.min.js"></script>
<script src="../include/js/jquery.cookie.js"></script>
<script src="../smarty/templates/administrator/administratorFunctions.js"></script>

{literal}
<script>

	$(document).ready(function(){
		$('#bm').click(function(e) {
			bookmark($(this).find('img').attr("id"));
		});
	});


	function bookmark(mid) {
		$.ajax({
			'type': 'POST',
			'url': 'index.php?module=administrator|System|PersonalBookmarks&action=save',
			data: {
				'mid': mid
			},
			success: function(data) {
				if(data == 'error') {
					alert('Fehler beim Speichern des Lesezeichens!');
				}
				else if(data == 'success') {
					alert('Lesezeichen erfolgreich gespeichert!');
					location.reload();
				}
				else {
					alert(
						'Ein unbekannter Fehler ist aufgetreten!' +
						' (Kein Zugriff zum Modul?)'
					);
				}
			},
			error: function(data) {
				alert('Ein Fehler ist beim Senden der Modul-ID aufgetreten!');
			}
		});
	}
</script>
{/literal}

<link rel="stylesheet"
	href="../smarty/templates/administrator/css/general.css"
	type="text/css" />


{block name=html_head}{/block}
</head>

<body>

	<div id="header">
		<!-- Selector for width of page -->
		<form>
			<div id="pageWidthSelector">
				<input type="radio" id="pageWidthSmall"
					name="pageWidthSelector" checked="checked" />
				<label for="pageWidthSmall">Schmal</label>
				<input type="radio" id="pageWidthMedium"
				name="pageWidthSelector" />
				<label for="pageWidthMedium">Mittel</label>
				<input type="radio" id="pageWidthLarge"
					name="pageWidthSelector" />
				<label for="pageWidthLarge">Breit</label>
				<input type="radio" id="pageWidthVeryLarge"
					name="pageWidthSelector" />
				<label for="pageWidthVeryLarge">Sehr Breit</label>
			</div>
		</form>


		<div id="adminBookmarks">
				{t}Bookmarks{/t}<br/>
				{$flagColors = [0 => 'red', 1 => 'yellow', 2 => 'blue', 3 => 'green']}
				{foreach $bookmarks as $index => $bm}
					<a href="index.php?module={$bm.modulePath}">
						{$col = $index % 4}
						<img src="../images/bookmarks/flag_{$flagColors.$col}.png">
					</a>
				{foreachelse}
					<p>{t}No Bookmarks added{/t}</p>
				{/foreach}
		</div>

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

		<div align="right" id="bm">
		{if !empty($moduleBacklink)}
			<img src="../images/actions/award_star_add.png" id="{$moduleBacklink}">
		{/if}
		</div>

		<div id="content">{block name=search}{/block}</div>

		<div id="content">{block name=content}{/block}</div>
		{if !empty($backlink)}
			<a class="backlink" href="{$backlink}">{t}Back{/t}</a>
		{/if}
		{if !empty($moduleBacklink)}
			<a class="moduleBacklink" href="index.php?module={$moduleBacklink}">{t}Back to the Module{/t}</a>
		{/if}
	</div>
	<div id="footer">
		<p>{block name=signature}<div id="footer">
	<p>BaBeSK {$babesk_version}</p>
</div>{/block}</p>
		{block name=footer}{/block}
		{block name=links}<br /><br /><a style="font-size:120%" href="index.php">Zur&uuml;ck zum Hauptmen&uuml;</a>{/block}
	</div>



<script type="text/javascript" language="JavaScript">
{literal}
$(document).ready(function() {
	$('body').focus();

	$("#pageWidthSelector").buttonset();
	$(document).tooltip();

{/literal}

	/* Error-Output, if any exist */
	{if isset($_userErrorOutput)}
		{foreach $_userErrorOutput as $error}
			adminInterface.errorShow('{AdminInterface::escapeForJs($error)}');
		{/foreach}
	{/if}

	/* Warning-Output, if any exist */
	{if isset($_userWarningOutput)}
		{foreach $_userWarningOutput as $warning}
			adminInterface.warningShow(
				'{AdminInterface::escapeForJs($warning)}');
		{/foreach}
	{/if}

	/* Message-Output, if any exist */
	{if isset($_userMsgOutput)}
		{foreach $_userMsgOutput as $msg}
			adminInterface.messageShow('{AdminInterface::escapeForJs($msg)}');
		{/foreach}
	{/if}

	/* Success-Output, if any exist */
	{if isset($_userSuccessOutput)}
		{foreach $_userSuccessOutput as $msg}
			adminInterface.successShow('{AdminInterface::escapeForJs($msg)}');
		{/foreach}
	{/if}
{literal}

	$('#pageWidthSelector').on('change', function(ev) {
		if($('#pageWidthSmall').prop('checked')) {
			$('#main').animate({'width': 700}, 400);
		}
		if($('#pageWidthMedium').prop('checked')) {
			$('#main').animate({'width': 960}, 400);
		}
		if($('#pageWidthLarge').prop('checked')) {
			$('#main').animate({'width': 1200}, 400);
		}
		if($('#pageWidthVeryLarge').prop('checked')) {
			$('#main').animate({'width': 1800}, 400);
		}
	});
});
{/literal}
</script>

</body>
</html>
