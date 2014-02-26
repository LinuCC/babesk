{extends file=$inh_path}{block name=content}

<h2 class="moduleHeader">{t}Select schoolyear{/t}</h2>

{if count($schoolyears)}
<form class="simpleForm" action="" method="post">
	<div class="simpleForm">
		<label class="simpleForm" for="schoolyear">
			{t}Please select a schoolyear to switch to when changes get applied:{/t}
		</label>
		{html_options name="schoolyear" options=$schoolyears class="inputItem"}
	</div>

	<div class="simpleForm" title="{t}When full-year switch get selected, it will be assumed that the normal behaviour is that the users move one gradelevel up.{/t}">
		<p class="simpleForm">
			{t}Is it a half-year switch or a full-year switch?{/t}
		</p>
		{html_options name="switchType" options=$switchTypes selected=0
			class="inputItem"}
	</div>

	<div class="simpleForm">
			<p class="simpleForm">
				{t}To which usergroup should new users be assigned?{/t}
			</p>
			{if count($usergroups)}
				<select name="usergroup" class="inputItem">
					<option value="0" class="inputItem option" selected="selected">
						{t}None{/t}
					</option>
					{foreach $usergroups as $group}
						<option value="{$group->getId()}" class="inputItem option">
							{$group->getName()}
						</option>
					{/foreach}
				</select>
			{else}
				<p class="simpleForm">
					{t}No user-groups found.{/t}
				</p>
			{/if}
	</div>

	<input id="submit" type="submit" name="schoolyearSelected"
		value="{t}continue{/t}"
	/>
</form>
{else}
{t}There is no schoolyear you can switch to. Please add a schoolyear and then try again.{/t}
<form action="index.php?module=administrator|System|Schoolyear&action=addSchoolYear" method="post">
	<input type="submit" value="{t}Add a schoolyear{/t}" />
</form>
{/if}


{/block}