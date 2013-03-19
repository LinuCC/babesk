{include file='web/header.tpl' title='Nachrichten-Menü'}


{*Show the messages that the user got*}
{if count($receivedMsg)}
<p>
	<b>Post:</b>
</p>
<table class="dataTable">
	<tr>
		<th>Beschreibung</th>
		<th>Status</th>
		<th>Aktion</th>
	</tr>

	{foreach $receivedMsg as $message}
	<tr>
		<td>
			<p>{$message.title}</p>
		</td>
		<td>
			{if $message.return == 'noReturn'}
				<p>muss nicht zurückgegeben werden</p>
			{elseif $message.return == 'shouldReturn'}
				<p><b style="color:rgb(50,50,50);">muss zurückgegeben werden</b></p>
			{elseif $message.return == 'hasReturned'}
				<p>wurde zurückgegeben</p>
			{/if}
		</td>
		<td>
			{if $BaBeSkTerminal}
				Hinweis: Post kann nicht am BaBeSK-Terminal <br>ge&ouml;ffnet werden!
			{else}
			<a href="index.php?section=Messages|MessageMainMenu&action=showMessage&ID={$message.ID}">
				<img src="../smarty/templates/web/images/page_white_acrobat.png">
			</a>
			{/if}
			<a href="index.php?section=Messages|MessageAdmin&amp;action=showMessage&amp;ID={$message.ID}">
				Details...
			</a>
		</td>
	</tr>
	{/foreach}
</table>
{/if}

{*Show the messages that were created by the user*}
{if count($createdMsg) and $editor}
<br /><h4>
	Selbst-erstellte Nachrichten:
</h4>
<table class="dataTable">
	<tr>
		<th>ID</th>
		<th>Beschreibung</th>
		<th>g&uuml;ltig von</th>
		<th>g&uuml;ltig bis</th>
		<th>Aktion</th>
	</tr>

	{foreach $createdMsg as $message}
	<tr>
		<td>
			{$message.ID}
		</td>
		<td>
			{$message.title}
		</td>
		<td>
			{$message.validFrom}
		</td>
		<td>
			{$message.validTo}
		</td>
		<td>
			{if $BaBeSkTerminal}
				Hinweis: Post kann nicht am BaBeSK-Terminal <br>ge&ouml;ffnet werden!
			{else}
			<a href="index.php?section=Messages|MessageMainMenu&amp;action=showMessage&amp;ID={$message.ID}">
				<img src="../smarty/templates/web/images/page_white_acrobat.png">
			</a>
			{/if}
			<a href="index.php?section=Messages|MessageMainMenu&amp;action=deleteMessage&amp;ID={$message.ID}">
				<img src="../smarty/templates/web/images/delete.png">
			</a>
		</td>
	</tr>
	{/foreach}
</table>
{/if}
{if $editor}
	<a href="index.php?section=Messages|MessageMainMenu&amp;action=newMessage">
		Neue Vorlage erstellen
	</a>
{/if}
{include file='web/footer.tpl'}