{extends file=$inh_path}

{block name=popup_dialogs append}

<div id="class-summary-modal" class="modal fade" tabindex="-1" role="dialog"
	aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">{t}Create Class-Summaries{/t}</h4>
			</div>
			<div class="modal-body">
				<form role="form" action="#" method="post">
					<div class="input-group form-group">
						<span class="input-group-addon">
							Anfangsdatum
						</span>
						<input name="class-summary-begin" id="class-summary-begin"
							class="form-control" type="text" placeholder="Anfangsdatum"
							data-provide="datepicker" data-date-format="dd.mm.yyyy"
							data-date-language="de">
					</div>
					<div class="input-group form-group">
						<span class="input-group-addon">
							Enddatum
						</span>
						<input name="class-summary-end" id="class-summary-end"
							class="form-control" type="text" placeholder="Enddatum"
							data-provide="datepicker" data-date-format="dd.mm.yyyy"
							data-date-language="de">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					{t}Cancel{/t}
				</button>
				<button type="button" class="btn btn-primary apply">
					{t}Create{/t}
				</button>
			</div>
		</div>
	</div>
</div>

{/block}


{block name='content'}

<h2 class='module-header'>Hauptmen&uuml; der Kursverwaltung</h2>

<div>
	{if !$isClassRegistrationGloballyEnabled}
		<span class="label label-warning">
			{t}Classregistrations are not allowed{/t}
		</span>
	{else}
		<span class="label label-info">
			{t}Classregistrations are allowed{/t}
		</span>
	{/if}
</div>

<fieldset class="smallContainer">
	<legend>
		{t}Standard-Actions{/t}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|AddClass">
					{t}Add a Class{/t}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|DisplayClasses">
					{t}Display all Classes{/t}
			</a>
		</li>
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>
		{t}More Actions{/t}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|CsvImport|FileUploadForm">
					{t}Import Classes with a CSV-File{/t}
			</a>
		</li>
		<li>
			<a id="class-summary-create" data-toggle="modal" href="#"
				data-target="#class-summary-modal">
					{t}Create Class-Summaries{/t}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|GlobalClassRegistration">
					{t}Change if Classregistrations are enabled{/t}
			</a>
		</li>
	</ul>
</fieldset>
{/block}


{block name="style_include" append}
<link rel="stylesheet" href="{$path_css}/datepicker3.css" type="text/css" />
{/block}


{block name="js_include" append}
<script type="text/javascript" src="{$path_js}/datepicker/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="{$path_js}/datejs/date.min.js"></script>
<script type="text/javascript" src="{$path_js}/datepicker/locales/bootstrap-datepicker.de.js"></script>
<script type="text/javascript">

$(document).ready(function() {

	$('#class-summary-modal button.apply').on('click', function(ev) {
		var startdate = Date.parse($('input[name=class-summary-begin]').val())
			.toString('yyyy-MM-dd');
		var enddate = Date.parse($('input[name=class-summary-end]').val())
			.toString('yyyy-MM-dd');
		window.location.href = 'index.php?module=administrator|Kuwasys|Classes|CreateClassSummary&startdate=' + startdate +
		'&enddate=' + enddate;
	});

	$('div#printDialog').dialog({
		autoOpen: false,
		height: 350,
		width: 350,
		modal: true,

		buttons: {

			"Erstellen": function() {
				var startdate = $('input[name=classSummaryStart]').val();
				var enddate = $('input[name=classSummaryEnd]').val();
				window.location.href = 'index.php?module=administrator|Kuwasys|Classes|CreateClassSummary&startdate=' + startdate +
				'&enddate=' + enddate;
				$(this).dialog("close");
			},

			"Abbrechen": function() {
				$(this).dialog("close");
			}

		}
	});
});

</script>
{/block}

