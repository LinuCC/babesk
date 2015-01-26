{extends file=$inh_path}{block name=content}

<h3 class="module-header">Lehrer-Auswahl</h3>

<div class="panel panel-default">
	<div class="panel-heading">
		Für welchen Lehrer wollen sie eine Sprechzeit wählen?
	</div>
	<div class="panel-body">
		<div class="list-group">
			{foreach $hosts as $host}
				<a href="index.php?module=web|Elawa|Selection&amp;hostId={$host->getId()}" class="list-group-item">
					{$host->getName()},
					{$host->getForename()}
				</a>
			{/foreach}
		</div>
	</div>
</div>

{/block}