{extends file=$inh_path} {block name="content"}

<h2 class="moduleHeader">Einen Benutzer von einem Kurs in einen anderen bewegen</h2>

<form action="index.php?section=Kuwasys|Users&action=moveUserByClass&classIdOld={$classIdOld}" method="post">
	<label>Der neue Kurs des Schülers
	<select name="classId">
		{foreach $classes as $class}
			<option value="{$class.ID}">{$class.label}</option>
		{/foreach}
	</select>
	</label>
	<label>Das Verhältnis des Schülers zum neuen Kurs
	<select name="status">
		{foreach $statusArray as $status}
			<option value="{$status.name}">{$status.nameTrans}</option>
		{/foreach}
	</select>
	</label>
</form>

{block}