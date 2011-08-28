<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{$title|default:'BaBeSK Login'}</title>
<link rel="stylesheet" href="{$smarty_path}/templates/web/css/general.css" type="text/css" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<div id="login_header">
       <img src="{$smarty_path}/templates/web/images/header.png" style="width:600px;" />
    </div>
<div id="login">
    {if $error != ''}
    {$error}
    {/if}
        <form method="POST" action="index.php">
          <p>Login: <input name="login" type="text" size="30" /></p>
          <p>Passwort: <input name="password" type="password" size="30" /></p>
          <input type="submit" value="Login" />
        </form>
</div>
<div id="footer">
    <p>&copy; 2011 Lessing Gymnasium Uelzen</p>
</div>
<div id="bachground">
  <img src="{$smarty_path}/templates/web/images/background.png" class="stretch" />
</div>
</body>
</html>