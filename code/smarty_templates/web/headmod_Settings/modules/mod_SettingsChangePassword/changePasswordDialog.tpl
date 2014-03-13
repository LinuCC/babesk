{extends file=$inh_path}{block name=content}

<div align="center"><h3>Passwort verändern</h3></div> <br>

<p> Bitte gib ein neues Passwort für deinen Account ein.</p>

<form action="index.php?section=Settings|SettingsChangePassword&action=changePassword" method='POST'>
	<label>neues Passwort: <input type='password' name='newPassword'></label><br>
	<label>Passwort wiederholen: <input type='password' name='newPasswordRepeat'></label><br>
	<input type='submit' value="Einstellung ändern">
</form>

{/block}