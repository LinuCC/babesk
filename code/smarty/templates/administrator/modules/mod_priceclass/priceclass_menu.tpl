{extends file=$base_path}{block name=content}
<form align="center" action='index.php?section=priceclass&action=1' method='post'>
	<input type='submit' value='Erstellen einer neuen Preisklasse' />
</form><br>
<form align="center" action='index.php?section=priceclass&action=2' method='post'>
	<input type='submit' value='Die Preisklassen anzeigen und bearbeiten' />
</form><br>

{/block}