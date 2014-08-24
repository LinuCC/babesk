{extends file=$inh_path}
{block name=content}

<h3 class="module-header">Schulbuchausleihnachrichten</h3>

{if count($templates)}
	<table class="table table-responsive table-hover table-striped">
		<tr>
			<th>ID</th>
			<th>Titel</th>
			<th>HTML-Text</th>
			<th>Aktion</th>
		</tr>
		{foreach $templates as $template}
		<tr>
			<td>{$template.ID}</td>
			<td>{$template.title}</td>
			<td>{$template.text}</td>
			<td>
				<a class="btn btn-danger btn-xs" title="Vorlage löschen" data-toggle="tooltip" href="index.php?section=Schbas|SchbasMessages&amp;action=deleteTemplate&amp;id={$template.ID}">
					<span class="icon icon-error" ></span>
				</a>
			</td>
		</tr>
		{/foreach}
	</table>
{else}
	<div class="alert alert-info">
		<p>Es sind keine Vorlagen vorhanden.</p>
	</div>
{/if}
<a class="btn btn-primary"
	href="index.php?section=Schbas|SchbasMessages&amp;action=createTemplateForm">
	Neue Vorlage erstellen...
</a>

{/block}