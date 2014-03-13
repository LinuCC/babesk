{extends file=$inh_path} {block name='content'}

<h2 class="moduleHeader">Den Benutzer {$userFullname} einem anderen Kurs hinzufügen</h2>


<form action="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;moveUser=true&amp;userId={$userId}&amp;oldLinkId={$oldLinkId}&amp;movedFromClassId={$movedFromClassId}" method="post">
	<label>Der neue Kurs des Schülers<br>
	<select name="classId">
		{foreach $classes as $class}
			<option value="{$class.classId}">
			{$class.classLabel}</option>
		{/foreach}
	</select>
	</label><br>
	<label>Das Verhältnis des Schülers zum neuen Kurs<br>
	<select name="statusId">
		{foreach $statuses as $status}
			<option value="{$status.statusId}">{$status.translatedName}</option>
		{/foreach}
	</select>
	</label><br>
	<input type="submit" value="Benutzer verschieben">
</form>
{/block}