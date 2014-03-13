{extends file=$inh_path} {block name='content'}

<form action='index.php?module=administrator|Kuwasys|Grade|AddGrade' method='post'>
	<input type='submit' value='eine neue Klasse hinzufügen'>
</form>
<form action='index.php?module=administrator|Kuwasys|Grade|ShowGrades' method='post'>
	<input type='submit' value='Die Klassen anzeigen'>
</form>

{/block}
