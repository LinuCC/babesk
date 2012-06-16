{extends file=$inh_path}{block name=content}
<p>{$promptStr}</p>
<form action='index.php?section={$sectionStr}&action={$actionStr}' method="post">
 	<input type="submit" name="confirmed" value='{$comfirmedStr}'>
 	<input type="submit" name="notConfirmed" value='{$notComfirmedStr}'>
</form>
{/block}