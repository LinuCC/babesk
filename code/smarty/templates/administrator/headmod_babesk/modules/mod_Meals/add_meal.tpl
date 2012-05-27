<!-- str-ifs looks up if some fields should be filled out beforehand -->
{extends file=$mealParent}{block name=content}
    <form action="index.php?section=babesk|Meals&amp;action=1" method="post">
		<label>Name der Mahlzeit:<br><input type="text" name="name" size="40" {if $name_str}value="{$name_str}"{/if} ></label><br><br>
		<label>Beschreibung<br><textarea name="description" cols="40" rows="10" >{if $descr_str}{$descr_str}{/if}</textarea></label><br><br>
		<label>Preisklasse:</label><br>
		<select name="price_class">
			{if $pc_str} {html_options values=$price_class_id output=$price_class_name selected=$pc_str}
			{else} {html_options values=$price_class_id output=$price_class_name selected="1"}
			{/if}
		</select><br><br>
		<label>Maximale Bestellungen:<br><input type="text" name="max_orders" maxlength="5" size = "5" {if $max_order_str}value="{$max_order_str}"{else}value="999"{/if}/></label><br><br>
		{if $date_str} {html_select_date end_year="+1" time=$date_str}
		{else}{html_select_date end_year="+1"}
		{/if}
		<input type="submit" value="Hinzuf&uuml;gen" />
	</form>

	{/block}