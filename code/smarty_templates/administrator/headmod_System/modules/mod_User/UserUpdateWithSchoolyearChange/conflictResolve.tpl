{extends file=$inh_path}{block name=content}

<h2 class="module-header">{t}Resolve conflicts{/t}</h2>

{$lastType = ''}
{$listStarted = false}

{if count($conflicts)}
<button id="YesToAllConflicts">{t}Yes to all{/t}</button>
<form id="conflictForm" action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu|ConflictsResolve" method="post">

	{foreach $conflicts as $conflict}
		{if not empty($conflict.birthday)}
			{$conflict.birthday = date('d.m.Y', strtotime($conflict.birthday))}
		{else}
			{$conflict.birthday = '---'}
		{/if}
		{if $conflict.type != $lastType}
			{if $listStarted}
				{*Stop old list if started*}
				</ul>
				</fieldset>
			{/if}
			{*Begin a new list*}
			<fieldset class="smallContainer">
			<legend>
				{if $conflict.type == "CsvOnlyConflict"}
					{t}User not found in program{/t}
				{elseif $conflict.type == "DbOnlyConflict"}
					{t}User existing but not found in new file{/t}
				{elseif $conflict.type == "GradelevelConflict"}
					{t}Users old and new gradelevels differ too much{/t}
				{/if}
			</legend>
			<ul class="inputFormList">
			{$listStarted = true}
		{/if}
		<li>
			{if $conflict.type == "CsvOnlyConflict"}
				{t escape=no forename=$conflict.forename name=$conflict.name birthday=$conflict.birthday newGrade=$conflict.newGrade}The user <span class="highlighted">"%1 %2"</span> (birthday: <span class="highlighted">"%3"</span>), that will be in grade <span class="highlighted">"%3"</span>, is new. Is that correct?{/t}
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
				{t escape=no forename=$conflict.forename name=$conflict.name birthday=$conflict.birthday}The user <span class="highlighted">"%1 %2"</span> (birthday: <span class="highlighted">"%3"</span>) is not in the upcoming schoolyear. Is that correct?{/t}<br />
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
				{t escape=no forename=$conflict.forename name=$conflict.name birthday=$conflict.birthday origGrade=$conflict.origGrade newGrade=$conflict.newGrade}The grade of the user <span class="highlighted">"%1 %2"</span> (birthday: <span class="highlighted">"%3"</span>) changed from "%4" to "%5". Is that correct?{/t}<br />

				<button id="Yes_{$conflict.userId}_{$conflict.type}"
					class="conflict_{$conflict.type}  conflictAnswerYes"
					conflictId="{$conflict.conflictId}" >
					{t}Yes{/t}
				</button>

				<button id="No_{$conflict.userId}_{$conflict.type}"
					conflictType="{$conflict.type}"
					class="conflict_{$conflict.type} conflictAnswerNo"
					conflictId="{$conflict.conflictId}"
					username="{$conflict.forename} {$conflict.name}" >
					{t}No{/t}
				</button>
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
{else}{*no conflicts existing*}
{t}No conflicts exist.{/t}
{$backlink = "index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu"}
{/if}


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


{/block}

{block name=js_include append}

<script src="{$path_js}/administrator/System/User/UserUpdateWithSchoolyearChange/conflictResolve.js" type="text/javascript"></script>

{/block}