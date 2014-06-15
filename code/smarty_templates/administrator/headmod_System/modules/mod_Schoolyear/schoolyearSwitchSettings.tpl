{extends file=$inh_path} {block name='content'}

<h2>
	Schuljahreswechsel
</h2>

<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<strong>Veraltet!</strong> Diese Funktionalität ist veraltet und könnte nicht das gewünschte Ergebnis ergeben!
</div>

<form action="index.php?module=administrator|System|Schoolyear|SwitchSchoolyear|Upload"
	method="POST">
	<fieldset class="smallContainer">
		<legend>Bitte wählen sie ein Schuljahr aus, zu dem gewechselt wird:</legend>
		<select name="schoolyearId">
			{foreach $schoolyears as $schoolyear}
			<option value="{$schoolyear.ID}">
				{$schoolyear.label}
			</option>
			{/foreach}
		</select>
	</fieldset>
	<fieldset class="smallContainer">
		<legend>Einstellungen:</legend>
		<div class="simpleForm">
			<p class="inputItem">Höchster Jahrgang:</p>
			<input class="inputItem" type="text" maxlength="2" size="2"
				name="highestGradelevel"
				title="Wenn Schüler in einen höheren als den angegebenen Jahrgang versetzt werden würden, dann werden sie nicht mehr in das Schuljahr gesetzt. Bei 12 Jahrgängen würde normalerweise hier eine 12 eingegeben werden." />
		</div>
		<div class="simpleForm">
			<p class="inputItem">Klasse erstellen, wenn nicht vorhanden:</p>
			<input class="inputItem" type="checkbox"
			name="shouldCreateClassesIfNotExist" value="yes"
			title="Wenn Schüler in Klassen versetzt werden, die es in dem System nicht gibt, werden die Klassen automatisch hinzugefügt wenn diese Checkbox aktiv ist [Kann unter Umständen lange dauern]. Ansonsten wird bei fehlender Klasse eine Fehlermeldung angezeigt." />
		</div>
	</fieldset>
	<input type="submit" value="Schuljahr wechseln und alle Schüler eine Jahrgangsstufe hochsetzen" />
</form>

<script type="text/javascript">
	$(document).tooltip();
	$(document).ready(function() {
		adminInterface.warningShow(
			'Dies überträgt die Schüler, die in diesem Schuljahr sind, in das\
			nächste Schuljahr und versetzt sie in den nächsthöheren Jahrgang.\
			Schüler, die sitzengeblieben sind / überspringen oder\
			anderes müssen nach diesem Vorgang manuell verändert werden.');
	});
</script>

{/block}
