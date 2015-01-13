{extends file=$inh_path} {block name=filling_content}

<h2 class='module-header'>Kursauflistung</h2>

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
			<th>Ist Optional?</th>
			<th>Maximale Registrierungen</th>
			<th>Veranstaltungstag</th>
			<th>Optionen</th>
		</tr>
	</thead>
	<tbody>
		{foreach $classes as $class}
		<tr {if $class->getIsOptional()}class="info"{/if}>
			<td>{$class->getID()}</td>
			<td>{$class->getLabel()}</td>
			<td>
				{foreach $class->getClassteachers() as $classteacher}
					<p>{$classteacher->getForename()} {$classteacher->getName()}</p>
				{/foreach}
			</td>
			<td>
				{$activeUserChoicesCount = 0}
				{foreach $class->getUsersInClassesAndCategories() as $userChoice}
					{if $userChoice->getStatus()->getName() == "active"}
						{$activeUserChoicesCount = $activeUserChoicesCount + 1}
					{/if}
				{/foreach}
				{$activeUserChoicesCount}
			</td>
			<td>
				{$waitingUserChoicesCount = 0}
				{foreach $class->getUsersInClassesAndCategories() as $userChoice}
					{if $userChoice->getStatus()->getName() == "waiting"}
						{$waitingUserChoicesCount = $waitingUserChoicesCount + 1}
					{/if}
				{/foreach}
				{$waitingUserChoicesCount}
			</td>
			<td>
				{$requestingUserChoicesCount = 0}
				{foreach $class->getUsersInClassesAndCategories() as $userChoice}
					{if $userChoice->getStatus()->getName() == "request1" ||
							$userChoice->getStatus()->getName() == "request2"}
						{$requestingUserChoicesCount = $requestingUserChoicesCount + 1}
					{/if}
				{/foreach}
				{$requestingUserChoicesCount}
			</td>
			<td>{if $class->getIsOptional() == 0}nein{else}ja{/if}</td>
			<td>{$class->getMaxRegistration()}</td>
			<td>
				{foreach $class->getCategories() as $category}
					<p>{$category->getTranslatedName()}</p>
				{/foreach}
			</td>
			<td>
				<div id='option{$class->getId()}'>

				</div>
				<div id='optionButtons{$class->getId()}' class="option-buttons">
					<a href="index.php?module=administrator|Kuwasys|Classes|DisplayClassDetails&amp;ID={$class->getId()}" data-toggle="tooltip"class="btn btn-info btn-xs"
						data-title="Kursdetails anzeigen">
						<span class="icon icon-listelements"></span>
					</a>
					{*TODO: categoryId in link benötigt oder kann wech?*}
					<a href="index.php?module=administrator|Kuwasys|Classes|ChangeClass&amp;ID={$class->getId()}{*&amp;categoryId={$class->getcategoryId}*}"
						data-toggle="tooltip" class="btn btn-default btn-xs"
						data-title="Kurs bearbeiten">
						<span class="icon icon-businesscard"></span>
					</a>
					<a href="index.php?module=administrator|Kuwasys|Classes|DeleteClass&amp;ID={$class->getId()}"
						data-toggle="tooltip" disabled
						class="btn btn-danger btn-xs" data-title="Kurs löschen">
						<span class="icon icon-error"></span>
					</a>
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