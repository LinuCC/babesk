{include file='web/header.tpl' title='Vorlagen'}
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>

{literal}
<script type="text/javascript">

function searchUser() {
	var name = document.getElementById("searchUserInp").value;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	}
	else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("userSelection").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "?section=Messages|MAdmin&action=searchUserAjax&username="+name,
		true);
	xmlhttp.send();
}

/* Cleans the search-user dialog */
function cleanSearchUser() {
	var selection = document.getElementById("userSelection")
	selection.innerHTML = "";
}

function addUser(ID, name) {
	var hiddenInput = document.createElement("input");
	hiddenInput.setAttribute("type", "hidden");
	hiddenInput.setAttribute("value", ID);
	hiddenInput.setAttribute("name", "msgReceiver[]");
	document.getElementById("addMessage").appendChild(hiddenInput);
	var output = document.createElement("li");
	output.innerHTML = name;
	document.getElementById("userSelected").appendChild(output);
	cleanSearchUser();
}

</script>

<style type='text/css'  media='all'>
fieldset {
	border: 1px solid #000000;
}
</style>

{/literal}

<h3>Neue Mitteilung erstellen:</h3>

<form id="addMessage"
	action='index.php?section=Messages|MAdmin&amp;action=saveMessage'
	method="post">
	<label>Titel:<input type="text" name="contracttitle" value=""></label><br /><br />
	<label>Text:<textarea class="ckeditor" name="contracttext"></textarea></label><br /><br />
	<label>G&uuml;ltig von: {if $date_str} {html_select_date prefix='StartDate' end_year="+1" time=$startdate_str}
		{else}{html_select_date prefix='StartDate' end_year="+1"}
		{/if}</label><br /><br />
	<label>G&uuml;ltig bis: {if $date_str} {html_select_date prefix='EndDate' end_year="+1" time=$enddate_str}
		{else}{html_select_date prefix='EndDate' end_year="+1"}
		{/if}</label><br /><br />
	<fieldset>
		<legend>An spezifische Benutzer senden:</legend>
		<input id="searchUserInp" type="text" name="searchUserInp"
			value="Benutzer suchen...">
		<input type="button" onclick="searchUser()" value="suchen">
		<div id="userSelection">
		</div>
		Einzelne Benutzer, an die die Email geschickt wird:
		<ul id="userSelected">
		</ul>
	</fieldset>
	<fieldset>
		<legend>Klasse:</legend>
		<select name="grades[]" size="5" multiple>
			{foreach item=grade from=$grades}
				<option value="{$grade.ID}">{$grade.name}</option>
			{/foreach}
		</select><br />
		<small>Mehrfachwahlen sind durch halten der Strg- (oder Ctrl-)Taste beim klicken möglich. Damit kann sich auch die angewählte Klasse wieder abwählen lassen.</small>
	</fieldset>
	<input id="submit" onclick="submit()" type="submit" value="Absenden" />
</form>
{include file='web/footer.tpl'}