{extends file=$adminParent}{block name=content}
Was wollen sie tun?<br>

<form action="index.php?section=babesk|admin&action={$action['add_admin']}" method="post">
	<input type="submit" value="Einen Administrator hinzufügen">
</form><br>

<form action="index.php?section=babesk|admin&action={$action['show_admins']}" method="post">
	<input type="submit" value="Die Administratoren anzeigen">
</form><br>

<form action="index.php?section=babesk|admin&action={$action['add_admin_group']}" method="post">
	<input type="submit" value="Eine Administratorgruppe hinzufügen">
</form><br>

<form action="index.php?section=babesk|admin&action={$action['show_admin_groups']}" method="post">
	<input type="submit" value="Die Administratorgruppen anzeigen">
</form><br>
{/block}