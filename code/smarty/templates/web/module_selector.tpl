{literal}
<script type="text/javascript">

function setHighlighted($id) {
	var headmod = document.getElementById('headmod_' + $id);
	headmod.style["border"] = "5px solid orange";
}

function setNormal($id) {
	var headmod = document.getElementById('headmod_' + $id);
	headmod.style["border"] = "2px solid #99cc66";
}

</script>
{/literal}

<div id='headmod_selection'>
{$counter = 1}
{foreach $head_modules as $headmod}
<div class='headmod_selector' id='headmod_{$counter}'>
	<a id='headmod_selector_link' href="index.php?section={$headmod.name}"onmouseover='javascript:setHighlighted({$counter})'
		 onmouseout='javascript:setNormal({$counter})'>{$headmod.display_name}</a>
</div>
{$counter = $counter + 1}
{/foreach}
</div>