{extends file=$inh_path} {block name='filling_content'}

<h2 class='module-header'>Die Kursleiter</h2>

<table class="table table-responsive table-hover table-striped">
	<thead>
		<tr>
			<th>ID</th>
			<th>Vorname</th>
			<th>Name</th>
			<th>Adresse</th>
			<th>Telefon</th>
			<th>Kurse leitend dieses Jahr</th>
		</tr>
	</thead>
	<tbody>
		{foreach $classteachers as $classteacher}
		<tr>
			<td>{$classteacher.ID}</td>
			<td>{$classteacher.forename}</td>
			<td>{$classteacher.name}</td>
			<td>{$classteacher.address}</td>
			<td>{$classteacher.telephone}</td>
			<td><ul class="list-group">{$classteacher.classes}</ul></td>
			</td>
			<td>
				<a class="btn btn-info btn-xs"
					href="index.php?module=administrator|Kuwasys|Classteachers|Change&amp;ID={$classteacher.ID}">
					<span class="fa fa-pencil fa-fw"></span>
				</a>
				<button type="button"
					class="btn btn-danger btn-xs delete-classteacher">
					<span class="fa fa-trash-o"></span>
				</button>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

<a class="btn btn-primary pull-right"
	href="index.php?module=administrator|Kuwasys|Classteachers">
	Zurück
</a>

{/block}


{block name=style_include append}

<style type="text/css" media="all">
table ul.list-group {
	margin-bottom: 0px;
}

table ul.list-group li.list-group-item {
	padding-top: 3px;
	padding-bottom: 3px;
}
</style>

{/block}


{block name=js_include append}

<script type="text/javascript" src="{$path_js}/bootbox.min.js"></script>
<script type="text/javascript" src="{$path_js}/administrator/Kuwasys/Classteachers/display-classteachers.js"></script>

{/block}