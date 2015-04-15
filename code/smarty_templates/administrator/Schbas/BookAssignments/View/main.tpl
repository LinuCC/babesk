{extends file=$inh_path}{block name=content}

<h3 class="module-header">Buchzuweisungen</h3>

<div class="row">
	<div class="col-sm-12 col-md-12 col-lg-12">
		<div id="view-panel" class="panel panel-dashboard">
			<div class="panel-heading">
				<div class="panel-title">Zuweisungen der Bücher an Nutzer</div>
			</div>
			<div class="panel-body">
			</div>
		</div>
	</div>
</div>

{/block}

{block name="style_include" append}
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