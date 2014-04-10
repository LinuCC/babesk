{extends file=$inh_path} {block name='filling_content'}

<h2 class='moduleHeader'>Die Kursleiter</h2>

<table class="table table-responsive table-hover table-striped">
	<thead>
		<tr>
			<th>ID</th>
			<th>Vorname</th>
			<th>Name</th>
			<th>Adresse</th>
			<th>Telefon</th>
			<th>Kurse leitend dieses Jahr</th>
		</tr>
	</thead>
	<tbody>
		{foreach $classteachers as $classteacher}
		<tr>
			<td>{$classteacher.ID}</td>
			<td>{$classteacher.forename}</td>
			<td>{$classteacher.name}</td>
			<td>{$classteacher.address}</td>
			<td>{$classteacher.telephone}</td>
			<td>{$classteacher.classes}</td>
			</td>
			<td>
				<a class="btn btn-info btn-xs"
					href="index.php?module=administrator|Kuwasys|Classteachers|Change&amp;ID={$classteacher.ID}">
					<span class="icon icon-edit"></span>
				</a>
				<button type="button"
					class="btn btn-danger btn-xs delete-classteacher">
					<span class="icon icon-error"></span>
				</button>
				<form class="deleteClassteacher" classteacherId="{$classteacher.ID}"
					action="#" method="post">
					<input type='submit' value='löschen'>
				</form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}


{block name=js_include append}

<script type="text/javascript" src="{$path_js}/bootbox.min.js"></script>
<script type="text/javascript">

$(document).ready(function() {

	bootbox.setDefaults({locale: 'de'});

	$('button.delete-classteacher').on('click', function(event) {

		event.preventDefault();

		var toDelete = $(this).attr('classteacherId');

		bootbox.confirm(
			'Der Klassenlehrer wird dauerhaft gelöscht! Sind sie sich wirklich \
			sicher?',
			function(res) {
				if(res) {
					window.location = "index.php?module=administrator|Kuwasys\
					|Classteachers|Delete&ID=" + toDelete;
				}
			}
		);

		// $('body').append('<div id="delConf" title="Klassenlehrer wirklich löschen?}">\
		// 				<p><span class="ui-icon ui-icon-alert"\
		// 				style="float:left; margin: 0 7px 20px 0;"></span>\
		// 				Der Klassenlehrer wird dauerhaft gelöscht! Sind sie sich wirklich sicher?</p>\
		// 				</div>');
		// $('div#delConf').dialog({
		// 	height: 200,
		// 	width: 400,
		// 	modal: true,
		// 	buttons: {
		// 		'Ja, löschen!': function() {
		// 			window.location = "index.php?module=administrator|Kuwasys\
		// 			|Classteachers|Delete&ID=" + toDelete;
		// 		},
		// 		'Nein, nicht löschen': function() {
		// 			$('div#delConf').remove();
		// 			$(this).dialog('close');
		// 		}
		// 	}
		// });
	});
});

</script>

{/block}