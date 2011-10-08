<form action='index.php?section=groups&action=4&where={$ID}' method='post'>
	<label>ID der Gruppe: <input type='text' value="{$ID}" name="ID" /> </label><br>
	<label>Name der Gruppe: <input type='text' value="{$name}" name="name" /> </label><br>
	<label>Maximales Guthaben der Gruppe: <input type='text' value="{$max_credit}" size="5" maxlength="5" name="max_credit" />Euro</label><br>
	<input type="submit" value="bestätigen">
</form>