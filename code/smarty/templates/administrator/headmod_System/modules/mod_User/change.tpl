{extends file=$UserParent}{block name=content}

<script src="../smarty/templates/administrator/AddItemInterface.js">
	</script>
<script src="../smarty/templates/administrator/headmod_System/modules/
	mod_User/change.js">
</script>

{$userIsInSchoolyear = false}
{foreach $schoolyears as $schoolyear}
	{if !empty($schoolyear.isUserIn)}
		{$userIsInSchoolyear = true}
	{/if}
{/foreach}

<!-- For Datepicker -->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<form class="simpleForm" action="#" method="post">
	<fieldset class="personalData">
		<legend>Persönliche Daten</legend>
		<input class="inputItem" type="hidden" name="ID" value="{$user.ID}" />
		<div class="simpleForm">Vorname:
			<input class="inputItem" type="text" name="forename"
			value="{$user.forename}" />
		</div>
		<div class="simpleForm">Name:
			<input class="inputItem" type="text" name="name"
			value="{$user.name}" />
		</div>
		<div class="simpleForm">Benutzername:
			<input class="inputItem" type="text" name="username"
			value="{$user.username}" />
		</div>
		<div class="simpleForm">Passwort ändern:
			<input class="passwordChange" type="checkbox" name="passwordChange" />
			<input class="inputItem" type="password" name="password" value="" />
		</div>
		<div class="simpleForm">Emailadresse:
			<input class="inputItem" type="text" name="email"
			value="{$user.email}" />
		</div>
		<div class="simpleForm">Telefonnummer:
			<input class="inputItem" type="text" name="telephone"
			value="{$user.telephone}" />
		</div>
		<div class="simpleForm">
			Geburtstag :
			<input class="inputItem" type="text" size="10" name="birthday" value="{$user.birthday}" />
		</div>
		<div class="simpleForm">
			gesperrt :
			<input class="inputItem" type="checkbox" name="accountLocked"
				{if $user.locked}checked="checked"{/if} />
		</div>
		<div class="simpleForm">Klasse:
				{if empty($grades)}
					<p class="inputItem">
						keine Klassem vorhanden
					</p>
				{else}
					<select class="inputItem" name="gradeId">
						<option value="">Keine</option>
						{html_options options=$grades selected=$user.gradeId}
					</select>
				{/if}
		</div>
		<div class="simpleForm">
				Schuljahre:
				{if empty($schoolyears)}
					<p class="inputItem">
						keine Schuljahre vorhanden
					</p>
				{else}
					<select class="inputItem" name="schoolyearIds"
						multiple="multiple" title="Mehrfachwahlen sind durch halten der Strg
						- (oder Ctrl-)Taste beim klicken möglich. Damit kann sich auch die
						angewählten Schuljahre wieder abwählen lassen. Strg+A, um alle
						Klassen auszuwählen.">
						<option value="NONE"
							{if !count($schoolyears) or !$userIsInSchoolyear}
								selected="selected"{/if}>
							Keine
						</option>
						{foreach $schoolyears as $schoolyear}
							<option value="{$schoolyear.ID}"
								{if !empty($schoolyear.isUserIn)}selected="selected"{/if}>
									{$schoolyear.name}
							</option>
						{/foreach}
					</select>
				{/if}
		</div>
		<div class="simpleForm">
			Kartennummer:
				<a class="cardnumberAdd" href="#">hinzufügen</a>
			<input style="display:inline" name="cardnumber" class="inputItem
				cardnumberAdd" type="text" size="10" maxlength="10"
				value="{$cardnumber}" />
		</div>
	</fieldset>
	<fieldset>
		<legend>BaBeSK</legend>
		<div class="simpleForm">Preisgruppen:
				{if empty($priceGroups)}
					<p class="inputItem">
						keine Preisgruppen vorhanden
					</p>
				{else}
					<select class="inputItem" name="pricegroupId">
						<option value="" {if $user.GID != 0}selected="selected"{/if}>
							Keine
						</option>
						{html_options options=$priceGroups selected=$user.GID}
					</select>
				{/if}
		</div>
		<div class="simpleForm">Guthaben:
			<input class="inputItem" type="int" name="credits" size="5" maxlength="5" />
		</div>
		<div class="simpleForm">Teilhabepaket:
			<input class="inputItem" type="checkbox" name="isSoli"
			{if $user.soli}checked="checked"{/if}/>
		</div>
	</fieldset>
	<input id="submit" type="submit" value="verändern" />
</form>
<div><form style="float:right;top:-30px" action="index.php?section=System|User&action=3&ID={$user.ID}" method="post"><input type='submit' value='löschen'></form></div>

{/block}