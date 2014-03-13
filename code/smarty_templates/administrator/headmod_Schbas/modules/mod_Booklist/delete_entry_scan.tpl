{extends file=$booklistParent}{block name=content}
    <form action="index.php?section=Schbas|Booklist&action=3" method="post">
    <fieldset>
        <h2>Buch mit ISBN l&ouml;schen </h2>
        <h3>Hinweis: Sollte das zu l&ouml;schende Buch noch im Inventar vorhanden sein, werden die Inventar- und ggf. Ausleihdaten auch gel&ouml;scht!</h3>
        <label for="barcode">Barcode:</label>
        <input type="text" name="barcode" /><br><br>
    </fieldset><br>
    <input type="submit" value="L&ouml;schen (keine weitere Abfrage!!!!)" />
  </form>

  {/block}