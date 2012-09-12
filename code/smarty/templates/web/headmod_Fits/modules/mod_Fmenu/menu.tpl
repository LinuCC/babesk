{include file='web/header.tpl' title='Hauptmen&uuml;'}
{if $hasFits}
<div id="order">
    <h3>Fits wurde bestanden!</h3>
</div>

{elseif $showTestlink}
<div id="order">
    <h3><a href="index.php?section=Fits|Quiz">Zum Onlinetest</a></h3>
</div>
{else}
<div id="order">
    Der F&uuml;hrerschein f&uuml;r IT-Systeme wird im 6. Jahrgang erworben und ist f&uuml;r den Zugriff auf die Bibliotheksrechner notwendig!
</div>
{/if}

{include file='web/footer.tpl'}