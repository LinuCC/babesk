{extends file=$schbasSettingsParent}{block name=content}

<form action="index.php?section=Schbas|SchbasSettings&action=editBankAccount" method="post">
	<input type="submit" value="Bankverbindung"><br>
</form><br>
<form action="index.php?section=Schbas|SchbasSettings&action=2" method="post">
	<input type="submit" value="Ausleihgeb&uuml;hren"><br>
</form><br>
<form action="index.php?section=Schbas|SchbasSettings&action=3" method="post">
	<input type="submit" value="Termine"><br>
</form><br>
<form action="index.php?section=Schbas|SchbasSettings&action=7" method="post">
	<input type="submit" value="Formular f&uuml;r Sch&uuml;ler freischalten"><br>
</form><br>
<form action="index.php?section=Schbas|SchbasSettings&action=8" method="post">
	<input type="submit" value="Texte editieren"><br>
</form><br>
<form action="index.php?section=Schbas|SchbasSettings&action=editCoverLetter" method="post">
	<input type="submit" value="Anschreiben editieren"><br>
</form><br>


{/block}