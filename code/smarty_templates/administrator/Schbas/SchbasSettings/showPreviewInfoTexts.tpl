{extends file=$schbasSettingsParent}{block name=content}

<h3 class="module-header">Vorschau Informationsschreiben</h3>

<div class="alert alert-info">
	Die Bücherliste in der Vorschau kann inkorrekt sein.
	Individuelle Anpassungen der Buchausleihzuweisungen werden in der Vorschau
	nicht berücksichtigt, deswegen kann die Vorschau von dem eigentlichen
	Dokument für die Schüler abweichen.
</div>

<form action="index.php?section=Schbas|SchbasSettings&amp;action=previewInfoDocs" method="post">

	<div class="form-group">
		<label>Bitte Jahrgang ausw&auml;hlen:</label>
		<select id="gradelabel" name="gradelabel" class="form-control">
			<option value="5" selected>5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
		</select>
	</div>

	<input id="submit"type="submit" class="btn btn-primary"
		value="Vorschau herunterladen" />
</form>

{/block}
