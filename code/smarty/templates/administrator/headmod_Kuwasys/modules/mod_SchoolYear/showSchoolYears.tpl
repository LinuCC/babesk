{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Die Schuljahre</h2>

{literal}

<script type="text/javascript">
function showOptions (ID) {
	document.getElementById('optionButtons' + ID).hidden = false;
	document.getElementById('option' + ID).hidden = true;
}
</script>

<style type="text/css">
.cleanButtons {
	display : inline;
}
.switchButton {
	
}
</style>
{/literal}

<table style='margin:0 auto;'>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Bezeichnung</th>
			<th align='center'>Aktiv</th>
		</tr>
	</thead>
	<tbody>
		{foreach $schoolYears as $schoolYear}
		<tr {if $schoolYear.active}bgcolor='rgbcolor(255,100,100)'{else}bgcolor='#FFC33'{/if}>
			<td align="center">{$schoolYear.ID}</td>
			<td align="center">{$schoolYear.label}</td>
			<td align="center">{if $schoolYear.active}&#10004;{else}&#10008;{/if}</td>
			<td align="center" bgcolor='#FFD99'>
			<div id='option{$schoolYear.ID}'>
			<form method="post"><input type='button' value='Optionen' onclick='showOptions("{$schoolYear.ID}")'></form>
			</div>
			<div id='optionButtons{$schoolYear.ID}' hidden>
			<form class='cleanButtons' action="index.php?section=Kuwasys|SchoolYear&action=changeSchoolYear&ID={$schoolYear.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form class='cleanButtons' action="index.php?section=Kuwasys|SchoolYear&action=deleteSchoolYear&ID={$schoolYear.ID}" method="post"><input style='inline' type='submit' value='löschen'></form>
			{if !($schoolYear.active)}<form class='cleanButtons' action="index.php?section=Kuwasys|SchoolYear&action=activateSchoolYear&ID={$schoolYear.ID}" method="post"><input style='inline' type='submit' value='aktivieren'></form>{/if}
			</div>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}