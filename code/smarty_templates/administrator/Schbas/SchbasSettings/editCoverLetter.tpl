{extends file=$schbasSettingsParent}{block name=content}
<script type="text/javascript" src="{$path_js}/vendor/ckeditor/ckeditor.js"></script>

<h3 class="module-header">Anschreiben</h3>

<form method="post"
	action="index.php?section=Schbas|SchbasSettings&amp;action=editCoverLetter">

	<div class="form-group">
		<label>Titel</label>
		<input id="messagetitle" type="text" name="messagetitle"
			class="form-control" value="{$title}" />
	</div>
	<div class="form-group">
		<label>Text Anschreiben:</label>
		<textarea class="ckeditor" name="messagetext">{$text}</textarea>
	</div>

	<input id="submit"type="submit" class="btn btn-primary" value="Speichern" />
</form>

{/block}