{extends file=$base_path} {block name=content}

<!-- JAVASCRIPT -->
<script type="text/javascript">
var oldHeadModule = ''; 

function getHeadModuleIdentifier(mod_ident) {
	
	var mod_ident_arr = mod_ident.split('|');
	return mod_ident_arr [0];
}

function makeTooltipDisappear() {
	var tooltip = document.getElementById('ToolTip');
	tooltip.style.visibility = "hidden";
	tooltip.style.display = "none";
}

function changeHeadModule(headMod) {

	makeTooltipDisappear();
	if(headMod == oldHeadModule) {
		alert('Dieses Modul ist bereits ausgewählt');
	}
	else {
		
		var elements = document.getElementsByClassName('menu_item');
		
		for(var i = 0; i < elements.length; i++) {
			
			var element = elements [i];
			var elementHeadMod = getHeadModuleIdentifier(element.id);
			if(elementHeadMod == headMod) {
				element.style.visibility = "visible";
				element.style.display = "inline";
			}
			else {
				element.style.display = "none";
				element.style.visibility = "hidden";
			}
		}
		oldHeadModule = headMod;
	}
}
</script>	

<!-- CSS-Script -->

{literal}
<style type='text/css'>
.HeadItem {
	display: inline;
}
.HeadItemForm {
	display: inline;
}
.HeadItemButton {
	width:100px;
	height:60px;
/* 	padding-left:20px; */
/* 	padding-right:20px; */
/* 	padding-top:10px; */
/* 	padding-bottom:10px; */
}
.menu_item {
	display: none;
	visibility: hidden;
}

.HeadItemContainer {
	margin-left:{/literal}{400 - ((count($head_modules) + 1) * 50)}{literal}px;
	margin-bottom:20px;
}
</style>
{/literal}

<!-- ACTUAL HTML -->

<div class="HeadItemContainer">
{foreach $head_modules as $headmod}
<div class="HeadItem" id="head_module">
	<form name="head_mod" action="" class="HeadItemForm">
		<input class="HeadItemButton" type="button" value="{$headmod}" onclick="changeHeadModule('{$headmod}')">
	</form>
</div>
{/foreach}
</div>


<h2 id="menu_header">Hauptmen&uuml;</h2>

{section name=module loop=$modules}
<div class="menu_item" id="{$modules[module]}">
	<h4>
		<a href="index.php?section={$modules[module]}&{$sid}">{$module_names[$modules[module]]}</a>
	</h4>
</div>
{/section}

<div id="ToolTip" style="text-align:center">
<b>Bitte wählen sie ein Modul aus</b>
</div>

<div id="close"></div>
{/block}
{block name=link}<!-- We are already in Main Menu, dont want a linkto same site -->{/block}
