{extends file=$base_path}{block name=content}
<p>Geldaufladung erfolgreich!</p>
<p>Dem Benutzer {$username} wurden {$amount}&euro; gutgeschrieben</p>
<a href="index.php?section=babesk|Recharge&{$sid}">weiter zur n&auml;chsten Geldaufladung</a>
{/block}