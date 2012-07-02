{extends file=$inh_path} {block name='content'}

<form action='index.php?section=Kuwasys|Users&action=addUser' method='post'>
	<input type='submit' value='einen neuen Schüler hinzufügen'>
</form>
<form action='index.php?section=Kuwasys|Users&action=showUsers' method='post'>
	<input type='submit' value='alle Schüler anzeigen'>
</form>

{/block}