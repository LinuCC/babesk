{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Die Kurse</h2>

<script type="text/javascript">
function showOptions (ID) {
	document.getElementById('optionButtons' + ID).hidden = false;
	document.getElementById('option' + ID).hidden = true;
}
</script>

<div class="filterBar">
<h3>Filter</h3>
<form action="index.php?section=Kuwasys|Classes&action=showClass" method="post">
	<input id="filterBarSubmitButton" type="submit" value="Absenden">
Nach
<select name="keyToSortAfter">
	<option value="label">Name</option>
	<option value="schoolyearLabel">Schuljahr</option>
</select>
sortieren.<br>
<select name="keyToFilterAfter">
	<option value="label">Name</option>
	<option value="schoolyearLabel">Schuljahr</option>
</select>
nach
	<input type="text" name="filterValue" maxlength="12">
filtern.
</form>
</div><br>

<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Name</th>
			<th align='center'>Kursleiter</th>
			<th align='center'>Aktive Teilnehmer</th>
			<th align='center'>Wartende Teilnehmer</th>
			<th align='center'>Wünschende Teilnehmer</th>
			<th align='center'>Maximale Registrierungen</th>
			<th align='center'>Schuljahr</th>
			<th align='center'>Veranstaltungstag</th>
		</tr>
	</thead>
	<tbody>
		{foreach $classes as $class}
		<tr bgcolor='#FFC33'>
			<td align="center">{$class.ID}</td>
			<td align="center">{$class.label}</td>
			<td align="center">{$class.classteacherName}</td>
			<td align="center">{$class.activeCount}</td>
			<td align="center">{$class.waitingCount}</td>
			<td align="center">{$class.request1Count + $class.request2Count}</td>
			<td align="center">{$class.maxRegistration}</td>
			<td align="center">{$class.schoolyearLabel}</td>
			<td align="center">{$class.unitTranslatedName}</td>
			<td align="center" bgcolor='#FFD99'>
			<div id='option{$class.ID}'>
			<form method="post"><input type='button' value='Optionen' onclick='showOptions("{$class.ID}")'></form>
			</div>
			<div id='optionButtons{$class.ID}' hidden>
			<form action="index.php?section=Kuwasys|Classes&action=changeClass&ID={$class.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=Kuwasys|Classes&action=deleteClass&ID={$class.ID}" method="post"><input type='submit' value='löschen'></form>
			<form action="index.php?section=Kuwasys|Classes&action=showClassDetails&ID={$class.ID}" method="post"><input type='submit' value='Details anzeigen'></form>
			</div>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}