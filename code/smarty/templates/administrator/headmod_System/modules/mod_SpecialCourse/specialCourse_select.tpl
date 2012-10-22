{extends file=$SpecialCourseParent}{block name=content}
<p>Bitte wählen sie aus, was sie tun möchten:</p><br>
<form action="index.php?section=System|SpecialCourse&action=1" method="post">
	<input type="submit" value="Oberstufenkurse editieren">
</form>
<form action="index.php?section=System|SpecialCourse&action=3" method="post">
	<input type="submit" value="Oberstufenkurse den Benutzern zuordnen">
</form>

{/block}