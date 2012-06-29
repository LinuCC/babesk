{extends file=$baseLayout} 
{block name="header"}
<h3>Einen Administrator hinzufügen</h3>
{/block}
{block name="main"}
Ein Administrator ist bereits vorhanden. Bitte fahren sie fort.
<form action="index.php?module=Kuwasys&action=finish" method="post">
	<input type="submit" value="Fortfahren">
</form>
{/block}