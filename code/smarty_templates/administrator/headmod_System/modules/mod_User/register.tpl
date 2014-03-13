{extends file=$UserParent}{block name=content}

<script>
	var grades = {json_encode($grades)};
	var schoolyears = {json_encode($schoolyears)};
</script>

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
		<label>
			Kartennummer:
			<a class="cardnumberAdd" href="#">hinzufügen</a>
			<input style="display:inline" name="cardnumber" class="inputItem cardnumberAdd" type="text" size="10" maxlength="10" />
		</label>

		<div class="simpleForm clearfix">
				<p class="inputItem">Gruppen:</p>
				{if empty($usergroups)}
					<p class="inputItem">
						keine Gruppen vorhanden
					</p>
				{else}
					<div class="inputItem">
						<div class="scrollableCheckboxes">
							{foreach $usergroups as $id => $name}
								<input type="checkbox"
									name="groups[{$id}]"/>
									{$name}<br />
							{/foreach}
						</div>
					</div>
				{/if}
		</div>

		<fieldset class="schoolyearGradeContainer smallContainer">
			<legend>Schuljahre und Klassen:</legend>
			{foreach $gradesAndSchoolyearsOfUser as $gas}
				<div class="schoolyearGradeRow">
					Im Schuljahr
					<select name="schoolyearId">
						{foreach $schoolyears as $syId => $syName}
							<option value="{$syId}">
									{$syName}
							</option>
						{/foreach}
					</select>
					in Klasse
					<select name="gradeId">
						{foreach $grades as $gradeId => $gradeName}
							<option value="{$gradeId}">
									{$gradeName}
							</option>
						{/foreach}
					</select>
					<input type="image" src="../images/status/forbidden_32.png"
						title="Diese Kombination entfernen"
						class="gradeSchoolyearRemove" />
				</div>
				{$counter++}
			{/foreach}
			<input type="image" src="../images/actions/plusbutton_32.png"
				title="Ein neues Schuljahr mit Klasse hinzufügen"
				class="gradeSchoolyearAdd" />
		</fieldset>

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
