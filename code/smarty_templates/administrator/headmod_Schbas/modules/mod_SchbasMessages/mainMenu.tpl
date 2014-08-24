{extends file=$inh_path}
{block name=content}

<h3 class="module-header">Schulbuchausleihnachrichten</h3>

{if count($templates)}
	<table class="dataTable">
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
				<a href="index.php?section=Schbas|SchbasMessages&amp;action=deleteTemplate&amp;id={$template.ID}">
					<img src="../smarty/templates/web/images/delete.png" alt="löschen">
				</a>
			</td>
		</tr>
		{/foreach}
	</table>
{else}
	<p>Es sind keine Vorlagen vorhanden.</p>
{/if}
<br /><br />
<a href="index.php?section=Schbas|SchbasMessages&amp;action=createTemplateForm">Neue Vorlage erstellen...</a>

{/block}