{extends file=$inh_path} {block name='content'}

<form action='index.php?section=Kuwasys|Grade&action=addGrade' method='post'>
	<input type='submit' value='eine neue Klasse hinzufügen'>
</form>
<form action='index.php?section=Kuwasys|Grade&action=showGrades' method='post'>
	<input type='submit' value='Die Klassen anzeigen'>
</form>

{/block}