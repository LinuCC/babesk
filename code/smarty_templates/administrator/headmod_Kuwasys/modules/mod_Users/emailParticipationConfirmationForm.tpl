{extends file=$inh_path} {block name='content'}

<h2 class="module-header">Emails absenden</h2>

<p>
	Hier können sie den Inhalt der Email selber füllen. die Anhänge werden
	automatisch hinzugefügt und abgeschickt.
</p>
<br />
<form action="index.php?section=Kuwasys|Users&amp;action=sendEmailsParticipationConfirmation" method="post">
	<label>Überschrift:<br /> <input type="text" name="subject" size="80"></label><br />
	<label>Text:<br /> <textarea name="body" cols="80" rows="10"></textarea></label><br />
	<input type="submit" value="Emails  absenden">
</form>

{/block}