{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Die Kursleiter</h2>

<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Vorname</th>
			<th align='center'>Name</th>
			<th align='center'>Adresse</th>
			<th align='center'>Telefon</th>
			<th align='center'>Kurse leitend</th>
		</tr>
	</thead>
	<tbody>
		{foreach $classteachers as $classteacher}
		<tr bgcolor='#FFC33'>
			<td align="center">{$classteacher.ID}</td>
			<td align="center">{$classteacher.forename}</td>
			<td align="center">{$classteacher.name}</td>
			<td align="center">{$classteacher.address}</td>
			<td align="center">{$classteacher.telephone}</td>
			<td align="center">{$classteacher.classes}</td>
			</td>
			<td align="center" bgcolor='#FFD99'>
				<form action="index.php?module=administrator|Kuwasys|Classteachers|Change&amp;ID={$classteacher.ID}" method="post">
					<input type='submit' value='bearbeiten'>
				</form>
				<form class="deleteClassteacher" classteacherId="{$classteacher.ID}"
					action="#" method="post">
					<input type='submit' value='löschen'>
				</form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

<!-- index.php?module=administrator|Kuwasys|Classteachers|Delete&amp;ID={$classteacher.ID} -->

<script type="text/javascript">

$(document).ready(function() {

	$('form.deleteClassteacher').on('submit', function(event) {

		event.preventDefault();

		var toDelete = $(this).attr('classteacherId');

		$('body').append('<div id="delConf" title="Klassenlehrer wirklich löschen?}">\
						<p><span class="ui-icon ui-icon-alert"\
						style="float:left; margin: 0 7px 20px 0;"></span>\
						Der Klassenlehrer wird dauerhaft gelöscht! Sind sie sich wirklich sicher?</p>\
						</div>');
		$('div#delConf').dialog({
			height: 200,
			width: 400,
			modal: true,
			buttons: {
				'Ja, löschen!': function() {
					window.location = "index.php?module=administrator|Kuwasys\
					|Classteachers|Delete&ID=" + toDelete;
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
