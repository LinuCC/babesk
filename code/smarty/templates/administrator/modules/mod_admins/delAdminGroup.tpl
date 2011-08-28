<h3>Administrator Gruppe l&ouml;schen</h3>
<p>Bitte w&auml;hlen sie die zu l&ouml;schende Gruppe aus</p>
<form action="index.php?section=admins&action=delAdminGroup&{$sid}" method="post">
	<fieldset>
		<select name="group" size="1">
			{section name=group loop=$admin_groups}
                <option>{$admin_groups[group]}</option>
			{/section}     
        </select>
	</fieldset>
	<input type="submit" value="Submit" />
</form>