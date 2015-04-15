{extends file=$inh_path}{block name=content}

<div align="center"><h3>Email verändern</h3></div>

<div class="col-md-8 col-md-offset-2">
	<p>Hier kannst du deine Emailadresse verändern. Deine jetzige lautet:
		<br><b>"{$emailOld}"</b><br>
		Wie soll deine neue lauten?<p>
	<form action="index.php?section=Settings|ChangeEmail&amp;action=changeEmail"
		method='post' role="form">
		<div class="form-group">
			<label for="new-email">Neue Email-Adresse</label>
			<input id="new-email" class="form-control" type="text" name="emailNew"
				placeholder="Email eingeben...">
		</div>
		<input type="submit" class="btn btn-primary"
			value="Email-Adresse verändern">
	</form>
</div>
{/block}