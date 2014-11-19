{extends file=$base_path}{block name=content}

<div align="center"><h3>Ersatzkarte ausstellen</h3></div> <br>

<p> Bitte scanne die neue Karte f&uuml;r {$forename} {$name} ({$class}) ein.</p>

<form action="index.php?section=Gnissel|GChangeCard&action=changeCard" method='POST'>
	<label for="newCard">Neue Karte: <input type='text' name='newCard'></label><br>
	<input type="hidden" name="uid" value="{$uid}">
	<input type='submit' value="Karte ändern">
</form>

<script type="text/javascript">
$(document).ready(function() {
	$('input[name=newCard]').focus();
});
</script>

{/block}
