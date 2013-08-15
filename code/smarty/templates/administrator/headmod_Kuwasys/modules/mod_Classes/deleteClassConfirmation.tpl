{extends file=$inh_path} {block name="content"}

<h2 class="moduleHeader">
	{_g('Class-Deletion')}
</h2>

<p>
	{_g('Do you really want to delete the Class "%1$s"? All its data is inevitably lost when doing so!', $class.label)}
</p>

<form action="index.php?module=administrator|Kuwasys|Classes|DeleteClass&amp;ID={$class.ID}" method="post">
	<input type="submit" value="{_g('Yes')}" name="confirmed" />
	<input type="submit"
		value="{_g('No, I dont want to delete the Class')}"
		name="declined" />
</form>

{/block}
