{extends file=$UserParent}{block name=content}
<form action="index.php?section=user&action=1" method="post">
        <label for="id">ID:</legend>
        <input type="text" name="id" size="10" maxlength="10" /><br><br>
        <input type="submit" value="Submit" />
</form>
{/block}