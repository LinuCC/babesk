{extends file=$base_path} {block name=content}

<h2 class="module-header">
	Das Passwort der Benutzer zurücksetzen
</h2>

<p>
	<b>Hinweis:</b><br>
	Mit diesem Befehl werden sie alle Passwörter der Benutzer des jetzigen aktiven
	Jahrs ({$activeYearName}) löschen. Wenn sie ein neues Passwort angeben,
	dann werden alle von diesen Schülern sich mit dem neuen Passwort einloggen können,
	ansonsten hat kein Schüler ein Passwort.
</p>

<form action="index.php?section=Kuwasys|Users&action=resetPasswords" method="post">
	<label>Ein neues Passwort angeben:
		<input type="text" name="newPassword"/>
	</label>
	<input type="submit" value="Passwort zurücksetzen"/>
</form>
{/block}