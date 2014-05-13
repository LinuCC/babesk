<fieldset>
	{$religionlist = $doctrine->getRepository('\\Babesk\\ORM\\SystemGlobalSettings')->findOneByName('religion')}
	{if !empty($religionlist)}
		{$religions = explode('|', $religionlist->getValue())}
	{/if}
	<legend>Religion verändern (ersetzt vorherige Religion(-en))</legend>
		<div class="multiselection-action-view">
			<input type="hidden" name="actionName" value="UserReplaceReligion">
			<div class="form-group col-sm-6">
				<select name="religion" class="form-control">
					{foreach $religions as $rel}
						<option value="{$rel}">{$rel}</option>
					{/foreach}
					}
				</select>
			</div>
			<div class="form-group pull-right">
				<button id="action-user-replace-religion-submit" type="button"
					class="btn btn-sm btn-warning multiselection-action-submit">
						Verändern
				</button>
			</div>
		</div>
</fieldset>