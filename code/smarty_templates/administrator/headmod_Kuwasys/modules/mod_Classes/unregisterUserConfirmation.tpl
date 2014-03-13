{extends file=$inh_path} {block name="content"}

<p>Wollen sie den Benutzer "{$username}" wirklich aus dem Kurs "{$classname}" entfernen?</p>

<form action="index.php?section=Kuwasys|Classes&amp;action=unregisterUser&amp;jointId={$jointId}" method="post">
	<input type="submit" value="Ja" name="unregisterConfirmed">
	<input type="submit" value="Nein, ich möchte den Benutzer nicht löschen" name="unregisterDeclined">
</form>

{/block}