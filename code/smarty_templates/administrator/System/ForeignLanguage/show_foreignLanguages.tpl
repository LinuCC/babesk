{extends file=$ForeignLanguageParent}{block name=content}

<form action="index.php?section=System|ForeignLanguage&action=2"
	method="post" onsubmit="submit()">
	<fieldset>
		<legend>Fremdsprachen</legend>

		{foreach $foreignLanguages as $foreignLanguage}
			<div class="input-group form-group col-sm-3" data-toggle="tooltip"
			title="Fremdsprache">
				<span class="input-group-addon">
					<span class="fa fa-clipboard fa-fw"></span>
				</span>
				<input type="text" name="foreignLanguages[]" class="form-control"
				value="{$foreignLanguage}" />
			</div>
		{/foreach}
		<div class="input-group form-group col-sm-3" data-toggle="tooltip"
		title="Neue Fremdsprache hinzufügen">
			<span class="input-group-addon">
				<span class="fa fa-clipboard fa-fw"></span>
			</span>
			<input type="text" name="foreignLanguages[]" class="form-control"
			value="" size="2" />
		</div>
	</fieldset>
	<input class="btn btn-primary" id="submit" onclick="submit()" type="submit" value="Speichern" />
</form>

{/block}