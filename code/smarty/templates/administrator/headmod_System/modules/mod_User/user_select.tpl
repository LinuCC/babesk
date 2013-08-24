{extends file=$UserParent}{block name=content}

<h2 class="moduleHeader">{_g('Usersettings-Mainmenu')}</h2>

<fieldset class="smallContainer">
	<legend>
		{_g('General')}
	</legend>
	<ul class="submodulelinkList" >
		<li>
			<a href="index.php?module=administrator|System|User|Register">
				{_g('Register a User')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|System|User|DisplayAll">
				{_g('Show Users')}
			</a>
		</li>
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>
		{_g('Bulk-Changes')}
	</legend>
	<ul class="submodulelinkList" >
		<li>
			<a href="index.php?module=administrator|System|User|CreateUsernames">
				{_g('Assign Usernames to User automatically')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|System|User|RemoveSpecialCharsFromUsernames">
				{_g('Remove Specialcharacters from Usernames')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|System|User|UserCsvImport">
				{_g('Import Users by a CSV-File')}
			</a>
		</li>
	</ul>
</fieldset>

{/block}
