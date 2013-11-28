{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Hauptmen&uuml; der Kursverwaltung</h2>

<div>
	{if !$isClassRegistrationGloballyEnabled}
		<p>
			{_g('Classregistrations are not allowed')}
		</p>
	{else}
		<p>
			{_g('Classregistrations are allowed')}
		</p>
	{/if}
</div>

<fieldset class="smallContainer">
	<legend>
		{_g('Standard-Actions')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|AddClass">
					{_g('Add a Class')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|DisplayClasses">
					{_g('Display all Classes')}
			</a>
		</li>
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>
		{_g('More Actions')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|CsvImport">
					{_g('Import Classes with a CSV-File')}
			</a>
		</li>
		<li>
			<a id="createClassSummaries" href="index.php?module=administrator|Kuwasys|Classes|CreateClassSummary">
					{_g('Create Class-Summaries')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|GlobalClassRegistration">
					{_g('Change if Classregistrations are enabled')}
			</a>
		</li>
	</ul>
</fieldset>

<div class="dialog" id="printDialog" title="Kursübersichten">
	<p>Bitte wählen sie das Anfangs- und Enddatum, zwischen denen der Kurs stattfindet (Anfang und Ende des Schuljahres)</p>
	<form>
		<fieldset>
			<label for="classSummaryStart">Anfangsdatum</label>
			<input type="text" size="10" name="classSummaryStart" />
		</fieldset>
		<fieldset>
			<label for="classSummaryEnd">Enddatum</label>
			<input type="text" size="10" name="classSummaryEnd" />
		</fieldset>
	</form>
</div>

<script type="text/javascript">

$(document).ready(function() {

	$('input[name=classSummaryStart]').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: "1920:+10"
	});

	$('input[name=classSummaryEnd]').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: "1920:+10"
	});

	$('#createClassSummaries').on('click', function(event) {
		event.preventDefault();
		$('div#printDialog').dialog('open');
	});

	$('div#printDialog').dialog({
		autoOpen: false,
		height: 350,
		width: 350,
		modal: true,

		buttons: {

			"Drucken": function() {
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
