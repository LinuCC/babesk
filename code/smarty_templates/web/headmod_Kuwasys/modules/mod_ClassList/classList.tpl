{extends file=$inh_path}{block name=content}

{$hasOpenClasses = false}
{foreach $classes as $class}
	{if $class.isOptional}{$hasOpenClasses = true}{/if}
{/foreach}

{if $hasOpenClasses}
	<h3>offene Ganztagsangebote</h3>
	<p class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		Offene Ganztagsangebote sind optional und können unabhängig von den normalen Kursangeboten gewählt werden. Bitte achte darauf, dass sich die Ganztagsangebote nicht mit von dir gewählten Kursen überschneiden.
	</p>

	<div id="open-class-container">
		<table id="open-class-table" class="table table-hover table-striped
			table-condensed table-bordered">
			<thead>
				<tr>
					<th>Titel</th>
					{foreach $classCategories as $classCategory}
						<th>
							{$classCategory.translatedName}
						</th>
					{/foreach}
				</tr>
			</thead>
			<tbody>
				{foreach $classes as $class}
					{if $class.isOptional}
						<tr>
							<td>
								<p>{$class.label}</p>
								<button type="button" class="btn btn-info"
									data-toggle="popover" title="{$class.classteacher}"
									data-content="{$class.description}">
									Informationen
								</button>
							</td>
							{foreach $classCategories as $classCategory}
								{* Check if user has applied for this class and category *}
								{$classId = $class.ID}{$categoryId = $classCategory.ID}
								{$applianceData = $classAppliance.$classId.$categoryId}
								{$applied = $applianceData.hasApplied}
								<td class="open-class-selector" data-class="{$class.ID}"
									data-category="{$classCategory.ID}"
									data-apply="{if $applied}true{else}false{/if}">
									<span class="label
										{if $applied}label-success{else}label-default{/if}">
										{if $applied}Teilnahme{else}keine Teilnahme{/if}
									</span>
									<button class="apply-button btn btn-default">
										{if $applied}nicht teilnehmen{else}Teilnehmen{/if}
									</button>
								</td>
							{/foreach}
						</tr>
					{/if}
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}

<h3>Kursliste</h3>

<div id="selector-container">
	{foreach $classCategories as $classCategory}
		<div class="panel panel-primary bg-fit unit-panel"
		unitId="{$classCategory.ID}">
			<div class="panel-heading">
				<div class="panel-title">
					<button type="button"
						class="btn btn-sm btn-default expand-button-content"
						data-toggle="collapse" data-parent=""
						href="#unit-accordion-body_{$classCategory.ID}"
						{if $classCategory.votedCount}disabled{/if}>
						<div class="icon icon-plus"></div>
					</button>
					{$classCategory.translatedName}
					{if $classCategory.votedCount}
						<span class="label label-info pull-right">bereits gewählt</span>
					{/if}
				</div>
			</div>
			<div id="unit-accordion-body_{$classCategory.ID}" class="unit-container-body collapse">
				<div class="panel-body">
					<div class="panel-group unit-container" id="unitAccordion_{$classCategory.ID}">
						{foreach $classes as $class}
							{if $class.unitId == $classCategory.ID && $class.isOptional == 0}
								<div classId="{$class.ID}"
									class="panel panel-default class-container">
									<div class="panel-heading">
										<div class="col-xs-7 col-sm-8 col-md-9">
											<button type="button" class="btn btn-sm btn-default expand-button-content"
												data-toggle="collapse"
												data-parent="#unitAccordion_{$classCategory.ID}"
												href="#class-accordion-body_{$class.ID}">
												<div class="icon icon-plus"></div>
											</button>
											<h4 class="panel-title">
												{$class.label}
													{if !$class.registrationEnabled}
														<span class="label label-danger">deaktiviert</span>
													{/if}
													{if $classCategory.votedCount}
														<span class="label label-info">
															bereits am Tag gewählt
														</span>
													{/if}

											</h4>
										</div>
										<div class="col-xs-5 col-sm-4 col-md-3">
											<div class="btn-group pull-right selection-buttons">
													<button type="button" classId="{$class.ID}"
													category="request1"
													class="btn btn-sm btn-success to-primary
													{if !$class.registrationEnabled || $classCategory.votedCount}disabled{/if}">
														Erstwahl
													</button>
													<button type="button" classId="{$class.ID}"
													category="request2"
													class="btn btn-sm btn-info to-secondary
													{if !$class.registrationEnabled || $classCategory.votedCount}disabled{/if}">
														Zweitwahl
													</button>
													<button type="button" classId="{$class.ID}"
													class="btn btn-sm btn-danger disabled to-disabled">
														<div class="fa fa-minus icon-btn-sm"></div>
													</button>
												</div>
										</div>
										<div class="clearfix"></div>
									</div>
									<div id="class-accordion-body_{$class.ID}"
										class="panel-collapse collapse class-container-body">
										<div class="panel-body">
											{if isset($class.classteacher)}
												<span class="label label-default">
													{$class.classteacher}
												</span>
											{/if}
											<div class="quotebox">
													{$class.description}
											</div>
										</div>
									</div>
								</div>
							{/if}
						{/foreach}
					</div>
				</div>
			</div>
		</div>
	{/foreach}

	<button type="button" class="btn btn-primary submit-button">
		Wahlen ausführen
	</button>
	<a type="button" class="btn btn-default"
		href="{if $backlink}{$backlink} {else}javascript: history.go(-1){/if}">
		Zurück
	</a>
	<button type="button" class="btn btn-info pull-right" data-toggle="popover"
	id="class-deactivated-info" title="Warum sind Kurse deaktiviert?"
	data-content="Für deaktivierte Kurse kann sich der Benutzer nicht mehr anmelden. Entweder diese Kurse sind voll, erlauben generell keine Anmeldungen oder sie haben sich für diesen Veranstaltungstag schon bei anderen Kursen angemeldet."
	data-placement="left" >
		Hilfe zu deaktivierten Kursen
	</button>

	</p>
	</div>
</div>
{/block}

{block name="js_include" append}

<script type="text/javascript" src="{$path_js}/web/Kuwasys/classlist.js"></script>

<script type="text/javascript">

$(document).ready(function() {

	$('div.classDescription').hide();

	$('table.classlist tr th').on('click', function(ev) {
		event.preventDefault();
		$(this).children('div.classDescription').toggle();
	});

	$('input.classListCheckbox').on('click', function(event) {

		var nameBeginning = $(this).attr('name').replace(/Choice.*/, 'Choice');

		$(this).parent().siblings().children('input').attr('checked', false);
		$(this).parents('table').
			find('input.classListCheckbox[name^=' + nameBeginning + ']')
			.not($(this)).attr('checked', false);
	});
});

</script>
{/block}

{block name="style_include" append}
<link rel="stylesheet" href="{$path_css}/web/Kuwasys/main.css"
type="text/css" />

<link rel="stylesheet" href="{$path_css}/web/Kuwasys/ClassList/classlist.css"
type="text/css" />
{/block}