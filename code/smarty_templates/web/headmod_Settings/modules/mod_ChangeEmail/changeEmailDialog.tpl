{extends file=$inh_path}{block name=content}

<div align="center"><h3>Email verändern</h3></div>

<p>Hier kannst du deine Emailadresse verändern. Deine jetzige lautet:
	<br><b>"{$emailOld}"</b><br>
	Wie soll deine neue lauten?<p>
<form action="index.php?section=Settings|ChangeEmail&amp;action=changeEmail" method='post'>
	<input type="text" name="emailNew">
	<input type="submit" value="Email-Adresse verändern">
</form>
{/block}