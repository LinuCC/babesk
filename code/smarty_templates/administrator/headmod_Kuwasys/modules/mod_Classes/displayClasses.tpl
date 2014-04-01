{extends file=$inh_path} {block name=filling_content}

<h2 class='moduleHeader'>Die Kurse</h2>

<div class="filter-bar text-center">
	<div>
		<div class="input-group form-group">
			<span class="input-group-addon">
				Kurse für welches Schuljahr?
			</span>
			<select id="schoolyearSelector" class="form-control" name="schoolyearId">
				{foreach $schoolyears as $schoolyearId => $schoolyearLabel}
					<option value="{$schoolyearId}"
						{if $schoolyearId == $activeSchoolyearId}selected="selected"{/if}
						>{$schoolyearLabel}</option>

				{/foreach}
			</select>
		</div>
	</div>
</div>

<table class="table table-striped table-responsive">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Kursleiter</th>
			<th>Aktive Teilnehmer</th>
			<th>Wartende Teilnehmer</th>
			<th>Wünschende Teilnehmer</th>
			<th>Maximale Registrierungen</th>
			<th>Veranstaltungstag</th>
			<th>Optionen</th>
		</tr>
	</thead>
	<tbody>
		{foreach $classes as $class}
		<tr>
			<td>{$class.ID}</td>
			<td>{$class.label}</td>
			<td>{$class.classteacherName}</td>
			<td>{$class.activeCount}</td>
			<td>{$class.waitingCount}</td>
			<td>{$class.request1Count + $class.request2Count}</td>
			<td>{$class.maxRegistration}</td>
			<td>{$class.unitTranslatedName}</td>
			<td>
				<div id='option{$class.ID}'>

				</div>
				<div id='optionButtons{$class.ID}' class="option-buttons">
					<form action="index.php?module=administrator|Kuwasys|Classes|DisplayClassDetails&ID={$class.ID}" method="post">
						<button type="submit" data-toggle="tooltip"
							class="btn btn-info btn-xs" data-title="Kursdetails anzeigen">
							<span class="icon icon-listelements"></span>
						</button>
					</form>
					<form action="index.php?module=administrator|Kuwasys|Classes|ChangeClass&amp;ID={$class.ID}" method="post">
						<button type="submit" data-toggle="tooltip"
							class="btn btn-default btn-xs" data-title="Kurs bearbeiten">
							<span class="icon icon-businesscard"></span>
						</button>
					</form>
					<form action="index.php?module=administrator|Kuwasys|Classes|DeleteClass&amp;ID={$class.ID}" method="post">
						<button type="submit" data-toggle="tooltip"
							class="btn btn-danger btn-xs" data-title="Kurs löschen">
							<span class="icon icon-error"></span>
						</button>
					</form>
				</div>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}


{block name=js_include append}
<script type="text/javascript">

$(document).ready(function() {

	$('#schoolyearSelector').on('change', function(event) {
		var id = $('#schoolyearSelector option:selected').val();
		window.location.href = 'index.php?module=administrator|Kuwasys|\
			Classes|DisplayClasses&schoolyearId=' + id;
	});
});

</script>
{/block}


{block name=style_include append}
<link rel="stylesheet" href="{$path_css}/administrator/Kuwasys/Classes/display-classes.css" type="text/css" />
{/block}