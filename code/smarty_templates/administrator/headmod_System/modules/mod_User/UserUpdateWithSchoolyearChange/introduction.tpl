{extends file=$inh_path}{block name=content}

<h2 class="module-header">{t}Update users and change the schoolyear{/t}</h2>

<p>
	{t condense=yes}
		Here you can change the schoolyear and at the same time update the
		grades of the users with a csv-file.
	{/t}
</p>

<div class="clearfix">
	<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession" method="post">
		<input style="float:left" class="view-cell" type="submit" value="{t}Begin change{/t}" name="schoolyearSelect" />
	</form>
	<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession" method="post">
		<input style="float:right" type="submit" value="{t}Csv-file help{/t}" name="csvHelp" />
	</form>
</div>
{/block}