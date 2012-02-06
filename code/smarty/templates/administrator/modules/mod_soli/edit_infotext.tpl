<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<center><h3>Speiseplan Infotexte</h3></center>
<form action="index.php?section=meals&action=6"
	method="post" onsubmit="submit()">
	<fieldset>
		<legend>Infotext 1</legend>
		<textarea class="ckeditor" name="infotext1">{$infotext1}</textarea>	
	</fieldset>
	<fieldset>
		<legend>Infotext 2</legend>
		<textarea class="ckeditor" name="infotext2">{$infotext2}</textarea>	
	</fieldset>
	<br> <input id="submit" onclick="submit()" type="submit" value="Submit" />
</form>
