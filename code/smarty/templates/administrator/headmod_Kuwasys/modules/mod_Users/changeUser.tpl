{extends file=$inh_path} {block name='content'}

<script type="text/javascript" src="../smarty/templates/administrator/headmod_Kuwasys/modules/mod_Users/showUsers.js">
</script>

<style type='text/css'  media='all'>
fieldset {
	border: 1px solid #000000;
}
</style>

<h2 class='moduleHeader'>Einen Benutzer verändern</h2>

<form action='index.php?section=Kuwasys|Users&action=changeUser&ID={$user.ID}' method='post'>
	<label>Vorname: <input type='text' value="{$user.forename}" name='forename'></label><br><br>
	<label>Name: <input type='text' value="{$user.name}" name='name'></label><br><br>
	<label>Benutzername: <input type='text' value="{$user.username}" name='username'></label><br><br>
	<!-- Hide Passwords with JavaScript, make a button for show/hide -->
	<div id='showPw'><button type='button' onclick='displayChangePassword()'>Passwort verändern</button></div>
	<div id='hidePw' hidden><button type='button' onclick='hideChangePassword()'>Passwort doch nicht verändern</button></div>
	<fieldset id='pwF' hidden>
	<label>Passwort: <input id='pw' type='password' name='password'></label><br><br>
	<label>Passwort wiederholen: <input id='pwRep' type='password' name='passwordRepeat'></label>
	</fieldset><br><br>
	<!--  -->
	<label>Email-Adresse: <input type='text' value="{$user.email}" name='email'></label><br><br>
	<label>Telefonnummer: <input type='text' value="{$user.telephone}" name='telephone'></label><br><br>
	<input type='submit' value='Absenden'>
</form>

{/block}