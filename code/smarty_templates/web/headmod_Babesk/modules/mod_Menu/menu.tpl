{extends file=$inh_path}{block name=content}

<h3>Bestellungen</h3>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="col-md-offset-2 col-md-8">
			{if $error}
				<div class="alert alert-info">{$error}</div>
			{else}
			<table class="table table-responsive table-striped table-hover table-bordered">
				<thead>
					<tr>
						<th>Datum</th>
						<th>Mahlzeit</th>
						<th>Aktion</th>
					</tr>
				</thead>
				<tbody>
					{foreach $meal as $m}
					<tr>
						<td>{$m.date}</td>
						<td>{$m.name}</td>
						<td>
							{if $m.cancel}
								<a href="index.php?section=Babesk|Cancel&id={$m.orderID}">
									Abbestellen
								</a>
							{else}
								<p>---</p>
							{/if}
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
			{/if}

			<div id="order">
				<a class="btn btn-primary" href="index.php?section=Babesk|Order">Bestellen</a>
			</div>
		</div>
	</div>
</div>
{/block}