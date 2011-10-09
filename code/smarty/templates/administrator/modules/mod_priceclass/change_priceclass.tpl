<form action='index.php?section=priceclass&action=4&where={$ID}' method='post'>
	<label>ID der Preisklasse: <input type='text' value="{$ID}" name="ID" /> </label><br>
	<label>Name der Preisklasse: <input type='text' value="{$name}" name="name" /> </label><br>
	<label>zugehörige Gruppe: </label>
	<select name='group_id'>
		{foreach $groups as $group}
			<option {$group['default']} value={$group['ID']}>{$group['name']}</option>
		{/foreach}
	</select><br>
	<label>Preis: <input type='text' value="{$price}" size="5" maxlength="5" name="price" />Euro</label><br>
	<input type="submit" value="bestätigen">
</form>