{extends file=$inh_path}{block name='content'}

<div>
	<h3>Übersicht über die gewählten Kurse</h3>
	{if !count($classes)}
		Keine Kurse wurden ausgewählt.
	{else}
	{foreach $classes as $unitname => $classesAtUnit}
		<div class="panel panel-primary bg-fit">
			<div class="panel-heading">
				<h4 class="panel-title">{$unitname}</h4>
			</div>
			<div class="panel-body">
				<ul class="list-group">
				{foreach $classesAtUnit as $class}
					{* Only link to options if registering is enabled *}
					{if $class.registrationEnabled}
					<a class="list-group-item" classId="{$class.ID}"
						href="index.php?section=Kuwasys|ClassDetails&classId={$class.ID}">
					{else}
					<li class="list-group-item" classId="{$class.ID}">
					{/if}

						<p class="list-group-item-heading" classId="{$class.ID}"
							href="index.php?section=Kuwasys|ClassDetails&classId={$class.ID}"
							>
								{$class.label}
								<span class="label pull-right
									{if $class.status == 'request1'}label-primary
									{elseif $class.status == 'request2'}label-info
									{elseif $class.status == 'active'}label-success
									{elseif $class.status == 'waiting'}label-default
									{else}label-default{/if}
									">{$class.translatedStatus}</span>
						</p>
						<p class="list-group-item-text">
							<p class="quotebox">{$class.description}</p>
						</p>
					{if $class.registrationEnabled}
						</a>
					{else}
						</li>
					{/if}
				{/foreach}
				</ul>
				<a class="btn btn btn-danger pull-right" href="#">{t}Unregister from all classes at this day{/t}</a>
			</div>
		</div>
	{/foreach}
	{/if}
	<a class="btn btn-primary"
	href="index.php?module=web|Kuwasys|ClassList">
		Zur Kurswahlliste
	</a>
</div>


{/block}

{block name='js_include' append}
<script type="text/javascript" src="{$path_smarty_tpl}/web/headmod_Kuwasys/classDescriptionSwitch.js">
</script>
{/block}

{block name='style_include' append}
<link rel="stylesheet" href="{$path_css}/web/Kuwasys/main.css"
type="text/css" />
{/block}