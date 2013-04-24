{extends file=$booklistParent}{block name=content}
<form action="index.php?section=Schbas|Booklist&action=4" method="post">
	<fieldset>
		<legend>Buchdaten</legend>
		<label>Fach: <input type="text" name="subject" maxlength="3" /></label> <br> <br> 
		<label>Klasse: <input type="text" name="class" maxlength="2" /></label><br> <br> 
		<label>Titel: <input type="text" name="title" maxlength="50" /></label><br> <br> 
		<label>Autor: <input type="text" name="author" maxlength="30" /></label><br> <br> 
		<label>Verlag:<input type="text" name="publisher" maxlength="30" /></label><br> <br> 
		<label>ISBN: <input type="text" name="isbn" maxlength="20" /></label><br> <br> 
		<label>Preis: <input type="text" name="price" maxlength="5" /></label><br> <br>
		<label>Bundle: <input type="text" name="bundle" maxlength="1" /></label><br> <br> 
	</fieldset>
	<input id="submit" type="submit" value="Hinzuf&uuml;gen" />
</form>
{/block}