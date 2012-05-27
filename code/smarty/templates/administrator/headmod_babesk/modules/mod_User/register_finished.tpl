{extends file=$UserParent}{block name=content}
Der Benutzer {$forename} {$name} wurde hinzugefügt. <br><br>
<form action="index.php?section=babesk|User&action=1" method="post">
	<input type ="submit" value="einen weiteren Benutzer hinzufügen">
</form>
<form action="index.php" method="post">
	<input type ="submit" value="zurück zum Admin-Bereich">
</form>

{/block}