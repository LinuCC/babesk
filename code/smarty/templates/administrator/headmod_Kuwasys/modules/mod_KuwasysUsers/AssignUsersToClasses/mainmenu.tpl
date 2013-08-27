{extends file=$inh_path}{block name="content"}

<h2 class="moduleHeader">
	Den Schülern ihre Kurse zuweisen
</h2>

<fieldset class="smallContainer">
	<legend>{_g('Actions')}</legend>
	<ul class="submodulelinkList">

		{if $tableExists}
		<li>
			<a href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Overview">
				{_g('Go to the existing Assignment-Process')}
			</a>
		</li>
		{/if}

		<li>
			<a id="resetAssignment"
			href="#">
				{_g('Start a new Assignment-Process')}
			</a>
		</li>

	</ul>
</fieldset>

<div id="confirmReset" title="{_g('Really start a new Assignment-Process?')}">
  <p>{_g('If you have already started an Assignment-Process, the Data will be inevitably lost. Are you sure?')}</p>
</div>
<script src="../smarty/templates/administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/mainmenu.js">
</script>
{/block}
