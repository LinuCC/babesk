{extends file=$inh_path}{block name=content}

<h2 class="moduleHeader">{t}Update users and change the schoolyear{/t}</h2>

<p>
{t}Here you can change the schoolyear and at the same time update the grades of the users with a csv-file.{/t}
</p>

<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession" method="post">
	<input type="submit" value="{t}Begin change{/t}" name="kindOfChange" />
</form>

{/block}