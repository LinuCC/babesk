	{extends file=$soliParent}{block name=content}
	<!-- the initial menu-->
	
	<fieldset>
	<legend><b>Gutscheinverwaltung</b></legend>
	<form action="index.php?section=soli&action=1" method="post">
		<input type="submit" value="Ein neuen Coupon für einen Benutzer hinzufügen." />
	</form>
	<form action="index.php?section=soli&action=2" method="post">
		<input type="submit" value="Gutscheine Anzeigen" />
	</form>
	</fieldset>
	<fieldset>
	<legend><b>Benutzer</b></legend>
	<form action="index.php?section=soli&action=3" method="post">
		<input type="submit" value="Soli-Benutzer anzeigen" />
	</form>
	<form action="index.php?section=soli&action=4" method="post">
		<input type="submit" value="Soli-Bestellungen anzeigen" />
	</form>
	<form action="index.php?section=soli&action=6" method="post">
		<input type="submit" value="Bestellungen eines Benutzers für eine Bestimmte Woche anzeigen" />
	</form>
	</fieldset>
	<br><br>
	{/block}