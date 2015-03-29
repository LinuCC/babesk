{extends file=$checkoutParent}{block name=content}

<div align="center"><h3>Passwort verändern</h3></div> <br>

<p> Bitte gib ein neues Passwort für {$forename} {$name} ({$class}) ein.</p>

<form action="index.php?section=Gnissel|GChangePassword&action=changePassword" method='POST'>
	<label for="newPassword">neues Passwort: <input type='password' name='newPassword'></label><br>
	<label for="newPasswordRepeat">Passwort wiederholen: <input type='password' name='newPasswordRepeat'></label><br>
	<input type="hidden" name="uid"value="{$uid}">
	<input type='submit' value="Einstellung ändern">
</form>


{/block}