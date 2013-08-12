{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Hauptmen&uuml; der Kursverwaltung</h2>

<div>
	{if !$isClassRegistrationGloballyEnabled}
		<p>
			{_g('Classregistrations are not allowed')}
		</p>
	{else}
		<p>
			{_g('Classregistrations are allowed')}
		</p>
	{/if}
</div>

<fieldset class="smallContainer">
	<legend>
		{_g('Standard-Actions')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|AddClass">
					{_g('Add a Class')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|DisplayClasses">
					{_g('Display all Classes')}
			</a>
		</li>
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>
		{_g('More Actions')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|CsvImport">
					{_g('Import Classes with a CSV-File')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|CreateClassSummary">
					{_g('Create Class-Summaries')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|AssignUsersToClasses">
					{_g('Assign the Users to the Classes')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Kuwasys|Classes|ClassRegistration">
					{_g('Change if Classregistrations are enabled')}
			</a>
		</li>
	</ul>
</fieldset>



{/block}
