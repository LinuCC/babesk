<form action="index.php?section=admin&action=7&where={$ID}" method="post">
	<label>ID des Admins:<input type="text" value='{$ID}' name='ID'></label><br><br>
	<label>Name des Admins:<input type="text" value='{$name}' name='name'></label><br><br>
	<label>Passwort des Admins:<input type="text" name='password'></label><br><br>
	<select name="admingroup">
	{foreach $groups as $group}
		<option value="{$group.ID}">{$group.name}</option>
	{/foreach}
	</select><br>
	<input type="submit" value="Bestätigen">
</form>