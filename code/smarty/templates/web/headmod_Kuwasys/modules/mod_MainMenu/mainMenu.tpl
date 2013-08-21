{extends file=$inh_path}{block name='content'}

<script type="text/javascript" src="../smarty/templates/web/headmod_Kuwasys/classDescriptionSwitch.js">
</script>

<style type='text/css'  media='all'>


a.classListing {
}
p.classListing {
	color: maroon;
}

.classlistingContainer {
	margin-left: 4em;
	margin-bottom: 20px;
}

</style>

<h2>Hauptmenü des Kurswahlsystems</h2><br>
<h4 style="text-align:center">Übersicht der Kurse</h4>
{if !count($classes)}
Keine Kurse wurden ausgewählt.
{else}
{foreach $classes as $unitname => $classesAtUnit}
	<h4>{$unitname}:</h4>
	<ul>
	{foreach $classesAtUnit as $class}
		<li class="classlistingContainer" classId="{$class.ID}">
			<b>
			{if $class.registrationEnabled}
				<a class="classListing" classId="{$class.ID}"
					{if $class.status == 'Aktiv'}
						style="color: rgb(255, 50, 50);"
					{else if $class.status == 'waiting'}
						style="color: rgb(50, 255, 50);"
					{/if}
					title="Klicken für Optionen"
					href="index.php?section=Kuwasys|ClassDetails&classId={$class.ID}">{$class.label} ({$class.status})</a>
			{else}
				<p class="classListing" classId="{$class.ID}"
					{if $class.status == 'Aktiv'}
						style="color: rgb(255, 50, 50);"
					{else if $class.status == 'Wartend'}
						style="color: rgb(50, 255, 50);"
					{/if}
					href="index.php?section=Kuwasys|ClassDetails&classId={$class.ID}"
					>{$class.label} ({$class.status})</p>
			{/if}
			</b>
			<div id="classDescription_{$class.ID}" class="classDescription" hidden="hidden">
				<p>{$class.status}</p>
				<p class="quotebox">{$class.description}</p>
			</div>
		</li>
	{/foreach}
	</ul>
{/foreach}
{/if}

<br><br>
<form action="index.php?section=Kuwasys|ClassList" method="post">
	<input type="submit" value="Zur Kurswahlliste">
</form>
{/block}
