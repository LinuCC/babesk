{extends file=$base_path}{block name=content}

<h3 class="module-header">{t}Select schoolyear{/t}</h3>

{if count($schoolyears)}
<form class="form-horizontal" action="" method="post">
	<div class="form-group" data-toggle="tooltip"
		title="{t}Please select a schoolyear to switch to when changes get applied:{/t}">
		<div class="col-md-2">
			<label for="schoolyear" class="control-label">
				Schuljahr
			</label>
		</div>
		<div class="col-md-10 ">
			<div class="input-group">
				<span class="input-group-addon">
					<span class="icon icon-counter"></span>
				</span>
				<select id="schoolyear" class="form-control" name="schoolyear">
					{foreach $schoolyears as $key => $sy}
						<option value="{$key}">{$sy}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>

	<div class="form-group" data-toggle="tooltip" title="{t}When full-year switch get selected, it will be assumed that the normal behaviour is that the users move one gradelevel up.{/t}">
		<div class="col-md-2">
			<label for="switchType" class="control-label">
				Schuljahreswechseltyp
			</label>
		</div>
		<div class="col-md-10">
			<div class="input-group">
			<span class="input-group-addon">
				<span class="icon icon-refresh"></span>
			</span>
			<select id="switchType" class="form-control" name="switchType">
				{foreach $switchTypes as $key => $st}
					<option value="{$key}" {if $st@first}selected{/if}>{$st}</option>
				{/foreach}
			</select>
			</div>
		</div>
	</div>

	<div class="form-group" data-toggle="tooltip"
		title="Die Nutzergruppe, die neuen Benutzern zugewiesen wird">
		<div class="col-md-2">
			<label for="usergroup" class="control-label">
				Gruppe neuer Nutzer
			</label>
		</div>
		<div class="col-md-10">
			{if count($usergroups)}
				<div class="input-group">
					<span class="input-group-addon">
						<span class="icon icon-user"></span>
					</span>
					<select name="usergroup" class="form-control">
						<option value="0" class="option" selected="selected">
							{t}None{/t}
						</option>
						{foreach $usergroups as $group}
							<option value="{$group->getId()}" class="option">
								{$group->getName()}
							</option>
						{/foreach}
					</select>
				</div>
			{else}
				<p class="alert alert-warning">
					{t}No user-groups found.{/t}
				</p>
			{/if}
		</div>
	</div>

	<input id="submit" type="submit" class="btn btn-default"
		name="schoolyearSelected" value="{t}continue{/t}"
	/>
</form>
{else}
{t}There is no schoolyear you can switch to. Please add a schoolyear and then try again.{/t}
<form action="index.php?module=administrator|System|Schoolyear&action=addSchoolYear" method="post">
	<input type="submit" value="{t}Add a schoolyear{/t}" />
</form>
{/if}


{/block}