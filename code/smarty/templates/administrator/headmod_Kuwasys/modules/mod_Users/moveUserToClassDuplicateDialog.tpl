{extends file=$inh_path} {block name='content'}

{foreach $class as $c}
<p>
	Es existiert bereits eine Verbindung des Schülers zu dem Kurs "{$c.class}" an
	dem Tag "{$c.unit}" mit dem gleichen Status "{$c.status}". Soll die alte
	Verbindung gelöscht werden und stattdessen die neue benutzt werden?
</p>
{/foreach}
<form action="index.php?section=Kuwasys|Users&amp;action=moveUserByClass&amp;classIdOld={$classIdOld}&amp;userId={$userId}" method="post">
	<input type="hidden" name="classIdNew" value="{$classIdNew}" />
	<input type="hidden" name="statusNew" value="{$statusId}" />
	<input type="hidden" name="removeOldUicLink" value="removeOldUicLink" />
	<input type="submit" value="den alten Link löschen und den neuen hinzufügen">
</form>

{/block}