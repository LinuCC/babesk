{extends file=$inh_path}{block name=content}

<div align="center"><h3>Erst-Passwort verändern</h3></div> <br>

<p> Bitte gib ein neues Passwort für deinen Account ein.</p>

<form action="index.php?section=Settings|ChangePresetPassword&action=changePassword" method='POST'>
	<label>neues Passwort: <input type='password' name='newPassword'></label><br>
	<label>Passwort wiederholen: <input type='password' name='newPasswordRepeat'></label><br>
	{if $onFirstLoginChangeEmail}
	<label>Email-Adresse: {if !$emailChangeForced}(erwünscht){/if}
	 <input type='text' name='newEmail'></label><br>
	{/if}
	<input type='submit' value="Einstellung ändern">
</form>

{/block}