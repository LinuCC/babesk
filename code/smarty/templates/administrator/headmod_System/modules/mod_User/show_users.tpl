{extends file=$UserParent}{block name=content}
{block name=search}
<form action="index.php?section=System|User&action=4" method="post"><input type='text' name='user_search'><input type='submit' value='Mit Benutzernamen oder Kartennummer suchen'></form>
{/block}
<table width=100%>
<tr><th align='center'>{$navbar}</th></tr>
</table>

<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<form name="filterID" action="index.php?section=System|User&action=2" method="post"><input type="hidden" name="filter" value="ID"><th align='center'><a href="#" onclick="document.filterID.submit();">ID</a></th></form>
			<form name="filterForename" action="index.php?section=System|User&action=2" method="post"><input type="hidden" name="filter" value="forename"><th align='center'><a href="#" onclick="document.filterForename.submit();">Vorname</a></th></form>
			<form name="filterName" action="index.php?section=System|User&action=2" method="post"><input type="hidden" name="filter" value="name"><th align='center'><a href="#" onclick="document.filterName.submit();">Name</a></th></form>
			<form name="filterUsername" action="index.php?section=System|User&action=2" method="post"><input type="hidden" name="filter" value="username"><th align='center'><a href="#" onclick="document.filterUsername.submit();">Benutzername</a></th></form>
			<form name="filterBirthday" action="index.php?section=System|User&action=2" method="post"><input type="hidden" name="filter" value="birthday"><th align='center'><a href="#" onclick="document.filterBirthday.submit();">Geburtstag</a></th></form>
			<form name="filterCredit" action="index.php?section=System|User&action=2" method="post"><input type="hidden" name="filter" value="credit"><th align='center'><a href="#" onclick="document.filterCredit.submit();">Geld</a></th></form>
			<th align='center'>Gruppe</th>
			<form name="filterClass" action="index.php?section=System|User&action=2" method="post"><input type="hidden" name="filter" value="class"><th align='center'><a href="#" onclick="document.filterClass.submit();">Klasse</a></th></form>
		</tr>
	</thead>
	<tbody>
		{foreach $users as $user}
		<tr bgcolor='#FFC33'>
			<td align="center">{$user.ID}</td>
			<td align="center">{$user.forename}</td>
			<td align="center">{$user.name}</td>
			<td align="center">{$user.username}</td>
			<td align="center">{$user.birthday}</td>
			<td align="center">{$user.credit}</td>
			<td align="center">{$user.groupname}</td>
			<td align="center">{$user.class}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=System|User&action=4&ID={$user.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=System|User&action=3&ID={$user.ID}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>



{/block}