{extends file=$UserParent}{block name=content}

<script src="../smarty/templates/administrator/AddItemInterface.js">
	</script>
<script src="../smarty/templates/administrator/headmod_System/modules/mod_User/register.js">
</script>

<form class="simpleForm" action="#" method="post">
	<fieldset>
		<legend>Persönliche Daten</legend>
		<label>Vorname:
			<input class="inputItem" type="text" name="forename" />
		</label>
		<label>Name:
			<input class="inputItem" type="text" name="name" />
		</label>
		<label>Benutzername:
			<input class="inputItem" type="text" name="username" />
		</label>
		<label>Passwort:
			<input class="inputItem" type="password" name="password" />
		</label>
		<label>Passwort wiederholen:
			<input class="inputItem" type="password" name="passwordRepeat" />
		</label>
		<label>Emailadresse:
			<input class="inputItem" type="text" name="email" />
		</label>
		<label>Telefonnummer:
			<input class="inputItem" type="text" name="telephone" />
		</label>
		<label>
			Geburtstag :
			<input class="inputItem" type="text" size="10" name="birthday" />
		</label>
		<label>Klasse:
			{if empty($grades)}
				<p class="inputItem">
					keine Klassen vorhanden
				</p>
			{else}
				<select name="gradeId" class="inputItem">
					{html_options options=$grades selected="1"}
				</select>
			{/if}
		</label>
		<label>
			Kartennummer:
			<a class="cardnumberAdd" href="#">hinzufügen</a>
			<input style="display:inline" name="cardnumber" class="inputItem cardnumberAdd" type="text" size="10" maxlength="10" />
		</label>
		<label>Schuljahr:
			{if empty($priceGroups)}
				<p class="inputItem">
					keine Schuljahre vorhanden, wird höchstwahrscheinlich Probleme bereiten!
				</p>
			{else}
				<select class="inputItem" name="schoolyearId">
					{html_options options=$schoolyears}
					<option value="">Keins</option>
				</select>
			{/if}
		</label>
	</fieldset>
	<fieldset>
		<legend>BaBeSK</legend>
		<label>Preisgruppen:
				{if empty($priceGroups)}
					<p class="inputItem">
						keine Preisgruppen vorhanden
					</p>
				{else}
					<select class="inputItem" name="pricegroupId">
						<option value="">Keine</option>
						{html_options options=$priceGroups selected="1"}
					</select>
				{/if}
		</label>
		<label>Guthaben:
			<input class="inputItem" type="int" name="credits" size="5" maxlength="5" />
		</label>
		<label>Teilhabepaket:
			<input class="inputItem" type="checkbox" name="isSoli" />
		</label>
	</fieldset>
	<input type="submit" value="Submit" />
</form>

{/block}