{extends file=$base_path}{block name=content}

<h2 class="module-header">{t}Update users with Schoolyear-change{/t}</h2>

<fieldset class="smallContainer">
	<legend>{t}Actions{/t}</legend>
	<ul class="submodulelinkList">

		{if $sessionExists}
		<li>
			<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu">
				{t}Go to the existing update-Process{/t}
			</a>
		</li>
		{/if}
		<li>
			<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession">
				{t}Start a new update-process{/t}
			</a>
		</li>
	</ul>
</fieldset>

{/block}