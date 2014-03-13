<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{$title|default:'BaBeSK Login'}</title>
<link rel="stylesheet" href="{$path_smarty_tpl}/web/css/general.css" type="text/css" />
<link rel="shortcut icon" href="webicon.ico" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<div id="login_header">
       <img src="{$path_smarty_tpl}/web/images/header.png" style="width:600px;" />
    </div>
<div id="login">
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
</div>
<div id="footer">
    <p>BaBeSK {$babesk_version}</p>
</div>
<div id="background">
  <img src="{$path_smarty_tpl}/web/images/background.png" class="stretch" />
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
