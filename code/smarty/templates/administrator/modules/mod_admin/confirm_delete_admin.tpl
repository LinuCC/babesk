{extends file=$base_path}{block name=content}
<p align="center">Wollen sie den Administrator {$name} wirklich löschen?</p>
<form align="center" action="index.php?section=admin&action=5&ID={$ID}" method="post">
	<input type="submit" value="Ja, ich möchte Den Administrator löschen" name="delete">
</form>
<form align="center" action="index.php?section=admin" method="post">
	<input type="submit" value="Nein, ich möchte den Administrator NICHT löschen" name="not_delete">
</form>
{/block}