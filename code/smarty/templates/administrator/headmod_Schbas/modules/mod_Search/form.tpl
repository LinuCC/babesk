{extends file=$SearchParent}{block name=content}
<h3>Bitte Suchbegriff eingeben</h3>
<form action="index.php?section=Schbas|Search" method="get">
	<input type='text' name='search'>
	<input type='submit' value='Mit Benutzernamen, Klasse oder Jahrgang suchen'>
</form>
{/block}