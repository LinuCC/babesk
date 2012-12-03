{extends file=$inh_path} {block name="content"}

<h2 class="moduleHeader">Die Benutzer zu Kursen zuordnen</h2>

<h5>Informationen:</h5>
<p>
Wenn sie den folgenden Button drücken, werden Die Benutzer, die nach einem Kurs angefragt haben, den Kursen nach Möglichkeit
zugeordnet. Dabei achtet dass Programm auf die maximale Registrierungsanzahl für diesen Kurs. Fragen zuviele Schüler
nach diesem Kurs nach, werden die Schüler per Zufallsverfahren dem Kurs zugewiesen. Der Rest kommt auf die Warteliste des
jeweiligen Kurses.
</p>

<form action="index.php?section=Kuwasys|Classes&action=assignUsersToClasses" method="post">
	<input type="submit" name="confirmed" value="Alle Schüler ihren gewünschten Kursen zuordnen">
</form>

{/block}