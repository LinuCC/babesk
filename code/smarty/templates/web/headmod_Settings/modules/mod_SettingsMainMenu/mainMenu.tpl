{include file='web/header.tpl' title='Email verändern'}

<div align="center"><h3>Einstellungsmenü</h3></div>

<p>Hier kannst du deine Einstellungen verändern</p>
<p>Was willst du verändern?</p>

<form action="index.php?section=Settings|ChangeEmail" method="post">
	<input type="submit" value="Die Email-adresse verändern">
</form>
<form action="index.php?section=Settings|SettingsChangePassword" method="post">
	<input type="submit" value="Das Passwort verändern">
</form>

{include file='web/footer.tpl'}