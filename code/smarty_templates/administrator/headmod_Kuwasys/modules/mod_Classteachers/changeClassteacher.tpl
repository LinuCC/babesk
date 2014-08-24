{extends file=$inh_path} {block name='content'}

{$isNoClassSelected = true}
{foreach $classes as $class}
	{if $class.selected}
		{$isNoClassSelected = false}
	{/if}
{/foreach}
<h2 class='module-header'>Einen Kursleiter verändern</h2>

<form action='index.php?module=administrator|Kuwasys|Classteachers|Change&amp;ID={$classteacher.ID}' method='post'>

	<label>Vorname: <input type='text' name='forename' value='{$classteacher.forename}'></label><br><br>
	<label>Name: <input type='text' name='name' value='{$classteacher.name}'></label><br><br>
	<label>Adresse: <input type='text' name='address' value='{$classteacher.address}'></label><br><br>
	<label>Telefon: <input type='text' name='telephone' value='{$classteacher.telephone}'></label><br><br>
	<select name='classes[]' size='10' multiple='multiple'>
			<option value='NoClass' {if !count($classesOfClassteacher)}selected='selected'{/if}>==Kein Kurs==</option>
		{foreach $classes as $class}
			<option
				value='{$class.ID}'
				{if array_search($class.ID, $classesOfClassteacher) !== false}
				selected='selected'{/if}>
				{$class.label}
			</option>
		{/foreach}
	</select><br>
	<p>Hinweis: Sie können mehrere Kurse durch halten der Strg-Taste selektieren</p><br>
	<input type='submit' value='Kursleiter verändern'>
</form>

{/block}
