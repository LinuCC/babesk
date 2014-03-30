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

<script>
EnableSubmit = function(val)
{
    var sbmt = document.getElementById("sendReminder");

    if (val.checked == true)
    {
        sbmt.disabled = false;
    }
    else
    {
        sbmt.disabled = true;
    }
}

EnableDelete = function(val)
{
    var sbmt = document.getElementById("deleteAll");

    if (val.checked == true)
    {
        sbmt.disabled = false;
    }
    else
    {
        sbmt.disabled = true;
    }
}
</script>


<fieldset>
<h3>Antr&auml;ge verwalten</h3>
<form action="index.php?section=Schbas|SchbasAccounting&action=userSetReturnedFormByBarcode" method="post">
	<input type="submit" value="Antrag erfassen">
</form>
<form action="index.php?section=Schbas|SchbasAccounting&action=userRemoveByID" method="post">
	<input type="submit" value="Antrag l&ouml;schen">
</form>
</fieldset>

<fieldset>
<h3>Finanzen verwalten</h3>
<form action="index.php?section=Schbas|SchbasAccounting&action=1" method="post">
	<input type="submit" value="Geldeingang erfassen">
</form>
<form action="index.php?section=Schbas|SchbasAccounting&action=sendReminder" method="post">
	<input type="checkbox" name="TOS" value="agreeReminder" onClick="EnableSubmit(this)">
	<input type="submit" id="sendReminder" value="Mahnungen senden" disabled="disabled">
</form>
    <form action="index.php?section=Schbas|SchbasAccounting&action=deleteAll" method="post">
        <input type="checkbox" name="TOS2" value="agreeReminder" onClick="EnableDelete(this)">
        <input type="submit" id="deleteAll" value="Buchhaltung leeren" disabled="disabled">
    </form>
</fieldset>

<fieldset>
<h3>Noch abzugebende B&uuml;cher</h3>
{hide}
<form action="index.php?section=Schbas|SchbasAccounting&action=remember" method="post">
	<input type="submit" value="Liste mit allen Schülern erstellen">
</form>
{/hide}
Liste nach Klassen:<br/>
{$listOfClasses}
</fieldset>

<fieldset>
<h3>Noch auszuleihende B&uuml;cher</h3>
Liste nach Klassen:<br/>
{$listOfClassesRebmemer}
</fieldset>


{/block}
