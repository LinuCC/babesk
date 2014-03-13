{include file='web/header.tpl' title='Abwählen von Kursen'}

<h2>Abwählen von Kursen an einem Tag</h2>

<p>Willst du wirklich alle Kurse für den Tag {$unit.translatedName} abwählen?</p>

<form action="index.php?section=Kuwasys|MainMenu&amp;action=cancelClassRegOfDay&amp;unitId={$unit.ID}" method="post">
	<input type="submit" name="cancelConfirmed" value="Ja, alle Kurse abwählen">
</form>
<form action="index.php?section=Kuwasys|MainMenu" method="post">
	<input type="submit" name="cancelConfirmed" value="Nein, die Kurse nicht abwählen">
</form>

{include file='web/footer.tpl'}