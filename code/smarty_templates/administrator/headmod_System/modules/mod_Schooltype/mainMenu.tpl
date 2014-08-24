{extends file=$inh_path} {block name='content'}

<h2 class='module-header'>Schultypen</h2>

{if $schooltypes !== false}

<form class="pull-left" action='index.php?section=System|Schooltype&amp;action=addSchooltype' method='POST'>
	<input class="btn btn-default" type='submit' value="Einen Schultyp hinzufügen" />
</form>

<form action='index.php?section=System|Schooltype&amp;action=deactivate'
	method='POST'>
	<input class="btn btn-danger pull-right" type='submit' value="Schultypen deaktivieren">
</form>

<div class="clearfix"></div>

	{if isset($schooltypes) and count($schooltypes)}
		<table class='table table-responsive table-striped table-hover'>
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Optionen</th>
				</tr>
			</thead>
			<tbody>
				{foreach $schooltypes as $schooltype}
				<tr>
					<td>{$schooltype.ID}</td>
					<td>{$schooltype.name}</td>
					<td>
					<form class="pull-left"
						action="index.php?section=System|Schooltype&action=changeSchooltype&ID={$schooltype.ID}" method="post">
						<input class="btn btn-default btn-xs" type='submit' value='bearbeiten'>
					</form>
					<form class="pull-left"
						action="index.php?section=System|Schooltype&action=deleteSchooltype&ID={$schooltype.ID}" method="post">
							<input class="btn btn-danger btn-xs" type='submit' value='löschen'>
					</form>
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	{/if}

{else}

<div class="alert alert-warning">
	<p>
		Die Schultypen sind deaktiviert! Sie müssen sie zuerst aktivieren,
		um sie benutzen zu können.
	</p>
</div>
<form action='index.php?section=System|Schooltype&amp;action=activate'
	method='POST'>
	<input class="btn btn-primary" type='submit' value="Schultypen aktivieren" />
</form>

{/if}

{/block}