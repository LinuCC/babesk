{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Hauptmen&uuml; der Kursverwaltung</h2>

<div>
	{if !$isClassRegistrationGloballyEnabled}
		<p>
			{t}Classregistrations are not allowed{/t}
		</p>
	{else}
		<p>
			{t}Classregistrations are allowed{/t}
		</p>
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
			<a id="createClassSummaries" href="index.php?module=administrator|Kuwasys|Classes|CreateClassSummary">
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
