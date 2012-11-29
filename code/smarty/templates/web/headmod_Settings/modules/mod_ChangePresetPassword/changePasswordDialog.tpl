{include file='web/header.tpl' title='Passwort verändern'}

<div align="center"><h3>Erst-Passwort verändern</h3></div> <br>

<p> Bitte gib ein neues Passwort für deinen Account ein.</p>

<form action="index.php?section=Settings|ChangePresetPassword&action=changePassword" method='POST'>
	<label>neues Passwort: <input type='password' name='newPassword'></label><br>
	<label>Passwort wiederholen: <input type='password' name='newPasswordRepeat'></label><br>
	<input type='submit' value="Passwort ändern">
</form>

{include file='web/footer.tpl'}