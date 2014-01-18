{extends file=$inh_path}{block name=content}

<h2 class="moduleHeader">{t}Resolve conflicts{/t}</h2>

{$lastType = ''}
{$listStarted = false}

<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu|ConflictsResolve" method="post">

	{foreach $conflicts as $conflict}
		{if $conflict.type != $lastType}
			{if $listStarted}
				{*Stop old list if started*}
				</ul>
				</fieldset>
			{/if}
			{*Begin a new list*}
			<fieldset class="smallContainer">
			<legend>{$conflict.type}</legend>
			<ul class="inputFormList">
			{$listStarted = true}
		{/if}

		<li>
			{if $conflict.type == "CsvOnlyConflict"}
				{t escape=no forename=$conflict.forename name=$conflict.name}The user <span class="highlighted">"%1 %2"</span> is new. Is that correct?{/t}
				<br />
				<button id="Yes_{$conflict.userId}_{$conflict.type}"
				class="conflict_{$conflict.type} conflictAnswerYes" conflictId="{$conflict.conflictId}" >
					{t}Yes{/t}
				</button>
				<button id="No_{$conflict.userId}_{$conflict.type}" conflictType="{$conflict.type}"
				class="conflict_{$conflict.type}  conflictAnswerNo" conflictId="{$conflict.conflictId}" username="{$conflict.forename} {$conflict.name}" >
					{t}No{/t}
				</button>
			{/if}
			{if $conflict.type == "DbOnlyConflict"}
				{t escape=no forename=$conflict.forename name=$conflict.name}The user <span class="highlighted">"%1 %2"</span> is not in the upcoming schoolyear. Is that correct?{/t}<br />
				<button id="Yes_{$conflict.userId}_{$conflict.type}"
				class="conflict_{$conflict.type}  conflictAnswerYes" conflictId="{$conflict.conflictId}" >
					{t}Yes{/t}
				</button>
				<button id="No_{$conflict.userId}_{$conflict.type}" conflictType="{$conflict.type}"
				class="conflict_{$conflict.type}  conflictAnswerNo" conflictId="{$conflict.conflictId}" username="{$conflict.forename} {$conflict.name}" >
					{t}No{/t}
				</button>
			{/if}
			{if $conflict.type == "GradelevelConflict"}
				{t escape=no forename=$conflict.forename name=$conflict.name origGrade=$conflict.origGrade newGrade=$conflict.newGrade}The grade of the user <span class="highlighted">"%1 %2"</span> changed from "%3" to "%4". Is that correct?{/t}<br />
				<button id="Yes_{$conflict.userId}_{$conflict.type}"
				class="conflict_{$conflict.type}  conflictAnswerYes" conflictId="{$conflict.conflictId}" >
					{t}Yes{/t}
				</button>
				<button id="No_{$conflict.userId}_{$conflict.type}" conflictType="{$conflict.type}"
				class="conflict_{$conflict.type} conflictAnswerNo" conflictId="{$conflict.conflictId}" username="{$conflict.forename} {$conflict.name}" >
					{t}No{/t}
			{/if}
		</li>

		{$lastType = $conflict.type}
	{/foreach}

	{if $listStarted}
		</ul>
		</fieldset>
	{/if}

	<input type="submit" name="change" value="{t}Submit changes{/t}" />
	<input type="submit" name="cancel" value="{t}Cancel changes{/t}" />

</form>

<script type="text/javascript">
	var translations = {
		"answeredWithYes": "{t}Answered with yes{/t}",
		"answeredWithNo": "{t}Answered with no{/t}",
		"changedToUser": "{t}changed to user: {/t}",
		"newGradeInput": "{t}Please give the correct new grade of the user:{/t}",
		"finished": "{t}finished{/t}",
		"newGradeWillBe": "{t}The new grade of the user will be:{/t}"
	};
</script>

<script src="../smarty/templates/administrator/headmod_System/modules/mod_User/UserUpdateWithSchoolyearChange/conflictResolve.js" type="text/javascript"></script>

{/block}