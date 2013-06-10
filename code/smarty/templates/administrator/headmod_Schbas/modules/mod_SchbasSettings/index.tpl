{extends file=$schbasSettingsParent}{block name=content}
<style type='text/css'  media='all'>
/*Table should not be over the Border of the Line of the main-block*/

fieldset {
	margin-left: 5%;
	margin-right: 5%;
	margin-bottom: 30px;
	border: 2px dashed rgb(100,100,100);
}
</style>

<fieldset>
<h3>Grundeinstellungen</h3>
<form action="index.php?section=Schbas|SchbasSettings&action=editBankAccount" method="post">
	<input type="submit" value="Bankverbindung">
</form>
<form action="index.php?section=Schbas|SchbasSettings&action=2" method="post">
	<input type="submit" value="Ausleihgeb&uuml;hren">
</form>
<form action="index.php?section=Schbas|SchbasSettings&action=3" method="post">
	<input type="submit" value="Termine">
</form>
</fieldset>



<fieldset>
<h3>Texteinstellungen</h3>
<form action="index.php?section=Schbas|SchbasSettings&action=editCoverLetter" method="post">
	<input type="submit" value="Anschreiben">
</form>
<form action="index.php?section=Schbas|SchbasSettings&action=8" method="post">
	<input type="submit" value="Informationstexte">
</form>


<form action="index.php?section=Schbas|SchbasSettings&action=previewInfoDocs" method="post">
	<input type="submit" value="Vorschau der Informationsschreiben"><br>
</form>
</fieldset>

<fieldset>
<h3>Systemstatus</h3>
<form action="index.php?section=Schbas|SchbasSettings&action=7" method="post">
	<input type="submit" value="R&uuml;ckmeldeformular aktivieren">
</form>

</fieldset>
{/block}