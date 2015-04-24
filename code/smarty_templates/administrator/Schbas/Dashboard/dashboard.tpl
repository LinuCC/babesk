{extends file=$inh_path}{block name=filling_content}

<h3 class="module-header">Schbas Dashboard</h3>

<div class="row">
	<div class="col-sm-12 col-md-12 col-lg-12">
		<div id="main-panel" class="panel panel-dashboard">
			<div class="panel-heading">
				<div class="panel-title">Schbas</div>
			</div>
			<div class="panel-body">
				Kommt vielleicht später :)
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-md-12 col-lg-6">
		<div id="book-panel" class="panel panel-dashboard">
			<div class="panel-heading">
				<div class="panel-title">Bücher</div>
			</div>
			<div class="panel-body">
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-md-12 col-lg-6">
		<div id="inventory-panel" class="panel panel-dashboard">
			<div class="panel-heading">
				<div class="panel-title">Exemplare</div>
			</div>
			<div class="panel-body">
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-md-12 col-lg-12">
		<div id="preparation-panel" class="panel panel-dashboard">
			<div class="panel-heading">
				<div class="panel-title">Schbas-Vorbereitungen</div>
			</div>
			<div class="panel-body">
			</div>
		</div>
	</div>
</div>

{/block}

{block name=style_include append}

<link rel="stylesheet" type="text/css" href="{$path_css}/react-widgets.css">

{/block}

{block name=js_include append}

<script type="text/javascript"
	src="{$path_js}/vendor/bootbox.min.js">
</script>
<script type="text/javascript"
	src="{$path_js}/dist/administrator/Schbas/Dashboard/main.js">
</script>

{/block}