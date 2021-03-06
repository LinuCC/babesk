{extends file=$inh_path}{block name="content"}

<h2 class="module-header">
	Den Schülern ihre Kurse zuweisen
</h2>

<fieldset class="smallContainer">
	<legend>{t}Actions{/t}</legend>
	<ul class="submodulelinkList">

		{if $tableExists}
		<li>
			<a href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Overview">
				{t}Go to the existing Assignment-Process{/t}
			</a>
		</li>
		{/if}
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>{t}Bulk-Actions{/t}</legend>
	<ul class="submodulelinkList">
		<li>
			<a id="resetAssignment" href="#">
				{t}Start a new Assignment-Process{/t}
			</a>
		</li>
		{if $tableExists}
		<li>
			<a id="applyAssignment" href="#">
				{t}Assign all Users to their Classes{/t}
			</a>
		</li>
		{/if}
	</ul>
</fieldset>

{/block}


{block name=js_include append}

<script src="{$path_js}/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/mainmenu.js">
</script>
<script type="text/javascript" src="{$path_js}/vendor/bootbox.min.js"></script>

{/block}