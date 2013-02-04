{include file='web/header.tpl' title='Hauptmen&uuml;'}
{if $birthday == $smarty.now|date_format:"%m-%d"}
<img src="../smarty/templates/web/images/birthday.jpg" class="center" /><br>
Fotograf: Will Clayton Lizenz: CC BY 2.0<br>
{/if}
{include file='web/footer.tpl'}