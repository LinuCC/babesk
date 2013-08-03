{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Die Klassen</h2>

<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Jahrgangsstufe</th>
			<th align='center'>Bezeichner</th>
			<th align='center'>Schultyp</th>
		</tr>
	</thead>
	<tbody>
		{foreach $grades as $grade}
		<tr bgcolor='#FFC33'>
			<td align="center">{$grade.ID}</td>
			<td align="center">{$grade.gradelevel}</td>
			<td align="center">{$grade.label}</td>
			<td align="center">{$grade.schooltypeName}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?module=administrator|Kuwasys|Grade|changeGrade&amp;ID={$grade.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<button id='delete#{$grade.ID}'>löschen</button>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

<script type="text/javascript">

$(document).ready(function() {

	var forYourOwnSafety = function(gradeId) {

		if(!$('div#safetyDelConf').length) {
			$('body').append('<div id="safetyDelConf" title="Die Klasse\
							löschen?"><p><span class="ui-icon ui-icon-alert"\
							style="float:left; margin: 0 7px 20px 0;"></span>\
							Die Daten gehen beim löschen unwiederbringlich\
							verloren!</p></div>');
		}
		$('div#safetyDelConf').dialog({
			height: 200,
			width: 550,
			modal: true,
			buttons: {
				'Ja, ich bin mir des Risikos bewusst!': function() {
					window.location = "index.php?module=administrator|Kuwasys\
						|Grade|DeleteGrade&ID=" + gradeId;
				},
				'Nein, nicht löschen': function() {
					$('div#safetyDelConf').remove();
					$(this).dialog('close');
				}
			}
		});

	}

	$('button[id^=delete#]').on('click', function(event) {
		event.preventDefault();
		var toDelete = $(this).attr('id').replace('delete#', '');
		$('body').append('<div id="delConf" title="Die Klasse löschen?">\
						<p><span class="ui-icon ui-icon-alert"\
						style="float:left; margin: 0 7px 20px 0;"></span>\
						Die Klasse und die dazugehörigen Daten werden\
						dauerhaft gelöscht! Sind sie sich sicher?</p>\
						</div>');
		$('div#delConf').dialog({
			height: 200,
			width: 400,
			modal: true,
			buttons: {
				'Ja, löschen!': function() {
					forYourOwnSafety(toDelete);
					$('div#delConf').remove();
					$(this).dialog('close');
				},
				'Nein, nicht löschen': function() {
					$('div#delConf').remove();
					$(this).dialog('close');
				}
			}
		});
	});
});


</script>
{/block}
