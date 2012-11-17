{include file='web/header.tpl' title='Passwort &Auml;ndern'}

<p>Bitte neues Passwort eingeben und zur Sicherheit wiederholen.</p>
<form action="index.php?section=Babesk|ChangePassword" method="post">
    <fieldset>
      <label for="passwd">Neues Passwort:</label>
      <input type="password" name="passwd" /><br><br>
	  <label for="passwd">Neues Passwort wiederholen:</label>
      <input type="password" name="passwd_repeat" /><br><br>
       <input type="submit" value="Best&auml;tigen" />
    </fieldset>
</form>

{include file='web/footer.tpl'}