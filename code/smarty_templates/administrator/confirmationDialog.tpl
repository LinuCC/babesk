{extends file=$inh_path}{block name=content}
<p>{$promptStr}</p>
<form action='index.php?section={$sectionStr}&action={$actionStr}' method="post">
 	<input style="padding: 20px" type="submit" name="dialogConfirmed" value='{$confirmedStr}'>
 	<input style="padding: 20px" type="submit" name="dialogNotConfirmed" value='{$notConfirmedStr}'>
</form>
{/block}