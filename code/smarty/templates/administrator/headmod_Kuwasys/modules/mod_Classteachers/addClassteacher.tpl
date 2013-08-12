{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Einen Kursleiter hinzufügen</h2>

<form action='index.php?module=administrator|Kuwasys|Classteachers|Add' method='post'>

	<label>Vorname: <input type='text' name='forename'></label><br><br>
	<label>Name: <input type='text' name='name'></label><br><br>
	<label>Adresse: <input type='text' name='address'></label><br><br>
	<label>Telefon: <input type='text' name='telephone'></label><br><br>
	<label>Welchen Kurs leitet der Kursleiter? (nur Kurse des aktivierten Jahrganges)<br>
	<select name='class[]' size='10' multiple='multiple'>
			<option value='NoClass' selected='selected'>==Kein Kurs==</option>
		{foreach $classes as $class}
			<option
				value='{$class.ID}'>
				{$class.label}
			</option>
		{/foreach}
	</select>
	</label><br>
	<p>Hinweis: Sie können mehrere Kurse durch halten der Strg-Taste selektieren</p>
	<br>
	<input type='submit' value='Kursleiter hinzufügen'>
</form>

{/block}
