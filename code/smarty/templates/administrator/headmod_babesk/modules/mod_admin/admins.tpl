{extends file=$adminParent}{block name=content}
<h3>Administrator Managment</h3>
<p>Bitte W&auml;hlen Sie:</p>
<a href="index.php?section=admins&action=addAdmin&{$sid}">Einen Administrator hinzuf&uuml;gen</a><br />
<a href="index.php?section=admins&action=delAdmin&{$sid}">Einen Administrator l&ouml;schen</a><br />
<a href="index.php?section=admins&action=addAdminGroup&{$sid}">Eine Administrator Gruppe hinzuf&uuml;gen</a><br />
<a href="index.php?section=admins&action=delAdminGroup&{$sid}">Eine Administrator Gruppe l&ouml;schen</a><br />

{/block}