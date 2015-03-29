{extends file=$inh_path}{block name='content'}

<div>
	<h3>Übersicht über die gewählten Kurse</h3>
		<div class="alert alert-info">
			Keine Kurse wurden ausgewählt.
		</div>
	{foreach $classes as $category}
		<div class="panel panel-primary bg-fit category-panel">
			<div class="panel-heading">
				<h4 class="panel-title">{$category.name}</h4>
			</div>
			<div class="panel-body">
				<ul class="list-group">
				{foreach $category.classes as $class}
					{* Only link to options if registering is enabled *}
					{if $class.registrationEnabled}
					<a class="list-group-item class-container"
						classId="{$class.ID}" statusname="{$class.statusName}"
						href="index.php?section=Kuwasys|ClassDetails&classId={$class.ID}&categoryId={$class.categoryId}">
					{else}
					<li class="list-group-item" classId="{$class.ID}">
					{/if}

						<p class="list-group-item-heading" classId="{$class.ID}">
								{$class.label}
								<span class="label pull-right
									{if $class.status == 'request1'}label-primary
									{elseif $class.status == 'request2'}label-info
									{elseif $class.status == 'active'}label-success
									{elseif $class.status == 'waiting'}label-default
									{else}label-default{/if}
									">{$class.translatedStatus}</span>
								{if $class.isOptional}
									<span class="label label-info pull-right">
										offenes Ganztagsangebot
									</span>
								{/if}
						</p>
						<p class="list-group-item-text">
							<p class="quotebox quoted">{$class.description}</p>
						</p>
					{if $class.registrationEnabled}
						</a>
					{else}
						</li>
					{/if}
				{/foreach}
				</ul>
				<button type="button" catId="{$category.id}"
					class="btn btn btn-danger pull-right unregister-category">
					{t}Unregister from all classes at this day{/t}
				</button>
			</div>
		</div>
	{/foreach}
	<a class="btn btn-primary"
	href="index.php?module=web|Kuwasys|ClassList">
		Zur Kurswahlliste
	</a>
</div>


{/block}

{block name='js_include' append}
<script type="text/javascript" src="{$path_js}/vendor/bootbox.min.js"> </script>
<script type="text/javascript" src="{$path_js}/web/Kuwasys/mainmenu.js">
</script>
{/block}

{block name='style_include' append}
<link rel="stylesheet" href="{$path_css}/web/Kuwasys/main.css"
type="text/css" />

<style type="text/css">

	.category-panel div.panel-body .list-group span.label.pull-right {
		margin-left: 2px;
	}

</style>

{/block}