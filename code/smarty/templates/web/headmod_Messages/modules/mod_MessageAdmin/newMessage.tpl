{include file='web/header.tpl' title='Vorlagen'}
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>

{literal}

<style type='text/css'  media='all'>
fieldset {
	border: 1px solid #000000;
}
</style>

{/literal}

<h3>Neue Mitteilung erstellen:</h3>

<form id="addMessage"
	action='index.php?section=Messages|MessageAdmin&amp;action=newMessage'
	method="post">
	<label>Titel:<input type="text" name="contracttitle" value=""></label><br /><br />
	<label>Text:<textarea class="ckeditor" name="contracttext"></textarea></label><br /><br />
	<fieldset>
		<legend>Einstellungen</legend>
		<label>
			Zettel zurückgeben?
			<input type="checkbox" name="shouldReturn">
		</label><br />
		<small>
			dadurch werden Funktionen für diese Nachricht benutzt, die eine Übersicht erlauben, um zu sehen welche Schüler (noch nicht) abgegeben haben.
		</small><br />
		<label>
			Eine Email versenden?
			<input type="checkbox" name="shouldEmail">
		</label><br />
		<small>
			Wenn bestätigt, wird eine Notiz-Email an alle Empfänger dieser Nachricht versendet, die eine Email angegeben haben.
		</small><br />
	</fieldset>
	<fieldset>
		<legend>Gültigkeitsbereich</legend>
		<label>
			G&uuml;ltig von: {if $date_str} {html_select_date prefix='StartDate' end_year="+1" time=$startdate_str}
			{else}{html_select_date prefix='StartDate' end_year="+1"}
			{/if}
		</label><br /><br />
		<label>
			G&uuml;ltig bis: {if $date_str} {html_select_date prefix='EndDate' end_year="+1" time=$enddate_str}
			{else}{html_select_date prefix='EndDate' end_year="+1"}
			{/if}
		</label><br /><br />
	</fieldset>
	<fieldset>
		<legend>An spezifische Benutzer senden</legend>
		<input id="searchUserInp" type="text" name="searchUserInp"
			value="Benutzer suchen...">
		<div id="userSelection">
		</div>
		Einzelne Benutzer, an die die Email geschickt wird:
		<ul id="userSelected">
		</ul>
	</fieldset>
	<fieldset>
		<legend>An Klassen senden</legend>
		<select name="grades[]" size="5" multiple>
			{foreach item=grade from=$grades}
				<option value="{$grade.ID}">{$grade.name}</option>
			{/foreach}
		</select><br />
		<small>Mehrfachwahlen sind durch halten der Strg- (oder Ctrl-)Taste beim klicken möglich. Damit kann sich auch die angewählte Klasse wieder abwählen lassen. Strg+A, um alle Klassen auszuwählen.</small>
	</fieldset>
	<input id="submit" onclick="submit()" type="submit" value="Absenden" />
</form>

{literal}
<script type="text/JavaScript" src="../smarty/templates/web/headmod_Messages/searchUser.js"></script>

<script type="text/javascript">
	$('#searchUserInp').on('keypress', function(event){
		searchUser('searchUserInp', 'userSelection', 'userSelectionButton');
	});

	$(document).on('click', '.userSelectionButton', function(event){
		var meId = $(this).attr('id').replace('userSelectionButtonId', '');
		var name = $(this).val();
		cleanSearchUser('userSelection');
		addUserAsHiddenInp(meId, name, 'addMessage', 'userSelected');
	});
</script>
{/literal}
{include file='web/footer.tpl'}