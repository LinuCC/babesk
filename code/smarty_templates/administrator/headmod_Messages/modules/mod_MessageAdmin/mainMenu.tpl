{extends file=$inh_path}
{block name=content}

<style type="text/css">

#main {
	width: 1200px;
}

</style>

{if count($creatorsWithMessages)}
	{foreach $creatorsWithMessages as $creator}
		<fieldset class="blockyField">
			<legend><h4>Ersteller: ID "{$creator->id}"; Name "{$creator->name}"</h4></legend>
			<table class="dataTable">
				<tr>
					<th>ID</th>
					<th>Titel</th>
					<th>Anzahl Empfänger</th>
					<th>Gültig von</th>
					<th>Gültig bis</th>
					<th>Aktion</th>
				</tr>
				{foreach $creator->messages as $message}
				<tr>
					<td>{$message.ID}</td>
					<td>{$message.title}</td>
					<td>{$message.receiverCount}</td>
					<td>{$message.validFrom}</td>
					<td>{$message.validTo}</td>
					<td>
						<a href="index.php?section=Messages|MessageAdmin&amp;action=deleteMessage&amp;id={$message.ID}">
							<img src="../smarty/templates/web/images/delete.png" alt="löschen">
						</a>
					</td>
				</tr>
				{/foreach}
			</table>
		</fieldset>
	{/foreach}
{else}
	<p>Es sind keine Nachrichten gesendet worden.</p>
{/if}
{/block}