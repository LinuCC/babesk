{extends file=$inh_path} {block name='content'}

<h2 class='module-header'>Die Klassen</h2>

<table class="table table-striped table-responsive">
	<thead>
		<tr>
			<th>ID</th>
			<th>Jahrgangsstufe</th>
			<th>Bezeichner</th>
			<th>Schultyp</th>
			<th>Optionen</th>
		</tr>
	</thead>
	<tbody>
		{foreach $grades as $grade}
		<tr>
			<td>{$grade.ID}</td>
			<td>{$grade.gradelevel}</td>
			<td>{$grade.label}</td>
			<td>{$grade.schooltypeName}</td>
			<td>
				<div class="btn-group">
					<a class="btn btn-info btn-xs" data-toggle="tooltip"
						title="Klasse ändern"
						href="index.php?module=administrator|System|Grade|ChangeGrade&amp;ID={$grade.ID}">
						<span class="icon icon-businesscard"></span>
					</a>
					<button class="btn btn-danger btn-xs delete-grade" data-toggle="tooltip" title="Klasse löschen" gradeid="{$grade.ID}">
						<span class="icon icon-error"></span>
					</button>
				</div>
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

	$('button.delete-grade').on('click', function(ev) {
		$button = $(this);
		bootbox.confirm(
			'Die Klasse und die dazugehörigen Daten werden\
				dauerhaft gelöscht! Dazu gehören auch Historie-Daten\
				von vorherigen Schuljahren, die mit dieser Klasse in\
				Verbindung stehen! Sind sie sich sicher?',
			function(res) {
				if(res) {
					var gradeId = $button.attr('gradeid');
					window.location = "index.php?module=administrator|System\
						|Grade|DeleteGrade&ID=" + gradeId;
				}
			}
		);
	});
});

</script>

{/block}
