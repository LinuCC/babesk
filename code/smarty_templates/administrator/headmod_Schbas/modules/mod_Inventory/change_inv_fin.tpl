{extends file=$inventoryParent}{block name=content}

<div class="panel panel-success">
		<div class="panel-heading">
			<h3 class="panel-title">Das Inventar wurde erfolgreich verändert.</h3>
		</div>
		<div class="panel-body">
			ID: {$id}<br>
			Kaufjahr: {$purchase}<br>
			Exemplar: {$exemplar}<br>
		</div>
		<div class="panel-footer">
			<a class="btn btn-primary pull-right"
				href="index.php?module=administrator|Schbas|Inventory&action=1">
				Zurück zur Buchliste
			</a>
			<div class="clearfix"></div>
		</div>
</div>

{/block}