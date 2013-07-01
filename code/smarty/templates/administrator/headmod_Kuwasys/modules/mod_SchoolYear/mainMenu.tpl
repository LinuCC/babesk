
{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Hauptmenü der Schuljahresverwaltung</h2>

<form action='index.php?section=Kuwasys|SchoolYear&action=addSchoolYear' method='post'>
	<input type='submit' value='ein neues Schuljahr hinzufügen'>
</form>
<form action='index.php?section=Kuwasys|SchoolYear&action=showSchoolYear' method='post'>
	<input type='submit' value='Die Schuljahre anzeigen'>
</form>

{/block}
