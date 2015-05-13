{extends file=$inh_path}{block name=filling_content}

<h3 class="module-header">Schbas Dashboard</h3>

<div id="react">
</div>

{/block}

{block name=style_include append}

<link rel="stylesheet" type="text/css" href="{$path_css}/react-widgets.css">
<link rel="stylesheet" type="text/css" href="{$path_css}/nprogress.css">

{/block}

{block name=js_include append}

<script type="text/javascript"
	src="{$path_js}/vendor/bootbox.min.js">
</script>
<script type="text/javascript"
	src="{$path_js}/dist/administrator/Schbas/Dashboard/main.js">
</script>

{/block}