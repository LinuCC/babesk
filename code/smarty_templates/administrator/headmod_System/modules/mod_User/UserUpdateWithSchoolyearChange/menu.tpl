{extends file=$base_path}{block name=content}

<h2 class="module-header">{t}User-update menu{/t}</h2>


<div class="text-center">
	<div>
		<span class="label label-danger">{$openConflictsCount}</span>
		{t}conflicts open{/t} /
		<span class="label label-success">{$solvedConflictsCount}</span>
		{t}conflicts resolved{/t}
	</div>
</div>

<fieldset class="smallContainer">
	<legend>{t}Actions{/t}</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu|ConflictsResolve">
				{t}Resolve conflicts{/t}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu|ChangesList">
				{t}Display changes-overview{/t}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|ChangeExecute">
				{t}Execute changes{/t}
			</a>
		</li>
	</ul>
</fieldset>

{/block}