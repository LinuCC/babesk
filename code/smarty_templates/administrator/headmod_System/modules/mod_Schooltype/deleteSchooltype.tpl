{extends file=$inh_path} {block name='content'}

<h3 class="module-header">Einen Schultyp löschen</h3>

<div class="panel panel-danger">
	<div class="panel-heading">
		<div class="paneltitle">
			Sicherheitsabfrage
		</div>
	</div>
	<div class="panel-body">
		Wollen sie den Schultypen "{$schooltype.name}" wirklich löschen?
	</div>
	<div class="panel-footer">
		<form action="index.php?section=System|Schooltype&amp;action=deleteSchooltype&amp;ID={$schooltype.ID}" method='POST'>
			<input class="btn btn-default" type="submit" value="NICHT löschen!" name="nonono"/>
			<input class="btn btn-danger" type="submit" value="löschen!" name="deletePls"/>
		</form>
	</div>
</div>

{/block}