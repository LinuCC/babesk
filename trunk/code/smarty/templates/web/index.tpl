<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{$title|default:'BaBeSK Login'}</title>
</head>
<body>
<p>
{if $error != ''}
<br>
{$error}
<br>
{/if}
</p>
    <form method="POST" action="index.php">
      <p>Login: <input name="login" type="text" size="30" ></p>
      <p>Passwort: <input name="password" type="password" size="30" ></p>
      <input type="submit" value="Login">
    </form>
{include file='web/footer.tpl'}