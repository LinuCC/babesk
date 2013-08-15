{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>
	{_g('Mainmenu of the Classteacher-Module')}
</h2>

<fieldset class="smallContainer">
	<legend>
		{_g('Standard-Actions')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classteachers|Add">
				{_g('Add a Classteacher')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classteachers|Display">
				{_g('Display all Classteachers')}
			</a>
		</li>
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>
		{_g('More Actions')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classteachers|CsvImport">
				{_g('Import Classteachers by a CSV-file')}
			</a>
		</li>
	</ul>
</fieldset>

{/block}
