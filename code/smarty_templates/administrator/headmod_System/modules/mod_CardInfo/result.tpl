{extends file=$checkoutParent}{block name=content}

<h3 class="module-header">Karteninformationen</h3>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">Kartennummer {$card->getCardnumber()}</h3>
	</div>
	<div class="panel-body">
		{if $user}
			<div class="row">
				<div class="col-sm-2">
					<label>Name</label>
				</div>
				<div class="col-sm-10">
					{$user->getForename()} {$user->getName()}
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<label>Gesperrt</label>
				</div>
				<div class="col-sm-10">
					{if $user->getLocked()}
						<span class="text-danger">
							<span class="icon icon-error"></span>
							Ja
						</span>
					{else}
						<span>Nein</span>
					{/if}
				</div>
			</div>
		{/if}
		<div class="row">
			<div class="col-sm-2">
				<label>Klasse</label>
			</div>
			<div class="col-sm-10">
				{if $grade}
					{$grade->getGradelevel()}{$grade->getLabel()}
				{else}
					Keine aktive Klasse vorhanden
				{/if}
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<a class="btn btn-default"
			href="index.php?module=administrator|System|CardInfo">
			Infos zu anderer Karte
		</a>
		{if $user}
			<a class="btn btn-default"
				href="index.php?module=administrator|System|User|DisplayChange&ID={$user->getId()}">
				Benutzer anzeigen/verändern
			</a>
		{/if}
	</div>
</div>

{/block}