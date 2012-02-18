<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>

<h3>Hilfetext bearbeiten:</h3>

<form action='index.php?section=help&action=2' onsubmit='submit()'
	method="post">
	<textarea class="ckeditor" name="helptext">{$helptext}</textarea>
	<input id="submit" onclick="submit()" type="submit" value="Submit" />
</form>