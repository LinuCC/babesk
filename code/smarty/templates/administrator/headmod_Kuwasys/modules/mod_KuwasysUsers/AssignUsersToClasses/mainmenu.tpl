{extends file=$inh_path}{block name="content"}

<h2 class="moduleHeader">
	Den Schülern ihre Kurse zuweisen
</h2>

<fieldset class="smallContainer">
	<legend>{_g('Actions')}</legend>
	<ul class="submodulelinkList">

		{if $tableExists}
		<li>
			<a href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Reset">
				{_g('Go to the existing Assignment-Process')}
			</a>
		</li>
		{/if}

		<li>
			<a href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Reset">
				{_g('Start a new Assignment-Process')}
			</a>
		</li>

	</ul>
</fieldset>

{/block}
