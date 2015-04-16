{extends file=$inh_path}{block name=content}

<h3 class='module-header'>Email verändern</h3>
<div class="col-md-8 col-md-offset-2">
	<div class="panel panel-danger">
		<div class="panel-heading">
			<div class="panel-title">
				Benutzer sperren
			</div>
		</div>
		<div class="panel-body">
			Willst du diesen Account wirklich sperren?
		</div>
		<div class="panel-footer">
			<a href="index.php?module=web|Settings|Account&amp;lockAccount=lockAccount"
				class="btn btn-danger pull-right">Account sperren!</a>
			<a href="index.php" class="btn btn-default">
				Abbrechen
			</a>
		</div>
	</div>
</div>
{/block}