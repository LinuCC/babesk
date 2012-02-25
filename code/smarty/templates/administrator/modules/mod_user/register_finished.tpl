{extends file=$base_path}{block name=content}
Der Benutzer {$forename} {$name} wurde hinzugefügt. <br><br>
<form action="index.php?section=register&next=1" method="post">
	<input type ="submit" value="einen weiteren Benutzer hinzufügen">
</form>
<form action="index.php" method="post">
	<input type ="submit" value="zurück zum Admin-Bereich">
</form>

{/block}