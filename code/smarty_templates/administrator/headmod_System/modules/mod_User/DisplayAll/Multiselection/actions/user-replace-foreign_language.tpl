{$langList = $doctrine->getRepository('\\Babesk\\ORM\\SystemGlobalSettings')->findOneByName('foreign_language')}
{if !empty($langList)}
	{$foreignLanguages = explode('|', $langList->getValue())}
	<fieldset>
		<legend>Fremdsprachen verändern (ersetzt vorherige Fremdsprachen)</legend>
			<div class="multiselection-action-view">
				<input type="hidden" name="actionName" value="UserReplaceForeignLanguage">
				<div class="form-group col-sm-10 row">
					<div class="col-sm-12">
						<div class="input-group btn-group">
							<span class="input-group-addon">
								<span class="fa fa-clipboard fa-fw"></span>
							</span>
							<select name="foreign_languages" class="multiselect"
							data-toggle="tooltip" title="Fremdsprachen auswählen" multiple="multiple">
								{foreach $foreignLanguages as $forLan}
									<option value="{$forLan}">{$forLan}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
					<div class="form-group pull-right">
						<button id="action-user-replace-forLan-submit" type="button"
							class="btn btn-warning multiselection-action-submit"
							data-toggle="tooltip" title="Fremdsprache verändern">
								<span class="fa fa-pencil fa-fw"></span>
						</button>
					</div>
		</div>
	</fieldset>

	<script type="text/javascript">
		$('select.multiselect[name="foreign_languages"]').multiselect({
			buttonContainer: '<div class="btn-group" />',
			buttonWidth: '100%'
		});
	</script>
{/if}
