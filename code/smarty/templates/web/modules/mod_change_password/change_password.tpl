{include file='web/header.tpl' title='Passwort &Auml;ndern'}

<p>Dies ist das erste Mal, dass du dich anmeldest. Zun&auml;chst musst du dein Passwort &auml;ndern</p>
<form action="index.php" method="post">
    <fieldset>
      <label for="passwd">Neues Passwort:</label>
      <input type="password" name="passwd" /><br><br>
	  <label for="passwd">Neues Passwort wiederholen:</label>
      <input type="password" name="passwd_repeat" /><br><br>
       <input type="submit" value="Best&auml;tigen" />
    </fieldset>
</form>

{include file='web/footer.tpl'}