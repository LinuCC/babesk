{extends file=$inh_path}{block name=content}

<div class="col-md-6 col-md-offset-3">
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="panel-title">Am {$date} das Menü "{$meal}" bestellen?</div>
		</div>
		<div class="panel-body">
			<form method="POST" class="pull-right"
				action="index.php?section=Babesk|Order&order={$orderId}&confirmed">
				<input class="btn btn-success" type="submit" name="OK" value="Bestellen">
			</form>

			<form method="POST" class="pull-left"
				action="index.php?section=Babesk|Order">
				<input class="btn btn-danger" type="submit" name="CANCEL" value="Abbrechen">
			</form>
		</div>
	</div>
</div>

{/block}
