{include file='web/header.tpl' title='Nachrichten-Admin'}

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
		<th>Aktion</th>
	</tr>
	{foreach $receivers as $receiver}
	<tr>
		<td>{$receiver->id}</td>
		<td>{$receiver->forename}</td>
		<td>{$receiver->name}</td>
		<td>
			{if ($receiver->readMessage)}
				Ja
			{else}
				Nein
			{/if}
		</td>
		<td>
			{$receiver->returnedMessage}
		</td>
		<td>
			<a id="{$receiver->id}" class="removeReceiver" href="">
				<img src="../smarty/templates/web/images/delete.png">
			</a>
		</td>
	</tr>
	{/foreach}
</table>

<input id="receiverSearch" type="text" value="neuer Empfänger..." /><br />

<span id="receiverSearchOutput">
</span>
<!-- <input id="receiverSelectButtonId1633" class="receiverSelectButton" type="button" value="Knut Terjung"> -->
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
			<th>Aktion</th>
		</tr>
		{foreach $managers as $manager}
		<tr>
			<td>{$manager->id}</td>
			<td>{$manager->forename}</td>
			<td>{$manager->name}</td>
			<td>
				<a id="{$manager->id}" class="removeManager" href="">
					<img src="../smarty/templates/web/images/delete.png">
				</a>
			</td>
		</tr>
		{/foreach}
	</table>
	<input id="managerSearch" type="text" value="neuer Manager..." /><br />
	<span id="managerSearchOutput">
	</span><br /><br />
	<input id="deleteMessage" type="button" value="Nachricht löschen" />
	<br />
{else}
	<p>
		Nur der Ersteller der Nachricht kann die Manager-Rechte verteilen und einsehen und die Nachricht löschen.
	</p>
{/if}

<script type="text/JavaScript" src="../smarty/templates/web/headmod_Messages/searchUser.js"></script>

{literal}
<script type="text/JavaScript">

var _messageId = {/literal}{$messageData.ID}{literal};

$('#receiverSearch').on('keypress', function(event) {
	searchUser('receiverSearch', 'receiverSearchOutput', 'receiverSelectButton');
});

$(document).on('click', '.receiverSelectButton', function(event) {
	var meId = $(this).attr('id').replace('receiverSelectButtonId', '');
	addReceiver(meId, {/literal}{$messageData.ID}{literal});
});

$('#managerSearch').on('keypress', function(event) {
	searchUser('managerSearch', 'managerSearchOutput', 'managerSelectButton');
});

$(document).on('click', '.managerSelectButton', function(event) {
	var meId = $(this).attr('id').replace('managerSelectButtonId', '');
	addManager(meId, {/literal}{$messageData.ID}{literal});
});

$('#deleteMessage').on('click', function(event) {
	if(confirm('Wollen sie diese Nachricht wirklich löschen?')) {
		deleteMessage({/literal}{$messageData.ID}{literal});
	}
})

$('.removeReceiver').on('click', function(event) {
	event.preventDefault();
	if(confirm('Wollen sie diesen Benutzer wirklich von der Nachrichtensendung entfernen?')) {
		removeReceiver(_messageId, $(this).attr('id'));
	}
});

$('.removeManager').on('click', function(event) {
	event.preventDefault();
	if(confirm('Wollen sie diesen Manager wirklich von der Nachrichten entfernen?')) {
		removeManager(_messageId, $(this).attr('id'));
	}
});

</script>
{/literal}


{include file='web/footer.tpl'}