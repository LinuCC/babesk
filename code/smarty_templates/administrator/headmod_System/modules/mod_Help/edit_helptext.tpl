{extends file=$base_path}{block name=content}
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>

<h3>Hilfetext bearbeiten:</h3>

<form action='index.php?section=System|Help&action=2'
	method="post">
	<textarea class="ckeditor" name="helptext">{$helptext}</textarea>
	<input id="submit" onclick="submit()" type="submit" value="Submit" />
</form>
{/block}