{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Hauptmenü der Kursverwaltung</h2>

<form action='index.php?section=Kuwasys|Classes&action=addClass' method='post'>
	<input type='submit' value='einen neuen Kurs hinzufügen'>
</form>
<form action='index.php?section=Kuwasys|Classes&action=csvImport' method='post'>
	<input type='submit' value='Kurse per CSV-Datei importieren'>
</form>
<form action='index.php?section=Kuwasys|Classes&action=showClass' method='post'>
	<input type='submit' value='Die Kurse anzeigen'>
</form>

{/block}