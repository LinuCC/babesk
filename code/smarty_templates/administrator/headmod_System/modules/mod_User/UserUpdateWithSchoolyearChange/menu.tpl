{extends file=$inh_path}{block name=content}

<h2 class="module-header">{t}User-update menu{/t}</h2>

<fieldset class="smallContainer">
	<legend>{t}Conflicts{/t}</legend>

	<span class="highlighted">{$openConflictsCount}</span>
	{t}conflicts open{/t} /
	<span class="highlighted">{$solvedConflictsCount}</span>
	{t}conflicts resolved{/t}<br />

	<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu|ConflictsResolve">
		{t}Resolve conflicts{/t}
	</a>
</fieldset>

<fieldset class="smallContainer">
	<legend>{t}Actions{/t}</legend>

	<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu|ChangesList">
		{t}Display changes-overview{/t}
	</a><br />

	<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|ChangeExecute">
		{t}Execute changes{/t}
	</a>
</fieldset>

{/block}