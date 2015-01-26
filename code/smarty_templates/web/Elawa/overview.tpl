{extends file=$inh_path}{block name=content}

<h3 class="module-header">Sprechzeiten Übersicht</h3>

<div class="panel panel-default">
	<div class="panel-body">
		{if count($meetings)}
			<table class="table table-responsive table-striped">
				<thead>
					<th>Tag</th>
					<th>Zeit</th>
					<th>Lehrer</th>
					<th>Raum</th>
				</thead>
				<tbody>
					{foreach $meetings as $meeting}
						<tr>
							<td>
								{$category = $meeting->getCategory()}
								{if $category}
									{$category->getName()}
								{else}
									---
								{/if}
							</td>
							<td>
								{$meeting->getTime()->format('H:i:s')}
							</td>
							<td>
								{$meeting->getHost()->getName()},
								{$meeting->getHost()->getForename()}
							</td>
							<td>
								{$room = $meeting->getRoom()}
								{if $room}
									{$room->getName()}
								{else}
									---
								{/if}
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		{else}
			<div class="alert-info">
				Bisher keine Sprechzeiten gewählt.
			</div>
		{/if}
	</div>
	<div class="panel-footer">
		<a href="" class="btn btn-primary pull-right">
			Neue Sprechzeit wählen
		</a>
		<div class="clearfix"></div>
	</div>
</div>

{/block}