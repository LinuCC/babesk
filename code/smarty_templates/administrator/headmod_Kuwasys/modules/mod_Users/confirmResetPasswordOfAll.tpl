{extends file=$base_path} {block name=content}

<h2 class="moduleHeader">
	Wollen sie wirklich die Passwörter der Benutzer aus dem Jahr ({$activeYearName}) zurücksetzen?
</h2>

<form action="index.php?section=Kuwasys|Users&action=resetPasswords" method="post">
	<input style="padding: 20px" type="submit" name="dialogConfirmed" value='Ja, ich möchte die Passwörter zurücksetzen'>
	<input style="padding: 20px" type="submit" name="dialogNotConfirmed" value='Nein, ich möchte die Passwörter nicht zurücksetzen'>
</form>

{/block}