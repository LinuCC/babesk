{include file='web/header.tpl' title='Nachrichten-Admin'}

<script type="text/JavaScript" src="../smarty/templates/web/headmod_Messages/searchUser.js"></script>

<h2>
	Nachrichten-Administration
</h2>

<table class="dataTable">
	<tr>
		<th>Titel</th>
		<td>{$messageData.title}</td>
	</tr>
	<tr>
		<th>HTML-Text</th>
		<td>{$messageData.text}</td>
	</tr>
	<tr>
		<th>Gültig ab</th>
		<td>{$messageData.validFrom}</td>
	</tr>
	<tr>
		<th>Gültig bis</th>
		<td>{$messageData.validTo}</td>
	</tr>
</table>

<br />
<h4>
	Empfänger der Nachricht:
</h4>
<table class="dataTable">
	<tr>
		<th>ID</th>
		<th>Vorname</th>
		<th>Nachname</th>
		<th>gelesen</th>
		<th>Rückgabe-Status</th>
	</tr>
	{foreach $receivers as $receiver}
	<tr>
		<td>{$receiver->id}</td>
		<td>{$receiver->forename}</td>
		<td>{$receiver->name}</td>
		<td>{$receiver->readMessage}</td>
		<td>
			{$receiver->returnedMessage}
			<a href="">ändern...</a>
		</td>
	</tr>
	{/foreach}
</table>

<input id="receiverSearch" type="text" value="neuer Empfänger..." onKeyPress="searchUser('receiverSearch', 'receiverSearchOutput')"><br />

<span id="receiverSearchOutput">
</span>

<br />
{if $isCreator}
	<h4>
		Manager der Nachricht:
	</h4>
	<table class="dataTable">
		<tr>
			<th>ID</th>
			<th>Vorname</th>
			<th>Nachname</th>
		</tr>
		{foreach $managers as $manager}
		<tr>
			<td>{$manager->id}</td>
			<td>{$manager->forename}</td>
			<td>{$manager->name}</td>
		</tr>
		{/foreach}
	</table>
{else}
	<p>
		Nur der Ersteller der Nachricht kann die Manager-Rechte verteilen und einsehen.
	</p>
{/if}


{include file='web/footer.tpl'}