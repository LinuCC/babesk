{extends file=$inh_path}{block name=content}

<h2 class="module-header">{t}Help for formatting the csv-file to update users{/t}</h2>

<p>

	{t}Use the semicolon (;) as a separator. Make sure the csv-file contains the following columns:{/t}
	<table class="dataTable">
		<tr>
			<td>forename</td>
			<td>{t}The forename of the user{/t}</td>
		</tr>
		<tr>
			<td>name</td>
			<td>{t}The lastname of the user{/t}</td>
		</tr>
		<tr>
			<td>grade</td>
			<td>{t}The grade the user should be switched to{/t}</td>
		</tr>
	</table>
	<br />
	<p>{t}Additionally, following columns can be added:{/t}</p>
	<table class="dataTable">
		<tr>
			<td>birthday</td>
			<td>
				{t}Neccessary if the birthdays of the users are already in the program!{/t}
				{t}The birthday of the user. Used to distinguish users with the same forename and name.{/t}
			</td>
		</tr>
		<tr>
			<td>telephone</td>
			<td>{t}The new telephone-number of the user. When this column exists, the old entries will be overridden, even if the new telephone-number is void!{/t}</td>
		</tr>
		<tr>
			<td>username</td>
			<td>{t}The new username of the user. When this column exists, the old entries will be overridden, even if the new username is void!{/t}</td>
		</tr>
	</table>
	<br />
	<p>{t}Example of a csv-file:{/t}</p>
	<p class="example-container">
		forename;name;grade;birthday;telephone;username<br />
		Hans;Mustermann;7b;12.3.1987;0573/12389;hans.mustermann<br />
		Peter;Müller;8k;27.9.1985;;peterchen<br />
	</p>
	<p>{t}Another example:{/t}</p>
	<p class="example-container">
		forename;name;grade<br />
		Hans;Mustermann;7b<br />
		Peter;Müller;8k<br />
	</p>
</p>

{/block}