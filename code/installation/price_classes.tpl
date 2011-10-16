<form action='index.php?step=4' method='post'>
	<b>Hier können sie Preisklassen hinzufügen. Diese entscheiden, wie viel jede Gruppe für die Mahlzeiten
		zu bezahlen hat<br><br></b>
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
	<input type="submit" name='add_another' value="Preisklasse hinzufügen">
	<input type="submit" name='go_on' value="Fortfahren">
</form>