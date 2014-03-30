<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{$title|default:'BaBeSK Login'}</title>
<link rel="stylesheet" href="../smarty/templates/web/css/general.css" type="text/css" />
<link rel="shortcut icon" href="webicon.ico" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<div id="login_header">
       <img src="../smarty/templates/web/images/header.png" style="width:600px;" />
    </div>
<div id="login">
    {if $maintenance eq 1}
        <p class="error">Diese Seite wird momentan gewartet.<br/>Bitte versuchen Sie es sp&auml;ter noch einmal.</p>
    {else}
    {if $error != ''}
    <p class="error">{$error}</p>
    {/if}
        <form method="POST" action="index.php">
          <p>Benutzername: <input name="login" type="text" size="30" /></p>
          <p>Passwort: <input name="password" type="password" size="30" /></p>
          <input type="submit" value="Login" />
        </form>
        {if isset($showLoginButton) && $showLoginButton}
        <a href="../publicData/index.php?section=GeneralPublicData|LoginHelp">Hilfe</a>
        {/if}
    {/if}
</div>
<div id="footer">
    <p>BaBeSK {$babesk_version}</p>
</div>
<div id="bachground">
  <img src="../smarty/templates/web/images/background.png" class="stretch" />
</div>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('input[name=login]').keypress(function(event){
      if (event.which == '13') {
        event.preventDefault();
      }
    }
  );
});
</script>
</body>
</html>
