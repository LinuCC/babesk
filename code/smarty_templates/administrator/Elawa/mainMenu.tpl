{extends file=$inh_path}{block name=content}

<h3 class="module-header">Elawa Hauptmenü</h3>

<div class="row">
	<div class="col-md-6">
		{if $group}
			Lehrergruppe:
			{$group->getName()}
		{else}
			Keine Lehrergruppe definiert!
		{/if}
		<a id="select-host-group-button" href="#" class="btn btn-default btn-xs">
			ändern
		</a>
	</div>
	<div class="col-md-6">
		<div class="pull-right">
			<label for="enable-selections">
				Wahlen freigegeben:
			</label>
			<input type="checkbox" name="enable-selections" id="enable-selections"
				data-size="mini" data-off-text="Nein" data-on-text="Ja"
				data-off-color="default" data-on-color="primary"
				{if $selectionsEnabled}checked{/if}>
		</div>
	</div>
</div>

<fieldset>
	<legend>Aktionen</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Elawa|Meetings">
				Sprechstunden
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Elawa|GenerateHostPdf">
				PDFs für die Lehrer
			</a>
		</li>
	</ul>
</fieldset>

{/block}


{block name=style_include append}
<link rel="stylesheet" href="{$path_css}/bootstrap-switch.min.css" type="text/css" />
{/block}


{block name=js_include append}
<script type="text/javascript" src="{$path_js}/vendor/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="{$path_js}/administrator/Elawa/main-menu.js"></script>
<script type="text/javascript" src="{$path_js}/vendor/bootbox.min.js"></script>
{/block}