{extends file=$schbasSettingsParent}{block name=content}
<form action="index.php?section=Schbas|SchbasSettings&action=setReminder"	method="post">
		<label>Bitte Nachrichtenvorlage ausw&auml;hlen:
			<select id="templateID" name="templateID">
					{foreach from=$allSchbasMessage item=schbasMessage name=zaehler}				
					<option value="{$schbasMessage.ID}" {if $schbasMessage.ID == $activeReminderID}selected{/if}>{$schbasMessage.title}  {if $schbasMessage.ID == $activeReminderID}(aktiv){/if}</option>
					{/foreach}
			</select>
		</label><br/>
		<label>Autor der Mahnungen (User-ID):
			<input type=text name="authorID" value="{$reminderAuthorID}" size=2>
		</label>
		<br />
	<input id="submit"type="submit" value="Mahnungseinstellungen speichern" />
</form>
{/block}
