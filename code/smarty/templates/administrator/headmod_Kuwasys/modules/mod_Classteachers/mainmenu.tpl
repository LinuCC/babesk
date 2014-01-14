{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>
	{t}Mainmenu of the Classteacher-Module{/t}
</h2>

<fieldset class="smallContainer">
	<legend>
		{t}Standard-Actions{/t}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classteachers|Add">
				{t}Add a Classteacher{/t}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classteachers|Display">
				{t}Display all Classteachers{/t}
			</a>
		</li>
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>
		{t}More Actions{/t}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classteachers|CsvImport">
				{t}Import Classteachers by a CSV-file{/t}
			</a>
		</li>
	</ul>
</fieldset>

{/block}
