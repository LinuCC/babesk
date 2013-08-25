{extends file=$inh_path} {block name="content"}

<h2 class="moduleHeader">
	{_g('Kuwasys User-Mainmenu')}
</h2>

<fieldset class="smallContainer">
	<legend>
		{_g('Printables')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a class="submodulelink" href="index.php?module=administrator|Kuwasys|KuwasysUsers|PrintParticipationConfirmation">
				{_g('Print Participation Confirmation')}
			</a>
		</li>
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>
		{_g('Bulk-Changes')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a class="submodulelink" href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses"
			title="{_g('Here you can assign the Users that submitted requests to Classes of the active Year')}">
				{_g('Assign the Users to Classes')}
			</a>
		</li>
	</ul>
</fieldset>

{/block}
