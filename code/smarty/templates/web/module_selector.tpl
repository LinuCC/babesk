{literal}
<script type="text/javascript">

$(document).ready(function() {

	$('div.headmodule').on('mouseover', function(event) {
		$(this).css('border', '5px solid orange');
	});

	$('div.headmodule').on('mouseout', function(event) {
		$(this).css('border', '2px solid #99cc66');
	});
});

</script>
{/literal}

<div id='headmod_selection'>


    <p>Willkommen! Bitte w&auml;hle ein Modul:</p>

{foreach $modules as $module}
	{if $module->isDisplayInMenuAllowed()}
		<a id='headmod_selector_link'
			href="index.php?section={$module->getName()}">
			<div class='headmodule' id='headmod_{$counter}'>
				{$module->getName()}
			</div>
		</a>
	{/if}
{/foreach}
</div>
