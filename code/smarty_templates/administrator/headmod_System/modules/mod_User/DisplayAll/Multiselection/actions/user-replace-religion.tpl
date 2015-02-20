<fieldset>
	{$religionlist = $doctrine->getRepository('\\Babesk\\ORM\\SystemGlobalSettings')->findOneByName('religion')}
	{if !empty($religionlist)}
		{$religions = explode('|', $religionlist->getValue())}
	{/if}
	<legend>Religion verändern (ersetzt vorherige Religion(-en))</legend>
		<div class="multiselection-action-view">
			<input type="hidden" name="actionName" value="UserReplaceReligion">
			<div class="form-group col-sm-10 row">
				<div class="col-sm-12">
					<div class="input-group" data-toggle="tooltip" title="Religion auswählen">
						<span class="input-group-addon">
							<span class="fa fa-user"></span>
						</span>
						<select name="religion" class="form-control">
							{foreach $religions as $rel}
								<option value="{$rel}">{$rel}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div class="form-group pull-right">
				<button id="action-user-replace-religion-submit" type="button"
					class="btn btn-warning multiselection-action-submit"
					data-toggle="tooltip" title="Religionen verändern">
						<span class="icon icon-edit"></span>
				</button>
			</div>
		</div>
</fieldset>