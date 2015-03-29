{extends file=$checkoutParent}{block name=content}

<div align="center"><h3>Benutzer freischalten</h3></div> <br>

<p>{$forename} {$name} ({$class}) freischalten?</p>

<form action="index.php?section=Gnissel|GUnlockUser&action=unlockUser" method='POST'>
	<input type="hidden" name="uid"value="{$uid}">
	<input type='submit' value="Freischalten">
</form>


{/block}