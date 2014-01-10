{extends file=$inh_path}{block name=content}

<form action="index.php?module=administrator|System|User|ResetAllUserPasswords"
	method="post">
	<p>
		Hier können sie die Passwörter aller Nutzer zu den voreingestellten Passwort zurücksetzen.
		{if $presetPassword}
		Sie werden Passwörter aller Benutzer (außer Benutzer mit ID=1, welcher normalerweise der SuperAdmin ist) auf dieses Passwort zurücksetzen. Dies gilt somit wahrscheinlich auch für ihr Passwort! Falls sie sich nach dem Zurücksetzen also nicht mehr einloggen können, probieren sie dieses Passwort aus.

		<div>
			<a href="http://localhost/babesk/code/administrator/index.php?module=administrator|System|PresetPassword">Klicken sie hier</a> um das voreingestelltes Passwort zu verändern.
		</div>
		<fieldset class="blockyField">
			<legend>
				<b>!!!WICHTIG!!!</b>
			</legend>
			Ändern sie nach dem Zurücksetzen manuell für alle wichtigen Accounts (diejenigen, die Zugriff auf den Administratorbereich haben) die Passwörter, da sonst jeder sich mit dem Passwort einloggen kann!
		</fieldset>

		{else}
		Momentan ist kein voreingestelltes Passwort gesetzt. Bitte
		<a href="http://localhost/babesk/code/administrator/index.php?module=administrator|System|PresetPassword">klicken sie hier</a> um ein voreingestelltes Passwort zu setzen.
		{/if}
	</p>

	<input class="isolated" type="submit" name="resetConfirmed" value="Ja, ich möchte die Passwörter zurücksetzen!" />
</form>
{/block}