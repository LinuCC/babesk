{extends file=$base_path}{block name=content}
<h2>Wählen sie aus, was sie ausführen möchten</h2>

<form action='index.php?section=help&action=1' method="post">
	<input type='submit' value='Hilfetext anzeigen'>
</form>
<form action='index.php?section=help&action=2' method="post">
	<input type='submit' value='Hilfetext bearbeiten'>
</form>
{/block}