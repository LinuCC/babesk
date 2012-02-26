	{extends file=$base_path}{block name=content}
	<!-- the initial menu-->
	
	<form action="index.php?section=soli&action=1" method="post">
		<input type="submit" value="Die Bestellungen mit Teilhabepaket anzeigen" />
	</form>
	<form action="index.php?section=soli&action=2" method="post">
		<input type="submit" value="Gutscheinverwaltung" />
	</form>
	<form action="index.php?section=soli&action=3" method="post">
		<input type="submit" value="Einstellungen" />
	</form>
	<br><br>
	{/block}