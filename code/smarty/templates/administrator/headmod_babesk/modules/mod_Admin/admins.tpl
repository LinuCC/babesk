{extends file=$adminParent}{block name=content}
<h3>Administrator Managment</h3>
<p>Bitte W&auml;hlen Sie:</p>
<a href="index.php?section=babesk|Admin&action=addAdmin&{$sid}">Einen Administrator hinzuf&uuml;gen</a><br />
<a href="index.php?section=babesk|Admin&action=delAdmin&{$sid}">Einen Administrator l&ouml;schen</a><br />
<a href="index.php?section=babesk|Admin&action=addAdminGroup&{$sid}">Eine Administrator Gruppe hinzuf&uuml;gen</a><br />
<a href="index.php?section=babesk|Admin&action=delAdminGroup&{$sid}">Eine Administrator Gruppe l&ouml;schen</a><br />

{/block}