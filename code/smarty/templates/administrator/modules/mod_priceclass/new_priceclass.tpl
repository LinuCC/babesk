{extends file=$base_path}{block name=content}
<form action='index.php?section=priceclass&action=1' method='post'>
	<label>Name der Preisklasse:<input type="text" name='name' size='20'/> </label><br>
	<label>Zu welcher Gruppe gehört die Preisklasse?:</label>
	<select name='group_id'>
		{$counter = 0}
		{foreach $groups as $group}
			<option value={$group['ID']}>{$group['name']}</option>
			{$counter = $counter + 1}
		{/foreach}
	</select><br>
	<label>Preis:<input type='text' name='price' size='5'>Euro</label><br>
	<input type="submit" value="Preisklasse hinzufügen">
</form>
{/block}