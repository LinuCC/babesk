{extends file=$logsParent}{block name=content}
<h3>Logs</h3>
<p>Bitte W&auml;hlen Sie:</p>
<a href="index.php?section=logs&action=show&{$sid}">Logs Anzeigen</a><br />
<a href="index.php?section=logs&action=delete&{$sid}">Logs L&ouml;schen</a>


{/block}