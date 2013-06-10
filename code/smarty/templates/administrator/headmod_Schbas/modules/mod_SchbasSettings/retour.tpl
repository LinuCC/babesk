{extends file=$schbasSettingsParent}{block name=content}

<b>Deadlines</b>
<br>
<form action="index.php?section=Schbas|SchbasSettings&amp;action=6" method="post">
R&uuml;ckgabe der ausgef&uuml;llten Formulare : <br>{html_select_date field_order="dmy" prefix="claim_"}<br><br>
Geldtransfer : <br>{html_select_date field_order="dmy" prefix="transfer_"}<br><br>

<input type="submit" value="Hinzuf&uuml;gen" />
</form>
{/block}