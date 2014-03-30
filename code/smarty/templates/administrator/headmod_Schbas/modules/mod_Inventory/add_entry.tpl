{extends file=$inventoryParent}
{block name=content}
    <form action="index.php?section=Schbas|Inventory&action=4" method="post">
    <fieldset>
        <legend>Inventar hinzuf&uuml;gen</legend>
        <textarea name="bookcodes" cols="50" rows="10"></textarea><br />
        <input type="submit" value="Abschicken" />
    </fieldset>
  </form>
{/block}