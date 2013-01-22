{extends file=$inh_path} {block name="content"}

<h2 class="moduleHeader">Einen Schüler dem Kurs hinzufügen</h2>

<form action="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;addUser=true&amp;classId={$classId}" method="post">
	<label>zu suchender Benutzername:<input type="text" name="usernameInput"></label>
	<input type="submit" name="searchUser" value="Benutzer suchen"><br /><br />
	{if count($users)}
		{foreach $users as $user}
			<label>{$user.userFullname}<input type="radio" name="userSelected" value="{$user.userId}"></label><br />
		{/foreach}<br />
		<label>Das Verhältnis des Schülers zum Kurs<br>
		<select name="statusId">
			{foreach $statuses as $status}
				<option value="{$status.statusId}">{$status.translatedName}</option>
			{/foreach}
		</select><br />
		<input type="submit" name="assignUserToClass" value="Benutzer zuordnen"><br /><br />
	{/if}
</form>

{/block}