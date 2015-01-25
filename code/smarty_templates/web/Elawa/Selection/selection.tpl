{extends file=$inh_path}{block name=content}

<h3 class="module-header">Sprechzeit-Auswahl</h3>

{$meetingAr = array()}
{$categories = array()}
{$times = array()}
{foreach $meetings as $meeting}
	{$time = $meeting->getTime()->format('H:i:s')}
	{$length = $meeting->getLength()->format('H:i:s')}
	{$catId = $meeting->getCategory()->getId()}
	{$meetingAr[$time][$length][$catId] = $meeting}
	{if !in_array($meeting->getCategory()->getName(), $categories)}
		{$categories[$catId] = $meeting->getCategory()->getName()}
	{/if}
{/foreach}
{foreach $meetingAr as $time => $lengths}
	{foreach $lengths as $length => $categoryAr}
		{$ignore = ksort($categoryAr)}
		{$meetingAr[$time][$length] = $categoryAr}
	{/foreach}
{/foreach}
{$ignore = ksort($categories)}

<form action="index.php?module=web|Elawa|Selection" method="post">

	<div class="panel panel-default">
		<div class="panel-heading">
			Hier können sie die Sprechzeiten von {$host->getForename()} {$host->getName()} auswählen.
			Nachdem sie die korrekte Sprechzeit ausgewählt haben, gehen sie bitte auf "Anmeldung bestätigen".
		</div>
		<div class="panel-body">
			<table id="selection-table" class="table table-responsive table-striped">
				<thead>
					<tr>
						<th>Zeit</th>
						<th>Länge</th>
						{foreach $categories as $catId => $catName}
							<th data-category-id="{$catId}">{$catName}</th>
						{/foreach}
					</tr>
				</thead>
				<tbody>
					{foreach $meetingAr as $time => $lengths}
						{foreach $lengths as $length => $rowCategories}
							<tr data-time="{$time}">
								<td>
									{$time}
								</td>
								<td>
									{$length}
								</td>
								{foreach $categories as $categoryId => $category}
									{if isset($rowCategories[$categoryId])}
										{$meeting = $rowCategories[$categoryId]}
										<td class="category-row">
											{if $meeting->getIsDisabled()}
												{*Meeting is deactivated*}
												<span class="text-muted">deaktiviert</span>
											{else if $meeting->getVisitor()->getId()}
												{*Meeting already has an applicant*}
												<label class="btn btn-default" disabled>
													Vergeben
												</label>
											{else}
												<label for="meetingId-{$meeting->getId()}"
													class="btn btn-success meeting-status-button">
													<span class="status-text">Frei</span>
													<input type="radio" name="meetingId"
														id="meetingId-{$meeting->getId()}"
														class="meetings" value="{$meeting->getId()}">
												</label>
											{/if}
										</td>
									{else}
										{*Meeting for this time-category-combo does not exist*}
										<td class="text-muted">Nicht angeboten</td>
									{/if}
								{/foreach}
							</tr>
						{/foreach}
					{/foreach}
				</tbody>
			</table>
		</div>
		<div class="panel-footer">
			<a href="index.php?module=web|Elawa" class="btn btn-danger">Abbrechen</a>
			<input type="submit" class="btn btn-default pull-right"
				value="Anmeldung bestätigen">
		</div>
</div>
</form>
{/block}

{block name=js_include append}
<script type="text/javascript" src="{$path_js}/web/Elawa/Selection/selection.js"></script>
{/block}