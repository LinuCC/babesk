{extends file=$base_path}{block name=content}
<form action='index.php?section=groups&action=1' method='post'>
	<label>Name der Gruppe:<input type="text" name='groupname' size='20'/> </label><br>
	<label>Maximales Guthaben:<input type='text' name='max_credit' size='5'>Euro</label><br>
	<input type="submit" value="Gruppe hinzufügen">
</form>
{/block}