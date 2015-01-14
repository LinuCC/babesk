{extends file=$base_path}{block name=content}

<h2 class="module-header">{t}Update users and change the schoolyear{/t}</h2>

<div class="panel panel-default">
	<div class="panel-body">
		{t condense=yes}
			Here you can change the schoolyear and at the same time update the
			grades of the users with a csv-file.
		{/t}
	</div>
</div>

<p class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<b>Existiert ein Aktualisierungsprozess</b> bereits, so wird er überschrieben.
</p>

<div class="clearfix">
	<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession" method="post">
		<input class="btn btn-warning pull-left" type="submit"
			value="Mit neuem Prozess starten" name="schoolyearSelect">
	</form>
	<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession" method="post" >
		<input type="submit" class="btn btn-default pull-left"
			value="{t}Csv-file help{/t}" name="csvHelp" style="margin-left: 20px">
	</form>
	<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange" class="btn btn-default pull-right">
		Abbrechen
	</a>
</div>
{/block}