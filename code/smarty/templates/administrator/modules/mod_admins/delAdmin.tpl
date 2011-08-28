<h3>Administrator l&ouml;schen</h3>
<p>Bitte w&auml;hlen sie den zu l&ouml;schenden Administrator aus</p>
<form action="index.php?section=admins&action=delAdmin&{$sid}" method="post">
	<fieldset>
		<select name="adminname" size="1">
			{section name=admin loop=$admins}
                	<option>{$admins[admin]}</option>
			{/section}
        </select>
	</fieldset>
	<input type="submit" value="Submit" />
</form>