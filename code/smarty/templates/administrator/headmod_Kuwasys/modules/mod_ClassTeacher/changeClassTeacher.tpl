{extends file=$inh_path} {block name='content'}

{$isNoClassSelected = true}
{foreach $classes as $class}
	{if $class.selected}
		{$isNoClassSelected = false}
	{/if}
{/foreach}

<h2 class='moduleHeader'>Einen Kursleiter verändern</h2>

<form action='index.php?section=Kuwasys|ClassTeacher&action=changeClassTeacher&ID={$classTeacher.ID}' method='post'>

	<label>Vorname: <input type='text' name='forename' value='{$classTeacher.forename}'></label><br><br>
	<label>Name: <input type='text' name='name' value='{$classTeacher.name}'></label><br><br>
	<label>Adresse: <input type='text' name='address' value='{$classTeacher.address}'></label><br><br>
	<label>Telefon: <input type='text' name='telephone' value='{$classTeacher.telephone}'></label><br><br>
	<select name='class[]' size='10' multiple='multiple'>
			<option value='NoClass' {if $isNoClassSelected}selected='selected'{/if}>==Kein Kurs==</option>
		{foreach $classes as $class}
			<option 
				value='{$class.ID}'
				{if $class.selected}selected='selected'{/if}> 
				{$class.label}
			</option>
		{/foreach}
	</select><br>
	<p>Hinweis: Sie können mehrere Kurse durch halten der Strg-Taste selektieren</p><br>
	<input type='submit' value='Kursleiter verändern'>
</form>

{/block}