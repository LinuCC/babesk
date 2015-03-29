{extends file=$schbasSettingsParent}{block name=content}
<script type="text/javascript" src="{$path_js}/vendor/ckeditor/ckeditor.js"></script>



<form action="index.php?section=Schbas|SchbasSettings&action=editCoverLetter"	method="post">




	<br />
	<label>Titel Anschreiben:<input id="messagetitle" type="text" name="messagetitle" value="{$title}" /></label><br />
	<label>Text Anschreiben:<textarea class="ckeditor" name="messagetext">{$text}</textarea></label><br /><br />

	<input id="submit"type="submit" value="Speichern" />
</form>

{/block}