{extends file=$inh_path}
{block name='content'}

<style type='text/css'  media='all'>
div.moduleFormulars {
	width:400px;
	margin:0 auto;
}

.moduleFormulars input{
	float:right;
}

fieldset {
	border: 1px solid #000000;
}
</style>

<h2 class="module-header">Hauptmenü der Emaileinstellungen</h2>

<div class="moduleFormulars">
<form action='index.php?section=System|EmailConfiguration&action=changeData' method='post'>
	<label>Host:<input type='text' name='host' value={$host}></label><br><br>
	<label>Absendername:<input type='text' name='fromName' value={$fromName}></label><br><br>
	<label>Absender:<input type='text' name='from' value={$from}></label><br><br>
	<fieldset>
		<legend>SMTP-Anmeldedaten</legend>
	<label>Benutzername:<input type='text' name='username' value={$username}></label><br><br>
	<label>Passwort:<input type='password' name='password' value={$password}></label><br><br>
	</fieldset><br>
	<input type="submit" name="Einstellung verändern" style="float:none">
</form>
</div>
{/block}