{include file='web/header.tpl' title='Quiz'}
<div style="float:left">{html_iframe src="headmod_Fits/modules/mod_Quiz/embed.php?uid={$uid}" height=400 width=450}
</div>
<div style="height:400px">
W&auml;hle links das Quiz "F&uuml;hrerschein f&uuml;r IT-Systeme" aus und spiele es durch.
Gib am Ende das angezeigte Kennwort in das untere Feld ein und klicke auf Senden.
<form action="index.php?section=Fits|Quiz" method="post">
    <fieldset>
      <label for="passwd">Kennwort:</label>
      <input type="text" name="fits_key" /><br><br>
       <input type="submit" value="Senden" />
    </fieldset>
</form>
</div>
{include file='web/footer.tpl'}