{include file='web/header.tpl' title='Vom Kurs abmelden'}
Wollen sie sich wirklich vom Kurs {$class.label} abmelden?

<form action="index.php?section=Kuwasys|ClassDetails&action=deRegisterClass&classId={$class.ID}" method="post">
	<input type="submit" name="yes" value="Ja, ich möchte mich vom Kurs abmelden">
	<input type="submit" name="no" value="Nein, ich möchte mich nicht vom Kurs abmelden">
</form>

{include file='web/footer.tpl'}