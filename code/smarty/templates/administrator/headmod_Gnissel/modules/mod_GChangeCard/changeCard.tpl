{extends file=$checkoutParent}{block name=content}

<div align="center"><h3>Ersatzkarte ausstellen</h3></div> <br>

<p> Bitte scanne die neue Karte f&uuml;r {$forename} {$name} ({$class}) ein.</p>

<form action="index.php?section=Gnissel|GChangeCard&action=changeCard" method='POST'>
	<label for="newCard">Neue Karte: <input type='text' name='newCard'></label><br>
	<input type="hidden" name="uid" value="{$uid}">
	<input type='submit' value="Karte ändern">
</form>


{/block}