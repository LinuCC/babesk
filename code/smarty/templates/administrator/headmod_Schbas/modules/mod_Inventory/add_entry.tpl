{extends file=$inventoryParent}{block name=content}
    <form action="index.php?section=Schbas|Inventory&action=4" method="post">
    <fieldset>
        <legend>Inventar-Daten</legend>
        <label for="barcode">Barcode:</label>
        <input type="text" name="barcode" /><br><br>
    </fieldset><br>
    <input type="submit" value="Submit" />
  </form>

  {/block}