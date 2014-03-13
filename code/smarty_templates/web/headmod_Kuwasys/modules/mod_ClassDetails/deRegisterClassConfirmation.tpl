{extends file=$inh_path}{block name=content}
Wollen sie sich wirklich vom Kurs {$class.label} abmelden?

<form action="index.php?section=Kuwasys|ClassDetails&action=deRegisterClass&classId={$class.ID}" method="post">
	<input type="submit" name="yes" value="Ja, ich möchte mich vom Kurs abmelden">
	<input type="submit" name="no" value="Nein, ich möchte mich nicht vom Kurs abmelden">
</form>

{/block}