{include file='web/header.tpl' title='Vorlagen'}
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>

<h3>Neue Mitteilung erstellen:</h3>

<form action='index.php?section=Messages|MAdmin&action=savecontract'
	method="post">
	Titel:<input type="text" name="contracttitle" value=""><br>
	Text:<textarea class="ckeditor" name="contracttext"></textarea>
	Klasse:<select name="class[]" size="5" multiple>{foreach item=class from=$classes}<option>{$class}</option>{/foreach}</select><br>
	G&uuml;ltig von: {if $date_str} {html_select_date prefix='StartDate' end_year="+1" time=$startdate_str}
		{else}{html_select_date prefix='StartDate' end_year="+1"}
		{/if}<br />
	G&uuml;ltig bis: {if $date_str} {html_select_date prefix='EndDate' end_year="+1" time=$enddate_str}
		{else}{html_select_date prefix='EndDate' end_year="+1"}
		{/if}<br />
	
	<input id="submit" onclick="submit()" type="submit" value="Absenden" />
</form>
{include file='web/footer.tpl'}