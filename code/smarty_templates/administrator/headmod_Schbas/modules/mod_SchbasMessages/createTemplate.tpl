{extends file=$inh_path}
{block name=content}

<script type="text/javascript" src="{$path_js}/ckeditor/ckeditor.js"></script>

<h3 class="module-header">Neue Vorlage</h3>

<form role="form" action="index.php?section=Schbas|SchbasMessages&amp;action=addTemplate" method="POST">
	<fieldset>
		<legend>Vorlagendaten</legend>
		<div class="form-group">
			<label for="template-title">Titel</label>
			<input id="template-title" class="form-control" type="text"
				name="templateTitle" placeholder="Titel der Vorlage">
		</div>
		<div class="form-group">
			<label for_"template-text">Text</label>
			<textarea id="template-text" class="ckeditor" name="templateText">
			</textarea>
		</div>
	</fieldset>
	<input type="submit" value="Vorlage hinzufügen" />
</form>
{/block}