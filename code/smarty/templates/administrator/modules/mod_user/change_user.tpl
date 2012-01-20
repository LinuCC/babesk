{literal}<!-- No Smarty-Variables wanted in JavaScript -->
<script type="text/javascript">

alert("SCHINKEN!");

function displayChangeCardId() {
	document.getElementById("cardnumber").disabled = false;
	document.getElementById("cardnumber").focus();
	document.getElementById("showccid").hidden = true;
	document.getElementById("hideccid").hidden = false;//show button to abandon changing cardID
	old_cardnumber = document.getElementById("cardnumber").value;
	document.getElementById("cardnumber").value = "";
}

function resetCardId() {
	document.getElementById("cardnumber").value = old_cardnumber;
	document.getElementById("hideccid").hidden = true;
	document.getElementById("showccid").hidden = false;//show button to change cardID
	document.getElementById("cardnumber").disabled = true;
	document.getElementById("cardiderror").hidden = true;
}

function checkCardId() {
	is_okay = document.getElementById("cardnumber").value.search(/[0-9]{10}/);
	if(is_okay != -1) {
		document.getElementById("cardiderror").hidden = true;
	}
	else {
		document.getElementById("cardiderror").hidden = false;
	}
}
</script>
{/literal}

<form action="index.php?section=user&action=4&ID={$user.ID}"
	method="post" onsubmit="submit()">
	<fieldset>
		<legend>Persönliche Daten</legend>
		<label>ID des Users:<input type="text" name="id" maxlength="10"
			width="10" value={$user.ID}>
		</label><br> <br> <label>Kartennummer des Users:<input id="cardnumber"
			type="text" name="cardnumber" maxlength="10" width="10"
			value={$cardnumber} onblur="checkCardId()" disabled>
			<button id="showccid" type="button" onclick="displayChangeCardId()">KartenID verändern</button>
			<button id="hideccid" type="button" onclick="resetCardId()" hidden>KartenID doch nicht verändern</button>
			<p id="cardiderror" class="error" hidden>Die KartenID ist nicht richtig eingegeben worden.</p>
		</label> <br> <br> <label>Vorname:<input type="text" name="forename"
			value="{$user.forename}" />
		</label><br> <br> <label>Name:<input type="text" name="name"
			value="{$user.name}" />
		</label><br> <br> <label>Benutzername:<input type="text"
			name="username" value="{$user.username}" />
		</label><br> <br> <label>Passwort ändern:<input type="password"
			name="passwd" />
		</label><br> <br> <label>Passwortänderung wiederholen:<input
			type="password" name="passwd_repeat" />
		</label><br> <br> Geburtstag : {html_select_date
		time="{$user.birthday}" start_year="-100"}<br> <br> <label>Konto
			sperren:<input type="checkbox" name="lockAccount" value="1" {if $user.locked}checked{/if}/>
		</label>
	</fieldset>
	<br>
	<fieldset>
		<legend>Identitätsinformationen</legend>
		<br> <br> <select name="gid"> {html_options values=$gid
			output=$g_names selected="{$user.GID}"}
		</select> <label>Guthaben:<input type="text" name="credits" size="5"
			maxlength="5" value="{$user.credit}" />
		</label>
	</fieldset>
	<br> <input id="submit" onclick="submit()" type="submit" value="Submit" />
</form>
