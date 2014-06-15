{extends file=$inh_path}
{block name='content'}

{if $authorGroup}
<p>
	Die Autorengruppe ist: <b>{$authorGroup.name}</b>
</p>
{else}
<p>
	Es wurde bisher keine Gruppe für Nachrichtenautoren zugewiesen.
</p>
{/if}

<form action="index.php?section=Messages|MessageAuthor&amp;action=changeAuthorGroup" method="POST">
<fieldset class="blockyField">
	<legend>
		Autorengruppe ändern:
	</legend>
	<label>Die neue Gruppe:
		<select name="group">
		{foreach $groups as $group}
			<option value="{$group.ID}">
				{$group.name}
			</option>
		{/foreach}
		</select>
	</label>
	<input type="submit" value="Gruppe ändern" />
</fieldset>
</form>
{/block}