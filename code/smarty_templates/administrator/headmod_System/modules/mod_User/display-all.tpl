{extends file=$UserParent}

{block name=html_snippets append}
<div id="column-show-template" hidden>
	<div class="form-group">
		<label class="col-sm-6 control-label"></label>
		<input type="checkbox" class="column-switch"data-on-text="Ja"
		data-off-text="Nein" data-size="mini" data-on-color="info"
		data-off-color="default" />
	</div>
</div>

<div id="list-user-settings-template" hidden>
	<a class="btn btn-xs btn-info user-action-settings" href="#">
		<span class="icon icon-Settings"></span>
	</a>
	<a class="btn btn-xs btn-danger user-action-delete" href="#">
		<span class="icon icon-error"></span>
	</a>
</div>
{/block}

{block name=popup_dialogs append}
<div id="table-columns-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">Angezeigte Spalten</h4>
			</div>
			<div class="modal-body">
				<form id="column-show-form" class="form-horizontal" role="form">
				</form>
			</div>
			<div class="modal-footer">
				<button id="column-show-form-submit" type="button"
					class="btn btn-primary">
					{t}Ok{/t}
				</button>
			</div>
		</div>
	</div>
</div>
{/block}


{block name=filling_content}
<div>
	<div class="center-block">
		<div class="col-sm-2 col-md-2 text-center">
			<button class="btn btn-default" data-toggle="modal"
				data-target="#table-columns-modal"
				title="{t}What Columns should be displayed{/t}">
				{t}Columns{/t}
			</button>
		</div>
		<div class="col-sm-7 col-md-8 text-center">
			<ul id="page-select" class="pagination">
			</ul>
		</div>
		<div class="col-sm-3 col-md-2 form-group">
			<div class="input-group users-per-page-container"
				title="{t}Elements per page{/t}" >
				<span class="input-group-addon">
					<span class="icon icon-Settings"></span>
				</span>
				<input id="users-per-page" type="text" maxlength="3" class="form-control"
					value="10" />
			</div>
		</div>
	</div>
</div>

<div>
	<table id="user-table" class="table table-striped table-responsive">
	</table>
</div>
<div>
	<ul id="relative-pager" class="pager">
		<li id="relative-pager-prev"><a href="#">&larr; {t}Previous{/t}</a></li>
		<li id="relative-pager-next"><a href="#">{t}Next{/t} &rarr;</a></li>
	</ul>
</div>

{/block}

{block name=style_include append}
<link rel="stylesheet" href="{$path_css}/administrator/System/User/display-all.css" type="text/css" />
<link rel="stylesheet" href="{$path_css}/bootstrap-switch.min.css" type="text/css" />
{/block}

{block name="js_include" append}
<script src="{$path_js}/administrator/System/User/display-all.js">
</script>
<script type="text/javascript" src="{$path_js}/bootstrap-switch.min.js">
</script>
{/block}
