{extends file=$base_path}{block name=content}
<p align="center">Wollen sie die Administratorgruppe {$name} wirklich löschen?</p>
<form align="center" action="index.php?section=admin&action=6&ID={$ID}" method="post">
	<input type="submit" value="Ja, ich möchte Die Gruppe löschen" name="delete">
</form>
<form align="center" action="index.php?section=admin" method="post">
	<input type="submit" value="Nein, ich möchte die Gruppe NICHT löschen" name="not_delete">
</form>
{/block}