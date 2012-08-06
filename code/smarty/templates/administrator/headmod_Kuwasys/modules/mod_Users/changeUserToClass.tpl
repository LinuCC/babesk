{extends $inh_path} {block name="content"}

<h2 class="moduleHeader">Die Zuordnung von "{$user.forename} {$user.name}" zu dem Kurs {$class.label} verändern</h2>

<form action="index.php?section=Kuwasys|Users&action=changeUserToClass&userId={$user.ID}&classId={$class.ID}" method="post">
	<label>Wie ist die Verbindung des Schülers zum Kurs?</label><br>
	<select name="classStatus">
		<option value="active"
			{if $linkStatus == "active"} selected="selected"{/if}
			>Aktiv</option>
		<option value="waiting"
			{if $linkStatus == "waiting"} selected="selected"{/if}
		>Wartend</option>
		<option value="request#1"
			{if $linkStatus == "request#1"} selected="selected"{/if}
		>Als Erstwunsch</option>
		<option value="request#2"
			{if $linkStatus == "request#2"} selected="selected"{/if}
		>Als Zweitwunsch</option>
		<option value="noConnection"
			{if $linkStatus != "active" && $linkStatus != "waiting" && $linkStatus != "request#1" && $linkStatus != "request#2"} selected="selected"{/if}
		>Nicht zugeordnet</option>
	</select><br>
	<input type="submit" value="Zuweisen">
</form>
{/block}