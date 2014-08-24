{extends file=$checkoutParent}{block name=content}

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
</script>

<h3 class="module-header">Schbasverwaltung Menü</h3>

<fieldset>
	<legend>Antr&auml;ge verwalten</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?section=Schbas|SchbasAccounting&amp;action=userSetReturnedFormByBarcode">
				Antrag erfassen
			</a>
		</li>
		<li>
			<a href="index.php?section=Schbas|SchbasAccounting&amp;action=userRemoveByID">
				Antrag löschen
			</a>
		</li>
	</ul>
</fieldset>

<fieldset>
	<legend>Finanzen verwalten</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?section=Schbas|SchbasAccounting&action=1">
				Geldeingang erfassen
			</a>
		</li>
		<li>
			<form action="index.php?section=Schbas|SchbasAccounting&action=sendReminder"
				method="post">
				<input type="checkbox" name="TOS" value="agreeReminder"
					onClick="EnableSubmit(this)">
				<input type="submit" id="sendReminder" class="btn btn-default"
					value="Mahnungen versenden" disabled="disabled">
			</form>
		</li>
	</ul>

</fieldset>

<fieldset>
	<legend>Noch abzugebende B&uuml;cher</legend>
	{hide}
	<form action="index.php?section=Schbas|SchbasAccounting&action=remember" method="post">
		<input type="submit" value="Liste mit allen Schülern erstellen">
	</form>
	{/hide}
	Liste nach Klassen:<br/>
	{$listOfClasses}
</fieldset>

<fieldset>
	<legend>Noch auszuleihende B&uuml;cher</legend>
	Liste nach Klassen:<br/>
	{$listOfClassesRebmemer}
</fieldset>


{/block}
