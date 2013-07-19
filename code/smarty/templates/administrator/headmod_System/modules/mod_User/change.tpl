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
		<div class="simpleForm"><p class="inputItem">Vorname:</p>
			<input class="inputItem" type="text" name="forename"
			value="{$user.forename}" />
		</div>
		<div class="simpleForm"><p class="inputItem">Name:</p>
			<input class="inputItem" type="text" name="name"
			value="{$user.name}" />
		</div>
		<div class="simpleForm"><p class="inputItem">Benutzername:</p>
			<input class="inputItem" type="text" name="username"
			value="{$user.username}" />
		</div>
		<div class="simpleForm"><p class="inputItem">Passwort ändern:</p>
			<div class="inputItem">
				<input class="passwordChange" type="checkbox" name="passwordChange" />
				<input style="display: inline" type="password" name="password" value="" />
			</div>
		</div>
		<div class="simpleForm"><p class="inputItem">Emailadresse:</p>
			<input class="inputItem" type="text" name="email"
			value="{$user.email}" />
		</div>
		<div class="simpleForm"><p class="inputItem">Telefonnummer:</p>
			<input class="inputItem" type="text" name="telephone"
			value="{$user.telephone}" />
		</div>
		<div class="simpleForm">
			<p class="inputItem">Geburtstag :</p>
			<input class="inputItem" type="text" size="10" name="birthday" value="{$user.birthday}" />
		</div>
		<div class="simpleForm">
			<p class="inputItem">gesperrt :</p>
			<input class="inputItem" type="checkbox" name="accountLocked"
				{if $user.locked}checked="checked"{/if} />
		</div>
		<div class="simpleForm"><p class="inputItem">Klasse:</p>
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
			<p class="inputItem">Kartennummer:</p>
			<div class="inputItem">
				<a class="cardnumberAdd" href="#">ändern</a>
			<input style="display:inline" name="cardnumber" class="inputItem
				cardnumberAdd" type="text" size="10" maxlength="10"
				value="{$cardnumber}" />
			</div>
		</div>
		<div class="simpleForm clearfix">
				<p class="inputItem">Gruppen:</p>
				{if empty($groups)}
					<p class="inputItem">
						keine Gruppen vorhanden
					</p>
				{else}
					<div class="inputItem">
						<div class="scrollableCheckboxes">
							{foreach $groups as $group}
								<input type="checkbox"
									name="groups[{$group.ID}]"
									{if $group.isUserIn}
										checked="checked"
									{/if}/>
									{$group.name}<br />
							{/foreach}
						</div>
					</div>
				{/if}
		</div>
		<div class="simpleForm">
				<p class="inputItem">Schuljahre:</p>
				{if empty($schoolyears)}
					<p class="inputItem">
						keine Schuljahre vorhanden
					</p>
				{else}
					<select class="inputItem" name="schoolyearIds"
						multiple="multiple" title="Mehrfachwahlen sind durch halten der Strg
						- (oder Ctrl-)Taste beim klicken möglich. Damit kann sich auch die
						angewählten Schuljahre wieder abwählen lassen. Strg+A, um alle
						Schuljahre auszuwählen.">
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
	</fieldset>
	<fieldset>
		<legend>BaBeSK</legend>
		<div class="simpleForm"><p class="inputItem">Preisgruppen:</p>
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
		<div class="simpleForm"><p class="inputItem">Guthaben:</p>
			<input class="inputItem" type="int" name="credits" size="5" maxlength="5" value="{$user.credit}"/>
		</div>
		<div class="simpleForm"><p class="inputItem">Teilhabepaket:</p>
			<input class="inputItem" type="checkbox" name="isSoli"
			{if $user.soli}checked="checked"{/if}/>
		</div>
	</fieldset>
	<input id="submit" type="submit" value="verändern" />
</form>
{/block}
