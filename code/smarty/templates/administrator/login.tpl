<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>BaBeSK Login</title>
	<link rel="stylesheet" href="{$smarty_path}/templates/administrator/css/general.css" type="text/css" />
</head>

<body onload="x = document.getElementsByTagName('input')[0];if (x.value == '') x.focus();">
<div id="login_header">
    <h1>BaBeSK Administrator Bereich</h1>
</div>
<div id="login">
    {if $status != ''}
       <h4>{$status}</h4>
    {/if}
    <form action="index.php" method="post">
    	<fieldset>
    		<legend>Einloggen</legend>
    		<label>Benutzername</label>
    			<input type="text" name="Username" /><br />
    		<label>Passwort</label>
    			<input type="password" name="Password" /><br />
    	</fieldset>
    	<input type="submit" value="Submit" />
    </form>
</div>

</body>
</html>