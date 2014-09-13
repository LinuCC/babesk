{extends file=$base_path}{block name=content}

<h2 class="module-header">{t}Update users and change the schoolyear{/t}</h2>

<p class="alert alert-info">
	{t condense=yes}
		Here you can change the schoolyear and at the same time update the
		grades of the users with a csv-file.
	{/t}
</p>

<div class="clearfix">
	<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession" method="post">
		<input class="btn btn-primary pull-left" type="submit"
			value="{t}Begin change{/t}" name="schoolyearSelect" />
	</form>
	<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession" method="post">
		<input type="submit" class="btn btn-default pull-right"
			value="{t}Csv-file help{/t}" name="csvHelp" />
	</form>
</div>
{/block}