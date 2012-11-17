<head>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
	<h2>Bitte wählen sie die zu installierende Komponente aus:</h2>
	{foreach $components as $component}
		<h3><a href='index.php?module={$component.name}'>{$component.nameDisplay}</a></h3>
	{/foreach}
</body>