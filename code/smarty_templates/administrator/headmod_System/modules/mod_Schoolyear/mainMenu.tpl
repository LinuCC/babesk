
{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Hauptmenü der Schuljahresverwaltung</h2>

<fieldset>
	<legend>Generell</legend>
	<ul class="submodulelinkList">
		<li>
			<a href='index.php?module=administrator|System|Schoolyear&amp;action=addSchoolYear' method='post'>
				ein neues Schuljahr hinzufügen
			</a>
		</li>
		<li>
			<a href='index.php?module=administrator|System|Schoolyear&amp;action=showSchoolYear' method='post'>
				Die Schuljahre anzeigen
			</a>
		</li>
		<li>
			<a href='index.php?module=administrator|System|Schoolyear|SwitchSchoolyear'
			method='post'>
				Ein Schuljahreswechsel durchführen
			</a>
		</li>
	</ul>
</fieldset>


{/block}
