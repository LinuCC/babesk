{extends file=$schbasSettingsParent}{block name=content}

<h3 class="moduleHeader">Schbas Einstellungsmenü</h3>

<fieldset>
	<legend>Grundeinstellungen</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?section=Schbas|SchbasSettings&amp;action=editBankAccount">Bankverbindung</a>
		</li>
		<li>
			<a href="index.php?section=Schbas|SchbasSettings&amp;action=2">Ausleihgebühren</a>
		</li>
		<li>
			<a href="index.php?section=Schbas|SchbasSettings&amp;action=3">Termine</a>
		</li>
	</ul>
</fieldset>



<fieldset>
	<legend>Texteinstellungen</legend>

	<ul class="submodulelinkList">
		<li>
			<a href="index.php?section=Schbas|SchbasSettings&amp;action=editCoverLetter">Anschreiben</a>
		</li>
		<li>
			<a href="index.php?section=Schbas|SchbasSettings&amp;action=8">Informationstexte</a>
		</li>
		<li>
			<a href="index.php?section=Schbas|SchbasSettings&amp;action=previewInfoDocs">Vorschau der Informationsschreiben</a>
		</li>
		<li>
			<a href="index.php?section=Schbas|SchbasSettings&amp;action=setReminder">Mahnung</a>
		</li>
	</ul>
</fieldset>

<fieldset>
	<legend>Systemstatus</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?section=Schbas|SchbasSettings&amp;action=7">
				Rückmeldeformular aktivieren
			</a>
		</li>
	</ul>
</fieldset>

{/block}