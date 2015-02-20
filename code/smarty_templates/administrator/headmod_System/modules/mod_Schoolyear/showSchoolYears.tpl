{extends file=$inh_path} {block name='content'}

<h2 class='module-header'>Die Schuljahre</h2>

{literal}



<style type="text/css">
.cleanButtons {
	display : inline;
}
.switchButton {

}
</style>
{/literal}

<table class="table table-responsive table-striped table-hover"
	style='margin:0 auto;'>
	<thead>
		<tr>
			<th>ID</th>
			<th>Bezeichnung</th>
			<th>Aktiv</th>
		</tr>
	</thead>
	<tbody>
		{foreach $schoolYears as $schoolYear}
		<tr {if $schoolYear.active}bgcolor='#df3'{/if}>
			<td>{$schoolYear.ID}</td>
			<td>{$schoolYear.label}</td>
			<td>{if $schoolYear.active}&#10004;{else}&#10008;{/if}</td>
			<td>
				<a class="btn btn-xs btn-default"
					href="index.php?module=administrator|System|Schoolyear&amp;action=changeSchoolYear&amp;ID={$schoolYear.ID}"
					data-toggle="tooltip" title="Schuljahr bearbeiten">
					<span class="icon icon-edit"></span>
				</a>
				<a class="btn btn-xs btn-danger"
					href="index.php?module=administrator|System|Schoolyear&amp;action=deleteSchoolYear&amp;ID={$schoolYear.ID}"
					data-toggle="tooltip" title="Schuljahr löschen">
					<span class="fa fa-trash-o"></span>
				</a>
				{if !($schoolYear.active)}
					<a class="btn btn-xs btn-info"
						href="index.php?module=administrator|System|Schoolyear&amp;action=activateSchoolYear&amp;ID={$schoolYear.ID}"
						data-toggle="tooltip" title="Schuljahr aktivieren">
						<span class="icon icon-refresh"></span>
					</a>
				{/if}
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}


{block name=js_include append}

{literal}

<script type="text/javascript">


</script>

{/literal}

{/block}