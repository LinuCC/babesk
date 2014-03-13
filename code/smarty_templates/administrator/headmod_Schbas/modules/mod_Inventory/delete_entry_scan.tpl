{extends file=$inventoryParent}{block name=content}
    <form action="index.php?section=Schbas|Inventory&action=3" method="post">
    <fieldset>
        <h2>Inventar mit Barcode l&ouml;schen </h2>
        <h3>Hinweis: Sollte das zu l&ouml;schende Buchexemplar noch verliehen sein, werden die Ausleihdaten auch gel&ouml;scht!</h3>
        <label for="barcode">Barcode:</label>
        <input type="text" name="barcode" /><br><br>
    </fieldset><br>
    <input type="submit" value="L&ouml;schen" />
  </form>

  {/block}