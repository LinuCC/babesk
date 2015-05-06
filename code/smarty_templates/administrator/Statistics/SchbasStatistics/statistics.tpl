{extends file=$base_path}{block name=content}

<div class="row">
  <div id="entry" class="col-xs-12">
  </div>
</div>

{/block}

{block name="style_include" append}
<link rel="stylesheet"
  href="{$path_css}/administrator/System/Users/displaySingle.css"
  type="text/css" />
<link rel="stylesheet"
  href="{$path_css}/nprogress.css"
  type="text/css" />
<link rel="stylesheet"
  href="{$path_css}/react-widgets.css"
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
  src="{$path_js}/dist/administrator/Statistics/SchbasStatistics/statistics.js"
  >
</script>

{/block}