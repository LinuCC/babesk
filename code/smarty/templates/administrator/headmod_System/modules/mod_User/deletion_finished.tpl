
{extends file=$UserParent}{block name=content}

<p align="center">Der Benutzer wurde erfolgreich gelöscht.</p>
<a class="button-link" href="{$pdf}" target="_blank"><input type="submit" value="1. Best&auml;tigungs-PDF im neuen Fenster &ouml;ffnen"></a><br>
<form name="delpdf" action="index.php?section=System|User&action=7&ID={$uid}" method="post">
<input type="submit" value="2. Best&auml;tigungs-PDF l&ouml;schen">

</form>


{/block}