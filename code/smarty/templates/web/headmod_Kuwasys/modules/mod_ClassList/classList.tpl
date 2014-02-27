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
<form action="index.php?module=web|Kuwasys|ClassList|UserSelectionsApply"
	method="post"
>
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
				<th class="classListClassLabel">
					<a href="#" class="classlabel" title="klicken um Details anzuzeigen">
						{$class.label}
						{if !$class.registrationEnabled}
							(gesperrt)
						{/if}
					</a>
					<div class="classDescription">
						<p>
							{$class.description}
						</p>
						<p>
							{if isset($class.classteacher)}
							Kursleiter:
								{$class.classteacher}
							{/if}
						</p>
					</div>
				</th>
				<td class="classListCheckbox">
					<input class="classListCheckbox" type="checkbox" name="firstChoice{$classUnit.ID}" value="{$class.ID}" {if !$class.registrationEnabled}disabled="disabled"{/if} />
				</td>
				<td class="classListCheckbox">
					<input class="classListCheckbox" type="checkbox" name="secondChoice{$classUnit.ID}" value="{$class.ID}" {if !$class.registrationEnabled}disabled="disabled"{/if} />
				</td>
			</tr>
			{/if}
			{/foreach}
		</table><br>
	{/foreach}

	<input type="submit" value="Absenden">
	<a style="float:right" onmouseover="showHelpTextLockedClasses()" onmouseout="hideHelpTextLockedClasses()">Warum sind Kurse gesperrt?</a><br>
	<p class="helpTextLockedClasses" hidden="hidden">Für gesperrte Kurse kann sich der Benutzer nicht mehr anmelden. Entweder
	diese Kurse sind voll, erlauben generell keine Anmeldungen oder sie haben sich für diesen Veranstaltungstag schon bei
	anderen Kursen angemeldet.</p>
</form>


<script type="text/javascript">

$(document).ready(function() {

	$('div.classDescription').hide();

	$('a.classlabel').on('click', function(event) {
		event.preventDefault();
		$(this).parent().children('div.classDescription').toggle();
	});

	$('input.classListCheckbox').on('click', function(event) {

		var nameBeginning = $(this).attr('name').replace(/Choice.*/, 'Choice');

		$(this).parent().siblings().children('input').attr('checked', false);
		$(this).parents('table').
			find('input.classListCheckbox[name^=' + nameBeginning + ']')
			.not($(this)).attr('checked', false);
	});


});

</script>
{include file='web/footer.tpl'}
