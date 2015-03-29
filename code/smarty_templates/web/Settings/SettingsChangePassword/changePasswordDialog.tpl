{extends file=$inh_path}{block name=content}

<div align="center"><h3>Passwort verändern</h3></div> <br>

<p> Bitte gib ein neues Passwort für deinen Account ein.</p>

<form action="index.php?section=Settings|SettingsChangePassword&action=changePassword" method='POST' role="form">
	<div class="form-group">
		<label for="password">Neues Passwort</label>
		<input id="password" class="form-control" type="password"
			name="newPassword">
	</div>
	<div class="form-group">
		<label for="password-repeat">Neues Passwort wiederholen</label>
		<input id="password-repeat" class="form-control" type="password"
			name="newPasswordRepeat">
	</div>
	<input class="btn btn-primary" type='submit' value="Einstellung ändern">
</form>

{/block}