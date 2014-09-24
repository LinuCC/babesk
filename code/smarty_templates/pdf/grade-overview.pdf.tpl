<html>

<h3>Klassenliste der Kurswahlen für Klasse {$gradeName}</h3>

{foreach $users as $userFullname => $categories}
	<b>{$userFullname}</b>
	<ul>
	{foreach $categories as $categoryName => $classes}
		<li>
		{$categoryName}
		<ul>
		{foreach $classes as $classId => $className}
			<li>{$className}</li>
		{/foreach}
		</ul>
		</li>
	{/foreach}
	</ul>
{/foreach}

</html>