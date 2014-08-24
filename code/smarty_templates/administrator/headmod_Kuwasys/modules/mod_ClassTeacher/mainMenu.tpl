{extends file=$inh_path} {block name='content'}

<h2 class='module-header'>Hauptmenü der Kursleiterverwaltung</h2>

<form action='index.php?section=Kuwasys|ClassTeacher&action=addClassTeacher' method='post'>
	<input type='submit' value='einen neuen Kursleiter hinzufügen'>
</form>
<form action='index.php?section=Kuwasys|ClassTeacher&action=csvImport' method='post'>
	<input type='submit' value='Kursleiter mithilfe einer Csv hinzufügen'>
</form>
<form action='index.php?section=Kuwasys|ClassTeacher&action=showClassTeacher' method='post'>
	<input type='submit' value='Die Kursleiter anzeigen'>
</form>

{/block}