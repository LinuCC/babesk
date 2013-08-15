{extends file=$UserParent}{block name=content}

<script>
	var grades = {json_encode($grades)};
	var schoolyears = {json_encode($schoolyears)};
	var classes = {json_encode($classes)};
	var statuses = {json_encode($statuses)};
</script>

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
		<fieldset class="schoolyearGradeContainer smallContainer">
			<legend>Schuljahre und Klassen:</legend>
			{foreach $gradesAndSchoolyearsOfUser as $gas}
				<div class="schoolyearGradeRow">
					Im Schuljahr
					<select name="schoolyearId">
						{foreach $schoolyears as $syId => $syName}
							<option value="{$syId}"
								{if $syId == $gas.schoolyearId}
									selected="selected"{/if}>
									{$syName}
							</option>
						{/foreach}
					</select>
					in Klasse
					<select name="gradeId">
						{foreach $grades as $gradeId => $gradeName}
							<option value="{$gradeId}"
								{if $gradeId == $gas.gradeId}
									selected="selected"{/if}>
									{$gradeName}
							</option>
						{/foreach}
					</select>
					<input type="image" src="../images/status/forbidden_32.png"
						title="Diese Kombination entfernen"
						class="gradeSchoolyearRemove" />
				</div>
				{$counter++}
			{foreachelse}
			<fieldset class="smallContainer">
				Der Benutzer ist noch in keinem Schuljahr. Er wird bei einigen Funktionen nicht benutzbar sein.
			</fieldset>
			{/foreach}
			<input type="image" src="../images/actions/plusbutton_32.png"
				title="Ein neues Schuljahr mit Klasse hinzufügen"
				class="gradeSchoolyearAdd" />
		</fieldset>
	</fieldset>

	{if $modsActivated.Babesk}
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
	{/if}
	{if $modsActivated.Kuwasys}
	<fieldset>
		<legend>Kuwasys</legend>
		<fieldset class="schoolyearClassContainer smallContainer">
			<legend>Schuljahre und Kurse:</legend>
			{foreach $classesOfUser as $cas}
				<fieldset class="smallContainer schoolyearClassRow">
					<legend>
						{foreach $classes as $class}
							{if $class.ID == $cas.ID}
								{$class.label}
							{/if}
						{/foreach}
					</legend>
					<p>Im Schuljahr</p>
					<select name="schoolyearId">
						{foreach $schoolyears as $syId => $syName}
							<option value="{$syId}"
								{if $syId == $cas.schoolyearId}
									selected="selected"{/if}>
									{$syName}
							</option>
						{/foreach}
					</select>
					<p>in Kurs</p>
					<select name="classId">
						{foreach $classes as $class}
							<option value="{$class.ID}"
								{if $class.ID == $cas.ID}
									selected="selected"{/if}>
									{$class.label}
							</option>
						{/foreach}
					</select>
					<p>mit Status</p>
					<select name="statusId">
						{foreach $statuses as $status}
							<option value="{$status.ID}"
								{if $status.ID == $cas.statusId}
									selected="selected"{/if}>
									{$status.translatedName}
							</option>
						{/foreach}
					</select>
					<input type="image"
						src="../images/status/forbidden_32.png"
						title="Diese Kombination entfernen"
						class="classSchoolyearRemove" />
				</fieldset>
				{$counter++}
			{foreachelse}
				Der Benutzer ist noch in keinem Kurs.
			{/foreach}
			<input type="image" src="../images/actions/plusbutton_32.png"
				title="Den Schüler einen Kurs zuweisen"
				class="classSchoolyearAdd" />
		</fieldset>
	</fieldset>
	{/if}

	<input id="submit" type="submit" value="verändern" />
</form>
{/block}
