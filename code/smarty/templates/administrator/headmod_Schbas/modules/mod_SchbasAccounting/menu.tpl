{extends file=$checkoutParent}{block name=content}
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
<h3>Antr&auml;ge verwalten</h3>
<form action="index.php?section=Schbas|SchbasAccounting&action=userSetReturnedFormByBarcode" method="post">
	<input type="submit" value="Antrag erfassen">
</form>
Antrag l&ouml;schen
</fieldset>

<fieldset>
<h3>Finanzen verwalten</h3>
<form action="index.php?section=Schbas|SchbasAccounting&action=1" method="post">
	<input type="submit" value="Geldeingang erfassen">
</form>
Mahnungen versenden<br/>
</fieldset>

<fieldset>
<h3>Noch abzugebende B&uuml;cher</h3>
<form action="index.php?section=Schbas|SchbasAccounting&action=remember" method="post">
	<input type="submit" value="Liste mit allen Schülern erstellen">
</form>
Liste nach Klassen:<br/>
{$listOfClasses}
</fieldset>


{/block}