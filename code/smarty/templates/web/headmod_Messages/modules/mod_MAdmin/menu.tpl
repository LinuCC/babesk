{include file='web/header.tpl' title='Vorlagen'}


{*Show the messages that the user got*}
{if count($receivedMsg)}
<p>
	<b>Post:</b>
</p>
<table>
	<tr>
		<th>Beschreibung</th>
		<th>Aktion</th>
	</tr>

	{foreach $receivedMsg as $message}
	<tr>
		<td>
			{$message.title}
		</td>
		<td>
			{if $BaBeSkTerminal}
				Hinweis: Post kann nicht am BaBeSK-Terminal <br>ge&ouml;ffnet werden!
			{else}
			<a href="index.php?section=Messages|MAdmin&action=showMessage&ID={$message.ID}">
				<img src="../smarty/templates/web/images/page_white_acrobat.png">
			</a>
			{/if}
		</td>
	</tr>
	{/foreach}
</table>
{/if}

{*Show the messages that were created by the user*}
{if count($createdMsg) and $editor}
<p>
	Selbst-erstellte Nachrichten:
</p>
<table>
	<tr>
		<th>Klasse</th>
		<th>Beschreibung</th>
		<th>g&uuml;ltig von</th>
		<th>g&uuml;ltig bis</th>
		<th>Aktion</th>
	</tr>

	{foreach $messages as $message}
	<tr>
		<td>
			{$message.class}
		</td>
		<td>
			{$message.title}
		</td>
		<td>
			{$valid_from}
		</td>
		<td>
			{$valid_to}
		</td>
		<td>
			{if $BaBeSkTerminal}
				Hinweis: Post kann nicht am BaBeSK-Terminal <br>ge&ouml;ffnet werden!
			{else}
			<a href="index.php?section=Messages|MAdmin&amp;action=showcontract&amp;id={$contract.id}">
				<img src="../smarty/templates/web/images/page_white_acrobat.png">
			</a>
			{/if}
			<a href="index.php?section=Messages|MAdmin&amp;action=deletecontract&amp;id={$contract.id}">
				<img src="../smarty/templates/web/images/delete.png">
			</a>
		</td>
	</tr>
	{/foreach}
</table>
{/if}
{if $editor}
	<a href="index.php?section=Messages|MAdmin&amp;action=newMessage">
		Neue Vorlage erstellen
	</a>
{/if}
{include file='web/footer.tpl'}