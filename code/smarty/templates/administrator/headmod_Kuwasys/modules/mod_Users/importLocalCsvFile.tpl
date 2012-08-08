{extends $inh_path} {block name="content"}

<h2 class="moduleHeader">Schüler per CSV-Datei importieren</h2>

<form action="index.php?section=Kuwasys|Users&action=csvImport" enctype="multipart/form-data" method="post">
	<label>Bitte wählen sie die CSV-Datei aus: <input type="file" name="csvFile"></label>
	<input type="submit" value="Hochladen">
</form>

{/block}