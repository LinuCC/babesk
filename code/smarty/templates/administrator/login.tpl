<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>BaBeSK Login</title>
	<link rel="stylesheet" href="../smarty/templates/administrator/css/general.css" type="text/css" />
	<link rel="shortcut icon" href="adminicon.ico" />
</head>

<body onload="x = document.getElementsByTagName('input')[0];if(x)if (x.value == '') x.focus();">
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
<div id="footer">
	<p>BaBeSK {$babesk_version} &copy; 2011 Lessing Gymnasium Uelzen</p>
</div>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('input[name=Username]').keypress(function(event){
			if (event.which == '13') {
				event.preventDefault();
			}
		}
	);
});
</script>

</body>
</html>
