{extends file=$inh_path}{block name=content}

<h2 class="moduleHeader">{t}User-update menu{/t}</h2>

<fieldset class="smallContainer">
	<legend>{t}Conflicts{/t}</legend>
	{t escape=no open=$openConflictsCount solved=$solvedConflictsCount}<span class="highlighted">%1</span> conflicts open / <span class="highlighted">%2</span> conflicts solved{/t}<br />
	<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|ConflictsResolve">
		{t}Resolve conflicts{/t}
	</a>
</fieldset>

<fieldset class="smallContainer">
	<legend>{t}Actions{/t}</legend>
	<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|ChangesList">
		{t}Display changes-overview{/t}
	</a>
	<br />
	<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|ChangeExecute">
		{t}Execute changes{/t}
	</a>
</fieldset>
<br /><br />

{/block}