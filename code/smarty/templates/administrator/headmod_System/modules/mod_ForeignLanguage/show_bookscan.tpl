{extends file=$ForeignLanguageParent}{block name=content}
<h3>Bitte Buch scannen</h3>
<form action="index.php?section=System|ForeignLanguage&action=5" method="post">
	<fieldset>
		
			<textarea name="bookcodes" cols="50" rows="10"></textarea><br />
			<input type="hidden" name="uid" value="{$uid}" /><br />
	</fieldset>
	<input type="submit" value="Senden" />
</form>
{/block}