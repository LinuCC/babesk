{extends file=$checkoutParent}{block name=content}

<h3 class="module-header">Informationen zum Exemplar</h3>

<div class="panel {if !$locked}panel-default{else}panel-danger{/if}">
		<div class="panel-heading">
			<h3 class="panel-title">Kontoinformationen</h3>
		</div>
		<div class="panel-body">
			<p><label>UserID:</label> {$userID}
				{if $locked} <font color="red"><b>(gesperrt!)</b></font>{/if}
			</p>
			<p><label>Vorname:</label> {$forename}</p>
			<p><label>Name:</label> {$name}</p>
			<p><label>Klasse:</label> {$class}</p>
		</div>
</div>

<div class="panel {if !$locked}panel-default{else}panel-danger{/if}">
	<div class="panel-heading">
		<h3 class="panel-title">Buchinformation</h3>
	</div>
	<div class="panel-body">
		<p><label>BuchID:</label> {$bookID}</p>
		<p><label>Fach:</label> {$subject}</p>
		<p><label>Klasse:</label> {$class}</p>
		<p><label>Titel:</label> {$title}</p>
		<p><label>Autor:</label> {$author}</p>
		<p><label>Herausgeber:</label> {$publisher}</p>
	</div>
</div>

<a class="btn btn-default pull-right"
	href="index.php?module=administrator|Schbas|BookInfo">
	Zurück
</a>

{/block}