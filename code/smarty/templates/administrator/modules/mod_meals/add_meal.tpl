    <form action="index.php?section=meals&amp;action=1" method="post">
		<label>Name der Mahlzeit:<br><input type="text" name="name" size="40"/></label><br><br>
		<label>Beschreibung<br><textarea name="description" cols="40" rows="10"></textarea></label><br><br>
		<label>Preisklasse:</label><br>
		<select name="price_class">
			{html_options values=$price_class_id output=$price_class_name selected="1"}
		</select><br><br>
		<label>Maximale Bestellungen:<br><input type="text" name="max_orders" maxlength="5" size = "5" value="999"/></label><br><br>
		<label><input type="text" name="day" maxlength="2" size="2" />  Tag</label><br>
		<label><input type="text" name="month" maxlength="2" size="2" />  Monat</label><br>
		<label><input type="text" name="year" maxlength="4" size="4" />  Jahr</label><br><br>
		<label><input type="checkbox" name="is_vegetarian" value="is_vegetarian" />Die Mahlzeit ist vegetarisch</label><br><br>
		<input type="submit" value="Hinzuf&uuml;gen" />
	</form>
