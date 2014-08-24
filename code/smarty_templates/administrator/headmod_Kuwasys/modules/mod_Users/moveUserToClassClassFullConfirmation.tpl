{extends file=$inh_path} {block name=content}

<h2 class="module-header">Der Kurs hat die maximale Anzahl von Registrierungen erreicht</h2>

<p>Sind sie sich sicher, dass sie trotzdem den Benutzer {$user.name} {$user.forename} vom Kurs "{$classOld.label}" zum Kurs
{$classNew.label} verschieben wollen?</p>

<form action="index.php?section=Kuwasys|Users&action=moveUserByClass&classIdOld={$classOld.ID}&userId={$user.ID}" method="post">
	<input type="hidden" name="statusNew" value="{$statusNew}">
	<input type="hidden" name="classIdNew" value="{$classNew.ID}">
	<input type="submit" name="confirmed" value="JA, ich möchte den Benutzer verschieben">
	<input type="submit" name="notConfirmed" value="NEIN, ich möchte den nicht Benutzer verschieben">
</form>

{/block}