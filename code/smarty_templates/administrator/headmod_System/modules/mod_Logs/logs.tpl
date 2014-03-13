{extends file=$logsParent}{block name=content}
<h3>Logs</h3>
<p>Bitte W&auml;hlen Sie:</p>
<a href="index.php?section=System|Logs&action=show&{$sid}">Logs Anzeigen</a><br />
<a href="index.php?section=System|Logs&action=delete&{$sid}">Logs L&ouml;schen</a>


{/block}