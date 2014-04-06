{extends file=$inh_path}{block name=content}

<h2>Kursliste</h2>

<div id="selector-container">
	{foreach $classUnits as $classUnit}
		<div class="panel panel-primary bg-fit unit-panel"
		unitId="{$classUnit.ID}">
			<div class="panel-heading">
				<div class="panel-title">
					<button type="button"
						class="btn btn-sm btn-default expand-button-content"
						data-toggle="collapse" data-parent=""
						href="#unit-accordion-body_{$classUnit.ID}"
						{if $classUnit.votedCount}disabled{/if}>
						<div class="icon icon-plus"></div>
					</button>
					{$classUnit.translatedName}
					{if $classUnit.votedCount}
						<span class="label label-info pull-right">bereits gewählt</span>
					{/if}
				</div>
			</div>
			<div id="unit-accordion-body_{$classUnit.ID}" class="unit-container-body collapse">
				<div class="panel-body">
					<div class="panel-group unit-container" id="unitAccordion_{$classUnit.ID}">
						{foreach $classes as $class}
							{if $class.unitId == $classUnit.ID}
								<div classId="{$class.ID}"
									class="panel panel-default class-container">
									<div class="panel-heading">
										<div class="col-xs-7 col-sm-8 col-md-9">
											<button type="button" class="btn btn-sm btn-default expand-button-content"
												data-toggle="collapse"
												data-parent="#unitAccordion_{$classUnit.ID}"
												href="#class-accordion-body_{$class.ID}">
												<div class="icon icon-plus"></div>
											</button>
											<h4 class="panel-title">
												{$class.label}
													{if !$class.registrationEnabled}
														<span class="label label-danger">deaktiviert</span>
													{/if}
													{if $classUnit.votedCount}
														<span class="label label-info">bereits am Tag gewählt</span>
													{/if}

											</h4>
										</div>
										<div class="col-xs-5 col-sm-4 col-md-3">
											<div class="btn-group pull-right selection-buttons">
													<button type="button" classId="{$class.ID}"
													category="request1"
													class="btn btn-sm btn-success to-primary
													{if !$class.registrationEnabled || $classUnit.votedCount}disabled{/if}">
														Erstwahl
													</button>
													<button type="button" classId="{$class.ID}"
													category="request2"
													class="btn btn-sm btn-info to-secondary
													{if !$class.registrationEnabled || $classUnit.votedCount}disabled{/if}">
														Zweitwahl
													</button>
													<button type="button" classId="{$class.ID}"
													class="btn btn-sm btn-danger disabled to-disabled">
														<div class="icon-error icon icon-btn-sm"></div>
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
{/block}