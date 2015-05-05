{extends file=$base_path}{block name=content}

<h3 class="module-header">Informationen zum Exemplar</h3>

<div class="panel {if !$locked}panel-default{else}panel-danger{/if}">
		<div class="panel-heading">
			<h3 class="panel-title">Kontoinformationen</h3>
		</div>
		<div class="panel-body">
			{if $user}
				<p><label>Benutzer-ID:</label> {$user->getId()}
					{if $locked} <font color="red"><b>(gesperrt!)</b></font>{/if}
				</p>
				<p><label>Vorname:</label> {$user->getForename()}</p>
				<p><label>Name:</label> {$user->getName()}</p>
				<p>
					<label>Klasse:</label>
					{* Backend made sure we only get the active attendance *}
					{if $activeGrade}
						{$activeGrade->getGradelevel()}{$activeGrade->getLabel()}
					{else}
						---
					{/if}
				</p>
			{else}
				<span class="text-info">
					Dieses Buch ist keinem Benutzer ausgeliehen.
				</span>
			{/if}
		</div>
</div>

<div class="panel {if !$locked}panel-default{else}panel-danger{/if}">
	<div class="panel-heading">
		<h3 class="panel-title">Buchinformation</h3>
	</div>
	<div class="panel-body">
		<p><label>BuchID:</label> {$book->getId()}</p>
		<p><label>Fach:</label> {$book->getSubject()->getName()}</p>
		<p><label>Klasse:</label> {$book->getClass()}</p>
		<p><label>Titel:</label> {$book->getTitle()}</p>
		<p><label>Autor:</label> {$book->getAuthor()}</p>
		<p><label>Herausgeber:</label> {$book->getPublisher()}</p>
	</div>
</div>

<a class="btn btn-default pull-right"
	href="index.php?module=administrator|Schbas|BookInfo">
	Zurück
</a>

{/block}