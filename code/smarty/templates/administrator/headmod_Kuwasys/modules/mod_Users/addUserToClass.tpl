{extends $inh_path} {block name="content"}

<h2 class="moduleHeader">Den Schüler einen Kurs zuweisen</h2>
<p>Welchen Kurs soll dem Schüler "{$user.forename} {$user.name}" zugewiesen werden?
Der Schüler ist dann aktiv am Kurs beteiligt</p>

<form action="index.php?section=Kuwasys|Users&action=addUserToClass&ID={$user.ID}" method="post">
	<select name="classId">
		{foreach $classes as $class}
			<option value="{$class.ID}">{$class.label}</option>
		{/foreach}
	</select><br>
	<label>Wie ist die Verbindung des Schülers zum Kurs?</label><br>
	<select name="classStatus">
		<option value="active">Aktiv</option>
		<option value="waiting">Wartend</option>
		<option value="request">Wunsche</option>
	</select><br>
	<input type="submit" value="Zuweisen">
</form>
{/block}