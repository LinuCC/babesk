{include file='web/header.tpl' title='Hauptmenü des Kurswahlsystems'}

<script type="text/javascript" src="../smarty/templates/web/headmod_Kuwasys/classDescriptionSwitch.js">
</script>

<style type='text/css'  media='all'>


a.classListing {
}
p.classListing {
	color: maroon;
}

div.classListing {

	border-style: solid;
	border-width: 1px;
	border-color: #2e6132;
	-webkit-border-radius: 20px;
  -khtml-border-radius: 20px;
  -moz-border-radius: 20px;
	border-radius: 20px;
	margin: 0 auto;
	padding: 15px;
	width: 650px;
}

</style>

<h2>Hauptmenü des Kurswahlsystems</h2><br>
<div class="classListing">
<h4 style="text-align:center">Übersicht der Kurse</h4>
{if !count($classes)}
Keine Kurse wurden ausgewählt.
{else}
{foreach $classes as $unitname => $classesAtUnit}
	<b>{$unitname}:</b><br>
	{foreach $classesAtUnit as $class}
		<b>&nbsp;&nbsp;&nbsp;&nbsp;
		{if $class.registrationEnabled}
			<a class="classListing" onmouseover="displayClassDescription('{$class.ID}')" onmouseout="hideClassDescription('{$class.ID}')"
				{if $class.status == 'Aktiv'} style="color: rgb(255, 50, 50);"
				{else if $class.status == 'waiting'} style="color: rgb(50, 255, 50);"{/if}
				href="index.php?section=Kuwasys|ClassDetails&classId={$class.ID}">{$class.label} ({$class.status})</a>
		{else}
			<p class="classListing" onmouseover="displayClassDescription('{$class.ID}')" onmouseout="hideClassDescription('{$class.ID}')"
				{if $class.status == 'Aktiv'} style="color: rgb(255, 50, 50);"
				{else if $class.status == 'Wartend'} style="color: rgb(50, 255, 50);"{/if}
				href="index.php?section=Kuwasys|ClassDetails&classId={$class.ID}">{$class.label} ({$class.status})</p>
		{/if}</b>
		<br>
		<div id="classDescription#{$class.ID}" class="classDescription" hidden="hidden">
			<p>{$class.status}</p>
			<p>{$class.description}</p>
		</div>
	{/foreach}
	{*Cancel all registrations for the User that are in this Unit*}
	{* Button not needed anymore, User should be able to click at two Classes
	<form action="index.php?section=Kuwasys|MainMenu&amp;action=cancelClassRegOfDay&amp;unitId={$unit->unit.ID}" method="post">
		<input type="submit" value="alle Kurse an diesem Tag abmelden">
	</form>
	*}
{/foreach}
{/if}

<br><br>
<form action="index.php?section=Kuwasys|ClassList" method="post">
	<input type="submit" value="Zur Kurswahlliste">
</form>
</div>
{include file='web/footer.tpl'}
