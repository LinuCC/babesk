{include file='web/header.tpl' title='Kursliste'}

<script type="text/javascript" src="../smarty/templates/web/headmod_Kuwasys/classDescriptionSwitch.js">
</script>
<script type="text/javascript" src="../smarty/templates/web/headmod_Kuwasys/generalFunctions.js">
</script>

<style type='text/css'  media='all'>

.classListLabelSelectable {
	float: left;
	font-weight: bold;
}

table {
	width: 700px;
	border: 1px solid #55AA55;
}

td.classListCheckbox  {
	width: 120px;
}

input.classListCheckbox {
	margin-left: 50px;
}

th.classListClassLabel {
	font-weight: normal;
	color: rgb(150,150,0);
}

p.weekdayHeading {
	font-weight: bold;
	font-variant: small-caps;
}

p.helpTextLockedClasses {

	text-align: center;
	border: 1px solid #000000;
}

</style>

<h2>Kursliste</h2><br>
<form action="index.php?section=Kuwasys|ClassList&action=formSubmitted" method="post">
{foreach $classUnits as $classUnit}
<p class="weekdayHeading">
{$classUnit.translatedName}
</p>
<table>
		<tr>
			<th>Kurs</th>
			<th>Erste Wahl</th>
			<th>Zweite Wahl</th>
		</tr>
		{foreach $classes as $class}
		{if $class.unitId == $classUnit.ID}
		<tr>
			<th class="classListClassLabel"><a onclick="switchClassDescriptionOfLink('{$class.ID}')">{$class.label}{if !$class.registrationForUserAllowed} (gesperrt){/if}</a>
			<div id="classDescription#{$class.ID}" class="classDescription">
		<p>{$class.description}</p>
		<script type="text/javascript">
			switchClassDescriptionOfLink('{$class.ID}');
		</script>
	</div>
			</th>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="firstChoice{$classUnit.ID}" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="secondChoice{$classUnit.ID}" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
		</tr>
		{/if}
		{/foreach}
	</table><br>
{/foreach}





<!-- <p class="weekdayHeading">
Montag
</p>

{foreach $sortedClasses as $weekdayOfClasses}
{$dayname = array_keys($sortedClasses, $weekdayOfClasses)}

{if $dayname[0] == "Mon"}
	<table>
		<tr>
			<th>Kurs</th>
			<th>Erste Wahl</th>
			<th>Zweite Wahl</th>
		</tr>
		{foreach $weekdayOfClasses as $class}
		<tr>
			<th class="classListClassLabel"><a onclick="switchClassDescriptionOfLink('{$class.ID}')">{$class.label}{if !$class.registrationForUserAllowed} (gesperrt){/if}</a>
			<div id="classDescription#{$class.ID}" class="classDescription" hidden="hidden">
		<p>{$class.description}</p>
	</div>
			</th>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="firstChoiceMon" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="secondChoiceMon" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
		</tr>
		{/foreach}
	</table><br>
{/if}
{/foreach}

<p class="weekdayHeading">
Dienstag
</p>

{foreach $sortedClasses as $weekdayOfClasses}
	<table>
{$dayname = array_keys($sortedClasses, $weekdayOfClasses)}
{if $dayname[0] == "Tue"}
		<tr>
			<th>Kurs</th>
			<th>Erste Wahl</th>
			<th>Zweite Wahl</th>
		</tr>
		{foreach $weekdayOfClasses as $class}
		<tr>
			<th class="classListClassLabel"><a onclick="switchClassDescriptionOfLink('{$class.ID}')">{$class.label}{if !$class.registrationForUserAllowed} (gesperrt){/if}</a>
			<div id="classDescription#{$class.ID}" class="classDescription" hidden="hidden">
		<p>{$class.description}</p>
	</div>
			</th>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="firstChoiceTue" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="secondChoiceTue" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
		</tr>
		{/foreach}
	</table><br>
{/if}
{/foreach}

<p class="weekdayHeading">
Mittwoch
</p>

{foreach $sortedClasses as $weekdayOfClasses}
{$dayname = array_keys($sortedClasses, $weekdayOfClasses)}
{if $dayname[0] == "Wed"}
	<table>
		<tr>
			<th>Kurs</th>
			<th>Erste Wahl</th>
			<th>Zweite Wahl</th>
		</tr>
		{foreach $weekdayOfClasses as $class}
		<tr>
			<th class="classListClassLabel"><a onclick="switchClassDescriptionOfLink('{$class.ID}')">{$class.label}{if !$class.registrationForUserAllowed} (gesperrt){/if}</a>
			<div id="classDescription#{$class.ID}" class="classDescription" hidden="hidden">
		<p>{$class.description}</p>
	</div>
			</th>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="firstChoiceWed" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="secondChoiceWed" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
		</tr>
		{/foreach}
	</table><br>
{/if}
{/foreach}

<p class="weekdayHeading">
Donnerstag
</p>

{foreach $sortedClasses as $weekdayOfClasses}
{$dayname = array_keys($sortedClasses, $weekdayOfClasses)}
{if $dayname[0] == "Thu"}
	<table>
		<tr>
			<th>Kurs</th>
			<th>Erste Wahl</th>
			<th>Zweite Wahl</th>
		</tr>
		{foreach $weekdayOfClasses as $class}
		<tr>
			<th class="classListClassLabel"><a onclick="switchClassDescriptionOfLink('{$class.ID}')">{$class.label}{if !$class.registrationForUserAllowed} (gesperrt){/if}</a>
				<div id="classDescription#{$class.ID}" class="classDescription" hidden="hidden">
				<p>{$class.description}</p>
				</div>
			</th>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="firstChoiceThu" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="secondChoiceThu" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
		</tr>
		{/foreach}
	</table><br>
{/if}
{/foreach}

<p class="weekdayHeading">
Freitag
</p>

{foreach $sortedClasses as $weekdayOfClasses}
{$dayname = array_keys($sortedClasses, $weekdayOfClasses)}
{if $dayname[0] == "Fri"}
	<table>
		<tr>
			<th>Kurs</th>
			<th>Erste Wahl</th>
			<th>Zweite Wahl</th>
		</tr>
		{foreach $weekdayOfClasses as $class}
		<tr>
			<th class="classListClassLabel"><a onclick="switchClassDescriptionOfLink('{$class.ID}')">{$class.label}{if !$class.registrationForUserAllowed} (gesperrt){/if}</a>
			<div id="classDescription#{$class.ID}" class="classDescription" hidden="hidden">
		<p>{$class.description}</p>
	</div>
			</th>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="firstChoiceFri" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
			<td class="classListCheckbox"><input class="classListCheckbox" type="radio" name="secondChoiceFri" value="{$class.ID}" {if !$class.registrationForUserAllowed}disabled="disabled"{/if}></td>
		</tr>
		{/foreach}
	</table><br>
{/if}
{/foreach} -->
<input type="submit" value="Absenden">
<a style="float:right" onmouseover="showHelpTextLockedClasses()" onmouseout="hideHelpTextLockedClasses()">Warum sind Kurse gesperrt?</a><br>
<p class="helpTextLockedClasses" hidden="hidden">Für gesperrte Kurse kann sich der Benutzer nicht mehr anmelden. Entweder
diese Kurse sind voll, erlauben generell keine Anmeldungen oder sie haben sich für diesen Veranstaltungstag schon bei
anderen Kursen angemeldet.</p>
</form>
{include file='web/footer.tpl'}