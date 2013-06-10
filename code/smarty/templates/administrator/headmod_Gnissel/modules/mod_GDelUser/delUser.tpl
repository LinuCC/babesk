{extends file=$checkoutParent}{block name=content}

<div align="center"><h3>Benutzer l&ouml;schen</h3></div> <br>

<p>{$forename} {$name} ({$class}) l&ouml;schen?</p>

<form action="index.php?section=Gnissel|GDelUser&action=delUser" method='POST'>
	<input type="hidden" name="uid"value="{$uid}">
	<input type='submit' value="L&ouml;schen">
</form>


{/block}