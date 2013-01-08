{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Das voreingestellte Passwort ändern</h2>

<p>
Hier können sie das Passwort verändern, dass Schüler bekommen, bevor sie sich zum ersten mal eingeloggt haben. Außerdem können sie verändern, ob die Schüler beim ersten mal einloggen ihr Passwort verändern müssen.
Das voreingestellte Passwort wird nur bei neu importierten Schülern übernommen.
Um für bereits bestehende Schüler das Passwort zurückzusetzen, gehen sie bitte auf
"Passwörter zurücksetzen" <b> (noch nicht implementiert!) </b>
</p>

<form action='index.php?section=System|PresetPassword&action=changePassword&webRedirect=1' method='post'>

	<label>Passwort: <input type='password' name='newPassword'></label><br><br>
	<label>Neueingabe des Passworts beim ersten Login:
		<input type='checkbox' name='firstLoginPassword'
		{if $onFirstLoginChangePassword}checked='checked'{/if}>
	</label><br>
	<label>Neueingabe der Email beim ersten Login:
		<input type='checkbox' name='firstLoginEmail'
		{if $onFirstLoginChangeEmail}checked='checked'{/if}>
	</label><br>
	<label>Email muss eingegeben werden:
		<input type='checkbox' name='firstLoginEmailForce'
		{if $onFirstLoginChangeEmail}checked='checked'{/if}>
	</label><br>
	<input type='submit' value='Einstellungen verändern'>
</form>

{/block}