{extends file=$inh_path} {block name="content"}

<h2 class="module-header">Den Benutzer "{$user.forename} {$user.name}" von dem Kurs "{$classOld.label}" in einen anderen verschieben</h2>

<form action="index.php?section=Kuwasys|Users&action=moveUserByClass&classIdOld={$classOld.ID}&userId={$user.ID}" method="post">
	<label>Der neue Kurs des Schülers<br>
	<select name="classIdNew">
		{foreach $classes as $class}
			<option value="{$class.ID}"
			{if $class.ID == $classOld.ID}selected="selected"{/if}>
			{$class.label}</option>
		{/foreach}
	</select>
	</label><br>
	<label>Das Verhältnis des Schülers zum neuen Kurs<br>
	<select name="statusNew">
		{foreach $statusArray as $status}
			<option value="{$status.ID}">{$status.translatedName}</option>
		{/foreach}
	</select>
	</label><br>
	<label>Soll ignoriert werden, wenn der Kurs schon voll ist?<input type="checkbox" name="ignoreMaxReg" value="yes"></label><br>
	<input type="submit" value="Absenden">
</form>

{/block}