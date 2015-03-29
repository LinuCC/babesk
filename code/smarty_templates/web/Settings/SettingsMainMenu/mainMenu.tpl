{extends file=$inh_path}{block name=content}

<div align="center"><h3>Einstellungsmenü</h3></div>

<p>Hier kannst du deine Einstellungen verändern</p>
<p>Was willst du verändern?</p>

<form action="index.php?section=Settings|ChangeEmail" method="post">
	<input class="btn btn-default" type="submit"
		value="Die Email-adresse verändern">
</form>
<form action="index.php?section=Settings|SettingsChangePassword" method="post">
	<input class="btn btn-default" type="submit" value="Das Passwort verändern">
</form>

{/block}