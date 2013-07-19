{extends file=$schbasSettingsParent}{block name=content}

<form action="index.php?section=Schbas|SchbasSettings&action=previewInfoDocs"	method="post">
	
	
	
		<label>Bitte Jahrgang ausw&auml;hlen:
			<select id="gradeValue" name="gradeValue">			
					<option value="5" selected>5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
			</select>
		</label>
	
	
	<input id="submit"type="submit" value="Vorschau herunterladen" />
</form>

{/block}