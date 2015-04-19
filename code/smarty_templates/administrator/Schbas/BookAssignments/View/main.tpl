{extends file=$inh_path}{block name=content}

<h3 class="module-header">Buchzuweisungen</h3>

<div class="row">
	<div id="view-entry" class="col-sm-12 col-md-12 col-lg-12">
	</div>
</div>

{/block}

{block name="style_include" append}
<link rel="stylesheet"
	href="{$path_css}/react-select.css"
	type="text/css" />
<link rel="stylesheet"
	href="{$path_css}/administrator/Schbas/BookAssignments/View/main.css"
	type="text/css" />
{/block}

{block name=js_include append}

<script type="text/javascript"
	src="{$path_js}/vendor/bootbox.min.js">
</script>
<script type="text/javascript"
	src="{$path_js}/dist/administrator/Schbas/BookAssignments/View/main.js">
</script>

{/block}