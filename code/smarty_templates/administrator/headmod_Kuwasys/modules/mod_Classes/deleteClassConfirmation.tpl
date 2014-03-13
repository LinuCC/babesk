{extends file=$inh_path} {block name="content"}

<h2 class="moduleHeader">
	{t}Class-Deletion{/t}
</h2>

<p>
	{t class=$class.label}Do you really want to delete the Class "%1"? All its data is inevitably lost when doing so!{/t}
</p>

<form action="index.php?module=administrator|Kuwasys|Classes|DeleteClass&amp;ID={$class.ID}" method="post">
	<input type="submit" value="{t}Yes{/t}" name="confirmed" />
	<input type="submit"
		value="{t}No, I dont want to delete the Class{/t}"
		name="declined" />
</form>

{/block}
