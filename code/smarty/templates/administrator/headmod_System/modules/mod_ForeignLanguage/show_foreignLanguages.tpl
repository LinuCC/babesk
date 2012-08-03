{extends file=$ForeignLanguageParent}{block name=content}

<form action="index.php?section=System|ForeignLanguage&action=2"
	method="post" onsubmit="submit()">
	<fieldset>
		<legend>Fremdsprachen</legend>
		{foreach from=$foreignLanguages item=foreignLanguage name=zaehler}
		<input type="text" name="rel{$smarty.foreach.zaehler.iteration}" size="3"
			maxlength="3" value="{$foreignLanguage}" /><br/>
		{/foreach}
		<input type="text" name="rel{$smarty.foreach.zaehler.total+1}" size="3"
			maxlength="3" value="" /><br/>
		<input type="hidden" name="relcounter" value="{$smarty.foreach.zaehler.total+1}" />
	</fieldset>
	<br> <input id="submit" onclick="submit()" type="submit" value="Speichern" />
</form>
{/block}