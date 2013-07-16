{extends file=$UserParent}{block name=content}

<p>Wenn sie auf den nachfolgenden Button drücken, werden die Sonderzeichen in den Benutzernamen (nicht in den Vor- und Nachnamen!) normalisiert. Z.b. wird
	"é" zu "e"und "î" zu "i". ä,ü,ö und ß lässt das Programm aber unangetastet!</p>

<form action="index.php?module=administrator|System|User|RemoveSpecialCharsFromUsernames" method="post">
	<input type="hidden" name="removeSpecialChars" value="true">
	<input type="submit" value="Benutzernamen normalisieren">
</form>

{/block}
